<x-mail::message>
# Bonjour

Nous avons bien reçu votre demande de préinscription dans la filière {{ $filiere }} en cycle {{ $cycle }} <br>

Actuellement en attente, l'administration vous fera un retour dans les plus brefs délais.


<x-mail::button :url="''">
    Voir la notification
</x-mail::button>


Cordialement,<br>

{{ config('app.name') }}
</x-mail::message>
