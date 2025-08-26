<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class PdfGenerationService
{
    public function generatePreInscriptionPdf(array $data, string $path): bool
    {
        try {
            Log::info("Génération fiche pré-inscription...");
            
            PDF::loadview('inscription.preinscription', $data)->save($path);
            
            Log::info("Fiche pré-inscription générée avec succès");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Erreur génération pré-inscription: " . $e->getMessage());
            return false;
        }
    }

    public function generateStudentCardPdf(array $data, string $path): bool
    {
        try {
            Log::info("Génération carte étudiant...");
            
            PDF::loadView('inscription.studentcard', $data)->save($path);
            
            Log::info("Carte étudiant générée avec succès");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Erreur génération carte étudiant: " . $e->getMessage());
            return false;
        }
    }
}
