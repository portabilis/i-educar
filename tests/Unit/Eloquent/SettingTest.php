<?php

namespace Tests\Unit\Eloquent;

use App\Setting;
use Tests\EloquentTestCase;

class SettingTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Setting::class;
    }
}
