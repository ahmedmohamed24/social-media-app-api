<?php

namespace App\Providers;

use App\Events\UserRegisteredEvent;
use App\Listeners\AttachProfileListener;
use App\Listeners\SendWelcomeMailListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // Registered::class => [
        // SendEmailVerificationNotification::class,
        // ],

        UserRegisteredEvent::class => [
            AttachProfileListener::class,
            SendWelcomeMailListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot()
    {
    }
}
