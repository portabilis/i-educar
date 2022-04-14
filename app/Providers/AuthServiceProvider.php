<?php

namespace App\Providers;

use App\Extensions\LegacyUserProvider;
use App\Policies\ProcessPolicy;
use App\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register custom user providers.
     *
     * @return void
     */
    private function registerUserProviders()
    {
        Auth::provider('legacy', function ($app) {
            return new LegacyUserProvider($app->make(Hasher::class));
        });
    }

    /**
     * Register Gates for application.
     *
     * @return void
     */
    private function registerGates()
    {
        Gate::before(function (User $user) {
            if ($user->isAdmin()) {
                return true;
            }
        });

        Gate::define('view', ProcessPolicy::class . '@view');
        Gate::define('modify', ProcessPolicy::class . '@modify');
        Gate::define('remove', ProcessPolicy::class . '@remove');
    }

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        $this->registerGates();
        $this->registerUserProviders();
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
