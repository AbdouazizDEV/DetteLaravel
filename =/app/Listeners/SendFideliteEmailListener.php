<?php

namespace App\Listeners;

use App\Events\ClientCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\SendFideliteEmailJob;

class SendFideliteEmailListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ClientCreated $event): void
    {
        // Dispatch le job pour envoyer l'email de fidélité
        if ($event->client->user) {
            SendFideliteEmailJob::dispatch($event->client);
        }
    }
}
