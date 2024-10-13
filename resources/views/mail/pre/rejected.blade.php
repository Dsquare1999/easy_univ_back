<x-mail::message>
# Bonjour,

Votre demande de préinscription dans la filière {{ $filiere }} en cycle {{ $cycle }} a été rejetée pour la raison suivante. <br>

{{ $why }} 

<x-mail::button :url="''">
    Voir la notification
</x-mail::button>

Prière relancer l'inscription dès que les corrections sont effectuées

Cordialement,<br>

{{ config('app.name') }}
</x-mail::message>
