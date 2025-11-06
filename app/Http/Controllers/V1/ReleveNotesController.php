<?php

namespace App\Http\Controllers\V1;

use App\Events\DocumentCreated;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Storage;

class ReleveNotesController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($cycle, $filiere, $classe, $unites, $notes, $meansPerMatiere, $year_part)
    {

        if (Storage::directoryMissing('releves')){
            Storage::makeDirectory('releves');
        }
        if (Storage::directoryMissing('bulletins')){
            Storage::makeDirectory('bulletins');
        }
        $filename = 'releve_' . str_replace(' ', '_', strtolower($cycle->name)) . '_' . str_replace(' ', '_', strtolower($filiere->name)) . '_' . str_replace(' ', '_', strtolower($classe->name)). '_semester_' . $year_part . '_' . now()->format('YmdHis') . '.pdf';
        $success = true;
        $filepath = Storage::disk('public')->path('releves/' . $filename);
        try {
            PDF::loadview('releves.semestre', [
                'cycle' => $cycle,
                'filiere' => $filiere,
                'classe' => $classe,
                'unites' => $unites,
                'notes' => $notes,
                'meansPerMatiere' => $meansPerMatiere,
                'year_part' => $year_part
            ])->setPaper('a4', 'landscape')
            ->save($path = $filepath);
        } catch (\Throwable $th) {
            $success = false;
            $error = $th->getMessage();
        }


        try {
            Log::info("Génération des bulletins pour le cycle: {$cycle->name}, filière: {$filiere->name}, classe: {$classe->name}");
            foreach ($notes as $note) {
                Log::info("Génération du bulletin. Unites : " . $unites);
                $bulletinName = 'bulletin_' . str_replace(' ', '_', strtolower($note['name'])) . '_' . str_replace(' ', '_', strtolower($cycle->name)) . '_' . str_replace(' ', '_', strtolower($filiere->name)) . '_' . str_replace(' ', '_', strtolower($classe->name)). '_semester_' . $year_part . '.pdf';
                $bulletinPath = Storage::disk('public')->path('bulletins/' . $bulletinName);

                $relativePath = 'bulletins/' . $bulletinName;
                $disk = Storage::disk('public');

                if ($disk->exists($relativePath)) {
                    $disk->delete($relativePath);
                    Log::info("Ancien bulletin supprimé : " . $relativePath);
                }

                PDF::loadview('releves.bulletin', [
                    'cycle' => $cycle,
                    'filiere' => $filiere,
                    'classe' => $classe,
                    'unites' => $unites,
                    'note' => $note,
                    'year_part' => $year_part
                ])
                ->save($path = $bulletinPath);

                $publicUrl = Storage::url('bulletins/' . $bulletinName);
                Log::info("Bulletin généré avec succès: " . $publicUrl);
                event(new DocumentCreated($note['student'], $note['user'], 'Bulletin Semestre ' . $year_part, $publicUrl, 'pdf', auth()->id()));
            }
        } catch (\Throwable $th) {
            Log::error("Erreur lors de la génération du bulletin: " . $th->getMessage());
            $success = false;
            $error = $th->getMessage();
        }

        return array(
            'success' => $success,
            'filename' => $filename,
            'error' => $success ? null : $error,
            'notes' => $notes
        );
    }
}