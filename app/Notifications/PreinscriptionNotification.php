<?php

namespace App\Notifications;

use App\Models\Classe;
use App\Models\Cycle;
use App\Models\Filiere;
use App\Models\Student;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use Illuminate\Support\Facades\Storage;
class PreinscriptionNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(Student $student, Classe $classe, Cycle $cycle, Filiere $filiere)
    {
        $this->student = $student;
        $this->classe = $classe;
        $this->cycle = $cycle;
        $this->filiere = $filiere;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Notification de Préinscription réussie')
                    ->markdown('mail.pre.inscription', [
                        'cycle' => $this->cycle->name,
                        'filiere' => $this->filiere->name, 
                    ])
                    ->attach(Storage::path('inscriptions') .DIRECTORY_SEPARATOR. $this->student->file, [
                        'as' => 'Fiche de préinscription.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'student' => $this->student,
            'classe' => $this->classe,
            'cycle' => $this->cycle,
            'filiere' => $this->filiere,
            'message' => "Votre inscription a été acceptée",
        ];
    }
}
