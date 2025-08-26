<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FilePathService
{
    public function ensureDirectoriesExist(): void
    {
        $directories = ['inscriptions', 'cards', 'qrcodes'];
        
        foreach ($directories as $directory) {
            if (Storage::directoryMissing($directory)) {
                Storage::makeDirectory($directory);
            }
        }
    }

    public function generateFilenames($user, $cycle, $filiere): array
    {
        $safeUserName = Str::slug($user->firstname . ' ' . $user->lastname, '_');
        $timestamp = now()->format('YmdHis');
        
        $baseFilename = "{$safeUserName}_{$timestamp}";
        $userFullName = str_replace(' ', '_', strtolower($user->firstname.' '.$user->lastname));
        $cycleName = str_replace(' ', '_', strtolower($cycle->name));
        $filiereName = str_replace(' ', '_', strtolower($filiere->name));

        return [
            'inscription_filename' => "preinscription_{$cycleName}_{$filiereName}_{$userFullName}.pdf",
            'card_filename' => "carte_d_etudiant_{$cycleName}_{$filiereName}_{$userFullName}.pdf",
            'qr_filename' => "qrcode_{$baseFilename}.png",
            'inscription_public_path' => Storage::url('inscriptions/' . $inscription_filename),
            'card_public_path' => Storage::url('cards/' . $card_filename),
        ];
    }
}
