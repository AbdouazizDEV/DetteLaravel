<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\UploadService; // Import the UploadService class
use App\Services\Contracts\ClientServiceInterface;
use App\Services\Contracts\FileStorageServiceInterface;
use App\Services\Contracts\FileStorageServiceImpl;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Models\Client;
use App\Observers\ClientObserver;
use App\Repositories\Contracts\DetteRepositoryInterface;
use App\Repositories\DetteRepository;
use App\Services\Contracts\DetteServiceInterface;
use App\Services\DetteService;
use App\Models\Dette;
use App\Observers\DetteObserver;
use App\Services\ClientService;
use App\Services\Contracts\DatabaseServiceInterface;
use App\Services\FirebaseService;
use App\Services\MongoDBService;


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

        $this->app->bind(DetteRepositoryInterface::class, DetteRepository::class);
        $this->app->bind(DetteServiceInterface::class, DetteService::class);
        //$this->app->bind(ClientServiceInterface::class, ClientService::class);
        
        $this->app->bind(DatabaseServiceInterface::class, function ($app) {
            // Utiliser DATABASE_DRIVER du fichier .env
            $driver = env('DATABASE_DRIVER', 'firebase');  // Utilise 'firebase' comme valeur par défaut

            if ($driver === 'firebase') {
                return new FirebaseService();
            } elseif ($driver === 'mongodb') {
                return new MongoDBService();
            }

            throw new \Exception("Unsupported database driver: $driver");
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Client::observe(ClientObserver::class);
        Dette::observe(DetteObserver::class);
    }
}