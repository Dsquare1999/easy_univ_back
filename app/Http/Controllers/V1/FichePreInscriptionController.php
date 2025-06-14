<?php

namespace App\Http\Controllers\V1;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

class FichePreInscriptionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($user, $student, $classe, $filiere, $cycle)
    {

        if (Storage::directoryMissing('inscriptions')){
            Storage::makeDirectory('inscriptions');
        }
        $filename = 'preinscription_' .str_replace(' ', '_', strtolower($cycle->name)). '_' . str_replace(' ', '_', strtolower($filiere->name)). '_' . str_replace(' ', '_', strtolower($user->firstname.' '.$user->lastname)) . '.pdf';
        $success = true;
        $error = null;
        $path = Storage::path('inscriptions') .DIRECTORY_SEPARATOR. $filename;
        try {
            PDF::loadview('inscription.preinscription', [
                'user' => $user,
                'student' => $student,
                'classe' => $classe,
                'filiere' => $filiere,
                'cycle' => $cycle
            ])
            ->save($path = Storage::path('inscriptions') .DIRECTORY_SEPARATOR. $filename);
            
        } catch (\Throwable $th) {
            $success = false;
            $filename = null;
            $error = $th->getMessage();
        }
        

        return array(
            'success' => $success,
            'filename' => $filename,
            'error' => $error,
            'path' => $path,
        );
    }
}