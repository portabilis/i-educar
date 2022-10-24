<?php

namespace Tests\Unit\Eloquent;

use App\Models\ManagerLinkType;
use Tests\EloquentTestCase;

class ManagerLinkTypeTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return ManagerLinkType::class;
    }
}
