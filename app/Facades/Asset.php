<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Contracts\AssetServiceContract;

class Asset extends Facade
{
    /**
     * @inheritdoc
     */
    protected static function getFacadeAccessor()
    {
        return AssetServiceContract::class;
    }
}
