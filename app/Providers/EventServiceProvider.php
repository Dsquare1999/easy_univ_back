<?php


namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \App\Events\StudentCreated::class => [
            \App\Listeners\CreateStudentNotification::class,
            \App\Listeners\CreateDocumentNotification::class,
            \App\Listeners\CreateCycleNotification::class,
            \App\Listeners\UpdateCycleNotification::class,
            \App\Listeners\RemoveCycleNotification::class,
        ],
    ];

}