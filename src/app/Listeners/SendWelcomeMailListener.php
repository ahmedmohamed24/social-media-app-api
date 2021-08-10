<?php

namespace App\Listeners;

use App\Jobs\SendMailJob;

class SendWelcomeMailListener
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
    public function handle($event)
    {
        \dispatch(new SendMailJob($event->email, $event->id));
    }
}
