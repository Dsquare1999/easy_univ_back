<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class QrCodeService
{
    public function generateQrCode(string $data, string $filename): ?string
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
}
