<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyAccess;
use Tests\EloquentTestCase;

class LegacyAccessTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyAccess::class;
    }

    public function test_get_last_access()
    {
        $query = $this->model->getLastAccess();

        $this->assertNotNull($query);
        $this->assertInstanceOf(LegacyAccess::class, $query);
    }
}
