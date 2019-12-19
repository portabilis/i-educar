<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;


class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     */
    public function boot()
    {
        Horizon::auth(function ($request) {
            if (!$request->user()) {
                return false;
            }

            if ($request->user()->isAdmin()) {
                return true;
            }

            return false;
        });
    }

    /**
     * Register the application services.
     *
     */
    public function register()
    {
        //
    }
}
