<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyCalendarDay;
use App\Models\LegacyUser;
use Tests\EloquentTestCase;

class LegacyCalendarDayTest extends EloquentTestCase
{
    protected $relations = [
        'createdByUser' => LegacyUser::class,
        'deletedByUser' => LegacyUser::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyCalendarDay::class;
    }
}
