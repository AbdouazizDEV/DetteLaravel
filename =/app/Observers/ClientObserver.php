<?php

namespace App\Observers;

use App\Models\Client;
use App\Jobs\SendFideliteEmailJob;
use App\Events\ClientCreated;
class ClientObserver
{
    /**
     * Handle the Client "created" event.
     */
    public function created(Client $client)
    {
        ClientCreated::dispatch($client);
    }

    /**
     * Handle the Client "updated" event.
     */
    public function updated(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "deleted" event.
     */
    public function deleted(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "restored" event.
     */
    public function restored(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "force deleted" event.
     */
    public function forceDeleted(Client $client): void
    {
        //
    }
}
