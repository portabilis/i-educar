<?php

namespace App\Facades;

use App\Contracts\AssetServiceContract;
use Illuminate\Support\Facades\Facade;

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
