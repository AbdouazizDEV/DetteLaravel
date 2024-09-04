<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\AuthentificationServiceInterface;
use App\Services\PassportAuthentificationService;
use App\Services\SanctumAuthentificationService;

class AuthCustomProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AuthentificationServiceInterface::class, function ($app) {
            // Vous pouvez définir ici la logique pour choisir entre Passport et Sanctum
            // Pour l'exemple, nous utiliserons Sanctum
            return new SanctumAuthentificationService();
        });
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
