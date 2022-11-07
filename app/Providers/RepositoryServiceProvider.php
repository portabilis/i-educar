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
        $this->app->bind(\iEducar\Support\Repositories\StudentRepository::class, \App\Repositories\StudentRepositoryEloquent::class);
        $this->app->bind(\iEducar\Support\Repositories\ResponsavelRepository::class, \App\Repositories\ResponsavelRepositoryEloquent::class);
        $this->app->bind(\iEducar\Support\Repositories\ResponsavelTurmaRepository::class, \App\Repositories\ResponsavelTurmaRepositoryEloquent::class);
    }
}
