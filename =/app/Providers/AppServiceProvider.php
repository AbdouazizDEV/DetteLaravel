<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\UploadService; // Import the UploadService class
use App\Services\Contracts\ClientServiceInterface;
use App\Services\Contracts\FileStorageServiceInterface;
use App\Services\Contracts\FileStorageServiceImpl;
use App\Repositories\Contracts\UserRepositoryInterface;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('uploadservice', function ($app) {
            return new UploadService();
        });
        $this->app->bind(
            ClientServiceInterface::class,
            \App\Services\ClientService::class
        );
    
        $this->app->bind(
            FileStorageServiceInterface::class,
            \App\Services\FileStorageService::class
        );
    
        $this->app->bind(
            \App\Repositories\Contracts\ClientRepositoryInterface::class,
            \App\Repositories\ClientRepository::class
        );
        $this->app->bind(FileStorageServiceInterface::class, \App\Services\FileStorageServiceImpl::class);
        $this->app->bind(UserRepositoryInterface::class, \App\Repositories\UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}