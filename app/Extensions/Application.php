<?php

namespace App\Extensions;

use EderSoares\Laravel\PlugAndPlay\Foundation\PlugAndPlayPackages;
use Illuminate\Foundation\Application as LaravelApplication;

class Application extends LaravelApplication
{
    use PlugAndPlayPackages;
}
