<?php

namespace App\Http\Controllers\V1;

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
    public function __invoke($cycle, $filiere, $classe, $unites, $notes, $meansPerMatiere)
    {

        if (Storage::directoryMissing('releves')){
            Storage::makeDirectory('releves');
        }
        if (Storage::directoryMissing('bulletins')){
            Storage::makeDirectory('bulletins');
        }
        $filename = 'releve_' . str_replace(' ', '_', strtolower($cycle->name)) . '_' . str_replace(' ', '_', strtolower($filiere->name)) . '_' . str_replace(' ', '_', strtolower($classe->name)). '_' . now()->format('YmdHis') . '.pdf';
        $success = true;
        try {
            PDF::loadview('releves.semestre', [
                'cycle' => $cycle,
                'filiere' => $filiere,
                'classe' => $classe,
                'unites' => $unites,
                'notes' => $notes,
                'meansPerMatiere' => $meansPerMatiere,
            ])->setPaper('a4', 'landscape')
            ->save($path = Storage::path('releves') .DIRECTORY_SEPARATOR. $filename);
        } catch (\Throwable $th) {
            $success = false;
            $error = $th->getMessage();
        }

        try {
            Log::info("Génération des bulletins pour le cycle: {$cycle->name}, filière: {$filiere->name}, classe: {$classe->name}");
            foreach ($notes as $note) {
                Log::info("Génération du bulletin. Unites : " . $unites);
                $bulletinName = 'bulletin_' . str_replace(' ', '_', strtolower($note['name'])) . '_' . str_replace(' ', '_', strtolower($cycle->name)) . '_' . str_replace(' ', '_', strtolower($filiere->name)) . '_' . str_replace(' ', '_', strtolower($classe->name)). '_' . now()->format('YmdHis') . '.pdf';
                PDF::loadview('releves.bulletin', [
                    'cycle' => $cycle,
                    'filiere' => $filiere,
                    'classe' => $classe,
                    'unites' => $unites,
                    'note' => $note
                ])->setPaper('a4', 'landscape')
                ->save($path = Storage::path('bulletins') .DIRECTORY_SEPARATOR. $bulletinName);
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