<?php

namespace App\Providers;

use App\Extensions\LegacyUserProvider;
use App\Policies\ProcessPolicy;
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
     * @inheritdoc
     */
    public function register()
    {
        $this->app->singleton(User::class, function () {
            return Auth::user();
        });
    }
}
