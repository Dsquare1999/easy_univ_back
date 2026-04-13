<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Classe;
use App\Models\User;
use App\Jobs\GenerateSingleBulletinJob;
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
            
            $classe = Classe::with('students')->findOrFail($this->classeId);
            $students = $classe->students;

            // Tri des étudiants par ordre alphabétique
            $students = $students->sortBy(function($student) {
                $user = User::find($student->user);
                return $user ? $user->lastname . ' ' . $user->firstname : '';
            });
            
            // Générer le QR Code une seule fois
            $qrCodePath = $this->generateQrCode("Document emis le ".now()->format('d/m/Y'), "Document QR Code");

            $studentCount = $students->count();
            Log::info("Dispatch de {$studentCount} jobs de génération de bulletins");
            
            // Dispatcher un Job par étudiant
            foreach ($students as $index => $student) {
                GenerateSingleBulletinJob::dispatch(
                    $student->id,
                    $this->classeId,
                    $this->yearPart,
                    $qrCodePath
                )->delay(now()->addSeconds($index * 2)); // Délai de 2 secondes entre chaque job
            }

            Log::info("Tous les jobs de génération de bulletins ont été dispatchés pour la classe: " . $this->classeId);
            
        } catch (\Exception $e) {
            Log::error("Erreur lors du dispatch des bulletins en arrière-plan: " . $e->getMessage());
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
