<?php

namespace App\Http\Controllers\V1;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

class ReleveNotesController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($cycle, $filiere, $classe, $matieres, $notes)
    {

        if (Storage::directoryMissing('releves')){
            Storage::makeDirectory('releves');
        }
        $filename = 'releve_' . str_replace(' ', '_', strtolower($cycle->name)) . '_' . str_replace(' ', '_', strtolower($filiere->name)) . '_' . str_replace(' ', '_', strtolower($classe->name)). '_' . now()->format('YmdHis') . '.pdf';
        $success = true;
        try {
            PDF::loadview('releves.semestre1', [
                'matieres' => $matieres,
                'notes' => $notes,
            ])->setPaper('a4', 'landscape')
            ->save($path = Storage::path('releves') .DIRECTORY_SEPARATOR. $filename);

            
        } catch (\Throwable $th) {
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