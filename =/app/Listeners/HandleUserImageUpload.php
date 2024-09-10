<?php

namespace App\Listeners;

use App\Events\ClientCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\UploadUserImageJob;

class HandleUserImageUpload
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
        $user = $event->client->user;

        if ($user && request()->hasFile('photo')) {
            $imagePath = request()->file('photo')->getRealPath();
            UploadUserImageJob::dispatch($user, $imagePath);
        }
        
    }
}
