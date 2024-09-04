<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\Contracts\FileStorageServiceInterface;
use Illuminate\Http\Request;

class HandleClientResponse
{
    protected $fileStorageService;

    public function __construct(FileStorageServiceInterface $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->route()->named('clients.store')) {
            $client = $response->original;

            if ($client->user_id) {
                $userPhotoPath = $client->user->photo;
                $client->photo_base64 = $this->fileStorageService->getBase64($userPhotoPath);
            } else {
                $avatarPath = $client->avatar;
                $client->photo_base64 = $this->fileStorageService->getBase64($avatarPath);
            }
        }

        return $response;
    }
}
