<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\ArticleRepositoryInterface;
use App\Repositories\ArticleRepository;
use App\Services\Contracts\ArticleServiceInterface;
use App\Services\ArticleService;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Repositories\ClientRepository;
use App\Services\Contracts\ClientServiceInterface;
use App\Services\ClientService;
use App\Services\Contracts\UploadServiceInterface;
use App\Services\UploadService;
use App\Repositories\Contracts\UploadRepositoryInterface;
use App\Repositories\UploadRepository;
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ArticleRepositoryInterface::class, ArticleRepository::class);
        $this->app->bind(ArticleServiceInterface::class, ArticleService::class);

        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(ClientServiceInterface::class, ClientService::class);
        
        $this->app->bind('clientService', ClientService::class);
        // $this->app->bind(UploadServiceInterface::class, UploadService::class);
        // $this->app->bind(UploadRepositoryInterface::class, UploadRepository::class);
    }
    
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
