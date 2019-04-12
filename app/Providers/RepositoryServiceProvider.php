<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\iEducar\Support\Repositories\MenuRepository::class, \App\Repositories\MenuRepositoryEloquent::class);
        $this->app->bind(\iEducar\Support\Repositories\SubmenuRepository::class, \App\Repositories\SubmenuRepositoryEloquent::class);
        $this->app->bind(\iEducar\Support\Repositories\UserRepository::class, \App\Repositories\UserRepositoryEloquent::class);
        $this->app->bind(\iEducar\Support\Repositories\UserTypeRepository::class, \App\Repositories\UserTypeRepositoryEloquent::class);
        $this->app->bind(\iEducar\Support\Repositories\ConfigurationRepository::class, \App\Repositories\ConfigurationRepositoryEloquent::class);
        $this->app->bind(\iEducar\Support\Repositories\SystemMenuRepository::class, \App\Repositories\SystemMenuRepositoryEloquent::class);
        $this->app->bind(\iEducar\Support\Repositories\StudentRepository::class, \App\Repositories\StudentRepositoryEloquent::class);
    }
}
