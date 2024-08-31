<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use App\Models\Client;
use App\Policies\ClientPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
   /*  protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ]; */
   /*  protected $policies = [
        Client::class => ClientPolicy::class,
    ]; */
    
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // $this->registerPolicies();

        //- Register Passport routes
        
        //Passport::routes();//

        //
    }
}
