<?php

namespace App\Listeners;

use App\Models\Document;

use App\Events\DocumentCreated;
use App\Services\NotifService;
use Illuminate\Support\Facades\Log;


class CreateDocumentNotification
{
    // (Optionnel) Active le queueing si tu veux l’exécuter en file d’attente :
    // implements ShouldQueue

    public function handle(DocumentCreated $event): void
    {
        $s = $event->student;

        Log::info("Création du document pour l'étudiant un étudiant");
        Document::create([
            'classe' => $s->classe,
            'student' => $s->id,
            'tag' => $s->tag, 
            'name' => $event->name,
            'type' => $event->type, // Type of document, e.g., '
            'path' => $event->path, // Assuming the Document model has a 'path' attribute
        ]);
        Log::info("Document créé avec succès pour l'étudiant: " . $event->user->firstname . ' ' . $event->user->lastname);

        NotifService::create(
            'document_created',
            'Nouvel document ajouté',
            "Le document {$event->name} a été ajouté concernant l'étudiant {$event->user->firstname} {$event->user->lastname} (matricule: " . ($event->user->matricule ?? 'N/A') . ").",
            $event->userId
        );
        Log::info("Notification de création de document envoyée pour l'étudiant: " . $event->user->firstname . ' ' . $event->user->lastname);
    }
}