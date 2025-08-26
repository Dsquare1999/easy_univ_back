<?php

namespace App\Services;

use App\Models\Notif;

class NotifService
{
    public static function create(string $type, string $title, string $message, $userId = null)
    {
        return Notif::create([
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'user' => $userId,
        ]);
    }
}
