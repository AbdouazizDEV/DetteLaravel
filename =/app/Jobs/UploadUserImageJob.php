<?php

namespace App\Jobs;

use App\Models\User;
use Cloudinary\Cloudinary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadUserImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $user;
    public $imagePath;
    /**
     * Create a new job instance.
     */
    public function __construct(User $user, $imagePath)
    {
        $this->user = $user;
        $this->imagePath = $imagePath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $cloudinary = new Cloudinary();
            $upload = $cloudinary->uploadApi()->upload($this->imagePath);
            $this->user->photo = $upload['secure_url'];
        } catch (\Exception $e) {
            $base64Image = base64_encode(file_get_contents($this->imagePath));
            $this->user->photo = $base64Image;
        }

        $this->user->save();
    }
}
