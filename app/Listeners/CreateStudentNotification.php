<?php

namespace App\Listeners;

use App\Events\StudentCreated;
use App\Services\NotifService;

class CreateStudentNotification
{
    // (Optionnel) Active le queueing si tu veux l’exécuter en file d’attente :
    // implements ShouldQueue

    public function handle(StudentCreated $event): void
    {
        $s = $event->student;

        NotifService::create(
            'student_created',
            'Nouvel étudiant ajouté',
            "L'étudiant {$s->user->firstname} {$s->user->lastname} (matricule: " . ($s->user->matricule ?? 'N/A') . ") a été ajouté.",
            $event->userId
        );
    }
}
