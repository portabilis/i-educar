<?php

namespace App\Providers;

use App\Extensions\LegacyUserProvider;
use App\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::provider('legacy', function ($app) {
            return new LegacyUserProvider($app->make(Hasher::class));
        });
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->app->singleton(User::class, function () {
            return Auth::user();
        });
    }
}
