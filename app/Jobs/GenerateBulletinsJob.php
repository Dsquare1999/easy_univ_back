<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Classe;
use App\Models\Cycle;
use App\Models\Filiere;
use App\Models\Matiere;
use App\Models\Releve;
use App\Models\Unite;
use App\Models\User;
use App\Http\Controllers\V1\ReleveNotesController;
use Illuminate\Support\Facades\Log;

class GenerateBulletinsJob implements ShouldQueue
{
    use Queueable;

    protected $classeId;
    protected $yearPart;
    protected $reportType;

    /**
     * Create a new job instance.
     */
    public function __construct($classeId, $yearPart, $reportType)
    {
        $this->classeId = $classeId;
        $this->yearPart = $yearPart;
        $this->reportType = $reportType;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("Début de la génération des bulletins pour la classe: " . $this->classeId);
            
            $notes = [];
            $classe = Classe::with(['matieres.unite'])->findOrFail($this->classeId);
            $matieres = $classe->matieres;
            $matieres = $matieres->where('year_part', $this->yearPart);
            
            $uniteIds = $matieres->pluck('unite')->filter()->unique()->values()->all();

            $unites = Unite::with(['matieres' => function($q) use ($matieres) {
                $q->whereIn('id', $matieres->pluck('id'))->orderBy('libelle', 'asc');
            }])->whereIn('id', $uniteIds)->orderBy('code', 'asc')->get();

            $students = $classe->students;

            $students = $students->sortBy(function($student) {
                $user = User::find($student->user);
                return $user ? $user->lastname . ' ' . $user->firstname : '';
            });
            
            $qrCodePath = $this->generateQrCode("Document emis le ".now()->format('d/m/Y'), "Document QR Code");

            $releves = Releve::with(['matiere.unite', 'student.user'])->where('classe', $this->classeId)->get();
            
            foreach ($students as $student) {
                $user = User::find($student->user);
                if (!$user) {
                    continue;
                }

                $note = [];
                $note['name'] = $user->lastname . ' ' . $user->firstname;
                $note['user'] = $user;
                $note['student'] = $student;
                $note['qrCodePath'] = $qrCodePath;
                $note['notes'] = [];

                $somme_notes = 0;
                $somme_coeffs = 0;

                foreach ($releves as $releve) {
                    if ($releve->student == $student->id) {
                        $moyenne = ((($releve->exam1 + $releve->exam2) / 2) * 0.4) + ($releve->partial * 0.6);
                        if ($releve->remedial) {
                            $moyenne = $releve->remedial;
                        }
                        $moyenne = round($moyenne, 2);
                        $matiere = Matiere::find($releve->matiere);
                        if (!$matiere) {
                            continue;
                        }
                        $note['notes'][$matiere->code] = $moyenne;

                        $somme_notes += $moyenne * $matiere->coefficient;
                        $somme_coeffs += $matiere->coefficient;
                    }
                }

                $note['moyenne'] = $somme_coeffs > 0 ? round($somme_notes / $somme_coeffs, 2) : null;
                $note['cote'] = $this->getCote($note['moyenne']);
                $notes[] = $note;
            }
            
            $meansPerMatiere = $this->generalMeanPerMatiere($notes, $matieres);

            $cycle   = Cycle::findOrFail($classe->cycle);
            $filiere = Filiere::findOrFail($classe->filiere);

            $relevesNotesController = new ReleveNotesController();
            $pdfresponse = $relevesNotesController($cycle, $filiere, $classe, $unites, $notes, $meansPerMatiere, $this->yearPart, $qrCodePath, $this->reportType);

            Log::info("Bulletins générés avec succès pour la classe: " . $this->classeId);
            
        } catch (\Exception $e) {
            Log::error("Erreur lors de la génération des bulletins en arrière-plan: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
        }
    }

    private function getCote($moyenne)
    {
        if (is_null($moyenne)) return '';
        if ($moyenne >= 16) return 'A';
        if ($moyenne >= 14) return 'B';
        if ($moyenne >= 12) return 'C';
        if ($moyenne >= 10) return 'D';
        return 'E';
    }

    private function generalMeanPerMatiere($notes, $matieres)
    {
        $meansPerMatiere = [];
        foreach ($matieres as $matiere) {
            $somme = 0;
            $count = 0;
            foreach ($notes as $note) {
                if (isset($note['notes'][$matiere->code])) {
                    $somme += $note['notes'][$matiere->code];
                    $count++;
                }
            }
            $meansPerMatiere[$matiere->code] = $count > 0 ? round($somme / $count, 2) : null;
        }
        return $meansPerMatiere;
    }

    private function generateQrCode(string $data, string $filename): ?string
    {
        try {
            $result = \Endroid\QrCode\Builder\Builder::create()
                ->writer(new \Endroid\QrCode\Writer\PngWriter())
                ->data($data)
                ->size(150)
                ->margin(10)
                ->build();

            $qrCodePngData = $result->getString();
            $qrCodeStoragePath = 'qrcodes/' . $filename;

            \Illuminate\Support\Facades\Storage::disk('public')->put($qrCodeStoragePath, $qrCodePngData);
            $absolutePath = \Illuminate\Support\Facades\Storage::disk('public')->path($qrCodeStoragePath);

            return $absolutePath;
        } catch (\Exception $e) {
            Log::error("Erreur génération QR Code: " . $e->getMessage());
            return null;
        }
    }
}
