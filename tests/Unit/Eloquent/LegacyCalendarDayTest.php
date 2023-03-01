<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyCalendarDay;
use App\Models\LegacyUser;
use Tests\EloquentTestCase;

class LegacyCalendarDayTest extends EloquentTestCase
{
    protected $relations = [
        'createdBy' => LegacyUser::class,
        'updatedBy' => LegacyUser::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyCalendarDay::class;
    }
}
