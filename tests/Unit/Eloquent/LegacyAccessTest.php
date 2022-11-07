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

    /** @test */
    public function getLastAccess()
    {
        $query = $this->model->getLastAccess();

        $this->assertNotNull($query);
        $this->assertInstanceOf(LegacyAccess::class, $query);
    }
}
