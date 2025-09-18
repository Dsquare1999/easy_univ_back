<?php

namespace App\Http\Controllers\V1;

use App\Models\Document;

use App\Events\DocumentCreated;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class FichePreInscriptionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($user, $student, $classe, $filiere, $cycle, $tag = null)
    {
        Log::info("Début génération PDF pour utilisateur: " . $user->id);

        try {
            // Préparation des répertoires
            $this->ensureDirectoriesExist();
            Log::info("Directories vérifiés/créés");
            
            // Génération des noms de fichiers
            $fileInfo = $this->generateFilenames($user, $cycle, $filiere);
            Log::info("Noms de fichiers générés");

            // $inscriptionPath = $fileInfo['inscription_public_path'] . $fileInfo['inscription_filename'];
            $inscriptionPath = $fileInfo['inscription_public_path'];
            Log::info("Inscription path: " . $inscriptionPath);
            $cardPath = $fileInfo['card_public_path'];
            Log::info("Card path: " . $cardPath);

            Log::info("Chemins générés - Inscription: {$inscriptionPath} | Carte: {$cardPath}");

            // Génération du QR Code
            $qrCodePath = $this->generateQrCode($user->email, $fileInfo['qr_filename']);

            // Préparation des données pour les vues
            $viewData = [
                'user' => $user,
                'student' => $student,
                'classe' => $classe,
                'filiere' => $filiere,
                'cycle' => $cycle,
                'tag' => $tag,
                'qrCodeDataUri' => $qrCodePath
            ];

            Log::info("Données préparées pour les vues");
            $preInscriptionSuccess = $this->generatePreInscriptionPdf($viewData, $fileInfo['inscription_path']);
            $cardSuccess = $this->generateStudentCardPdf($viewData, $fileInfo['card_path']);


            if (!$preInscriptionSuccess || !$cardSuccess) {
                throw new \Exception("Échec de génération d'un ou plusieurs documents");
            }
            Log::info("Génération PDF terminée avec succès");

            event(new DocumentCreated($student, $user, 'Fiche de pré-inscription', $inscriptionPath, 'pdf', auth()->id()));
            event(new DocumentCreated($student, $user, 'Carte d\'étudiant', $cardPath, 'pdf', auth()->id()));

            Log::info("Événements de création de document déclenchés pour l'étudiant: " . $user->firstname . ' ' . $user->lastname);
            return [
                'success' => true,
                'filename' => $fileInfo['inscription_filename'],
                'cardname' => $fileInfo['card_filename'],
                'error' => null,
                'path' => $fileInfo['inscription_public_path'], // Chemin public
                'cardpath' => $fileInfo['card_public_path'],     // Chemin public
            ]; 

        } catch (\Throwable $th) {
            Log::error("Échec de la génération de PDF pour l'utilisateur {$user->id}: " . $th->getMessage());
            Log::error("Stack trace: " . $th->getTraceAsString());
            
            return [
                'success' => false,
                'filename' => null,
                'cardname' => null,
                'error' => "Erreur interne lors de la génération des documents. Détail : " . $th->getMessage(),
                'path' => null,
                'cardpath' => null,
            ];
        }
    }

    private function ensureDirectoriesExist(): void
    {
        $directories = ['inscriptions', 'cards', 'qrcodes'];
        
        foreach ($directories as $directory) {
            if (Storage::directoryMissing($directory)) {
                Storage::makeDirectory($directory);
            }
        }
    }

    private function generateFilenames($user, $cycle, $filiere): array
    {
        $safeUserName = Str::slug($user->firstname . ' ' . $user->lastname, '_');
        $timestamp = now()->format('YmdHis');
        
        $baseFilename = "{$safeUserName}_{$timestamp}";
        $userFullName = str_replace(' ', '_', strtolower($user->firstname.' '.$user->lastname));
        $cycleName = str_replace(' ', '_', strtolower($cycle->name));
        $filiereName = str_replace(' ', '_', strtolower($filiere->name));

        $inscriptionFilename = "preinscription_{$cycleName}_{$filiereName}_{$userFullName}.pdf";
        $cardFilename = "carte_d_etudiant_{$cycleName}_{$filiereName}_{$userFullName}.pdf";

        // Chemins physiques pour la sauvegarde des fichiers
        $inscriptionPath = Storage::disk('public')->path('inscriptions/' . $inscriptionFilename);
        $cardPath = Storage::disk('public')->path('cards/' . $cardFilename);

        // URLs publiques pour l'accès via le frontend
        $inscriptionUrl = Storage::disk('public')->url('inscriptions/' . $inscriptionFilename);
        $cardUrl = Storage::disk('public')->url('cards/' . $cardFilename);

        Log::info("Fichiers paths: {$inscriptionUrl}, {$cardUrl}");

        return [
            'inscription_filename' => $inscriptionFilename,
            'card_filename' => $cardFilename,
            'qr_filename' => "qrcode_{$baseFilename}.png",
            // Chemins physiques pour la sauvegarde
            'inscription_path' => $inscriptionPath,
            'card_path' => $cardPath,
            // URLs pour l'accès frontend
            'inscription_public_path' => $inscriptionUrl,
            'card_public_path' => $cardUrl,
        ];
    }

    private function generateQrCode(string $data, string $filename): ?string
    {
        try {
            Log::info("Génération QR Code pour: " . $data);

            $qrData = urlencode($data);
            $apiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={$qrData}";
            $qrCodePngData = @file_get_contents($apiUrl);

            if ($qrCodePngData === false) {
                throw new \Exception("Impossible de contacter l'API du QR Code");
            }

            $qrCodeStoragePath = 'qrcodes/' . $filename;
            Storage::disk('public')->put($qrCodeStoragePath, $qrCodePngData);
            
            $absolutePath = Storage::disk('public')->path($qrCodeStoragePath);
            Log::info("QR Code généré avec succès: " . $absolutePath);
            
            return $absolutePath;

        } catch (\Exception $e) {
            Log::error("Erreur génération QR Code: " . $e->getMessage());
            return null;
        }
    }

    private function generatePreInscriptionPdf(array $data, string $path): bool
    {
        try {
            Log::info("Génération fiche pré-inscription...");
            Log::info("Data: " . json_encode($data));
            Log::info("Path: " . json_encode($path));
            PDF::loadview('inscription.preinscription', $data)->save($path);
            
            Log::info("Fiche pré-inscription générée avec succès");
            return true; 
            
        } catch (\Exception $e) {
            Log::error("Erreur génération pré-inscription: " . $e->getMessage());
            return false;
        }
    }

    private function generateStudentCardPdf(array $data, string $path): bool
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