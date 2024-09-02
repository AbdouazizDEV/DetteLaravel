<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use App\Models\Client;
use App\Models\User;
use App\Policies\ClientPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;

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
    protected $policies = [
        User::class => UserPolicy::class,
    ];
    
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // $this->registerPolicies();

        //- Register Passport routes
        
        //Passport::routes();//

        $this->registerPolicies();

        Gate::define('admin', [UserPolicy::class, 'isAdmin']);
        Gate::define('boutiquier', [UserPolicy::class, 'isBoutiquier']);
        Gate::define('client', [UserPolicy::class, 'isClient']);
    }
}
