<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEducationNetwork;
use Tests\EloquentTestCase;

class LegacyEducationNetworkTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyEducationNetwork::class;
    }
}
