<x-mail::message>
# Bonjour,

Votre demande de préinscription dans la filière {{ $filiere }} en cycle {{ $cycle }} a été approuvée. <br>

Veuillez trouver ci-joint votre fiche de préinscription, et procéder à votre inscription


<x-mail::button :url="''">
    Voir la notification
</x-mail::button>

Nous vous remercions pour votre confiance.

Cordialement,<br>

{{ config('app.name') }}
</x-mail::message>
