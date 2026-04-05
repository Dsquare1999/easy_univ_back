<?php

namespace App\Http\Controllers\V1;

use App\Events\DocumentCreated;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RelevePVController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($cycle, $filiere, $classe, $unites, $notes, $meansPerMatiere, $year_part, $qrCodePath)
    {

        if (Storage::directoryMissing('pv')){
            Storage::makeDirectory('pv');
        }
        $cycleName   = Str::slug($cycle->name, '_');
        $filiereName = Str::slug($filiere->name, '_');
        $success = true;
        $error = null;

        $filename = 'pv_' . $cycleName . '_' . $filiereName . '_semester_' . $classe->academic_year . '_' . $year_part . '.pdf';
        $relativePath = "pv/{$filename}";
        $filepath = Storage::disk('public')->path('pv/' . $filename);
        $disk = Storage::disk('public');
        Log::info("Filename: " . $filename);
        Log::info("Filepath: " . $filepath);

        if ($disk->exists($relativePath)) {
            $disk->delete($relativePath);
        }

        try {
            Log::info("Avant génération du PDF PV");
            
            $pdf = PDF::loadview('releves.pv', [
                'cycle' => $cycle,
                'filiere' => $filiere,
                'classe' => $classe,
                'unites' => $unites,
                'notes' => $notes,
                'meansPerMatiere' => $meansPerMatiere,
                'year_part' => $year_part,
                'qrCodePath' => $qrCodePath
            ]);
            
            Log::info("Vue chargée avec succès");
            
            $pdf->setPaper('a4', 'landscape');
            
            Log::info("Format papier défini");
            
            $pdf->save($filepath);
            
            Log::info("PDF sauvegardé avec succès: " . $filepath);
            
        } catch (\Throwable $th) {
            $success = false;
            $error = $th->getMessage();
            Log::error("Erreur lors de la génération du PDF PV: " . $error);
            Log::error("Stack trace: " . $th->getTraceAsString());
        }

        return array(
            'success' => $success,
            'filename' => $filename,
            'error' => $success ? null : $error,
            'notes' => $notes
        );
    }
}