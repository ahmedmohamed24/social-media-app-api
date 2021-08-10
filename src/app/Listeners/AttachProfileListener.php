<?php

namespace App\Listeners;

use App\Events\UserRegisteredEvent;
use App\Models\Profile;

class AttachProfileListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param object $event
     */
    public function handle(UserRegisteredEvent $event)
    {
        Profile::create(['user_id' => $event->id]);
    }
}
