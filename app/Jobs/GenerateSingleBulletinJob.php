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
use App\Models\Student;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Events\DocumentCreated;

class GenerateSingleBulletinJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 300;
    public $tries = 3;

    protected $studentId;
    protected $classeId;
    protected $yearPart;
    protected $qrCodePath;

    /**
     * Create a new job instance.
     */
    public function __construct($studentId, $classeId, $yearPart, $qrCodePath)
    {
        $this->studentId = $studentId;
        $this->classeId = $classeId;
        $this->yearPart = $yearPart;
        $this->qrCodePath = $qrCodePath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $student = Student::findOrFail($this->studentId);
            $user = User::findOrFail($student->user);
            
            Log::info("Génération du bulletin pour: {$user->lastname} {$user->firstname}");
            
            $classe = Classe::with(['matieres.unite'])->findOrFail($this->classeId);
            $matieres = $classe->matieres->where('year_part', $this->yearPart);
            
            $uniteIds = $matieres->pluck('unite')->filter()->unique()->values()->all();
            $unites = Unite::with(['matieres' => function($q) use ($matieres) {
                $q->whereIn('id', $matieres->pluck('id'))->orderBy('libelle', 'asc');
            }])->whereIn('id', $uniteIds)->orderBy('code', 'asc')->get();

            $cycle = Cycle::findOrFail($classe->cycle);
            $filiere = Filiere::findOrFail($classe->filiere);

            // Calculer les notes de l'étudiant
            $note = [];
            $note['name'] = $user->lastname . ' ' . $user->firstname;
            $note['user'] = $user;
            $note['student'] = $student;
            $note['qrCodePath'] = $this->qrCodePath;
            $note['notes'] = [];

            $somme_notes = 0;
            $somme_coeffs = 0;

            $releves = Releve::where('classe', $this->classeId)
                ->where('student', $student->id)
                ->get();

            foreach ($releves as $releve) {
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
                $note['notes'][$matiere->code.'_exam1'] = $releve->exam1;
                $note['notes'][$matiere->code.'_exam2'] = $releve->exam2;
                $note['notes'][$matiere->code.'_partial'] = $releve->partial;

                $somme_notes += $moyenne * $matiere->coefficient;
                $somme_coeffs += $matiere->coefficient;
            }

            $note['moyenne'] = $somme_coeffs > 0 ? round($somme_notes / $somme_coeffs, 2) : null;
            $note['cote'] = $this->getCote($note['moyenne']);

            // Générer le PDF
            $filename = 'bulletin_' . strtolower(str_replace(' ', '_', $cycle->name)) . '_' 
                      . strtolower(str_replace(' ', '_', $filiere->name)) . '_' 
                      . strtolower(str_replace(' ', '_', $user->lastname)) . '_'
                      . strtolower(str_replace(' ', '_', $user->firstname)) . '_'
                      . 'semester_' . $classe->year . '_' . $this->yearPart . '_'
                      . now()->format('YmdHis') . '.pdf';

            $filepath = storage_path('app/public/bulletins/' . $filename);
            
            if (Storage::directoryMissing('bulletins')) {
                Storage::makeDirectory('bulletins');
            }

            $pdf = PDF::loadview('releves.bulletin', [
                'cycle' => $cycle,
                'filiere' => $filiere,
                'classe' => $classe,
                'unites' => $unites,
                'note' => $note,
                'year_part' => $this->yearPart,
                'qrCodePath' => $this->qrCodePath
            ]);

            $pdf->save($filepath);

            // Déclencher l'événement
            $publicPath = 'bulletins/' . $filename;
            event(new DocumentCreated($student, $user, 'Bulletin', $publicPath, 'pdf', null));

            Log::info("Bulletin généré avec succès pour: {$user->lastname} {$user->firstname}");
            
            // Libérer la mémoire
            unset($pdf, $note, $releves, $matieres, $unites);
            gc_collect_cycles();
            
        } catch (\Exception $e) {
            Log::error("Erreur génération bulletin pour student {$this->studentId}: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            throw $e;
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
}
