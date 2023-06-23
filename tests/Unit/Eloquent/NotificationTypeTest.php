<?php

namespace Tests\Unit\Eloquent;

use App\Models\Notification;
use App\Models\NotificationType;
use Tests\EloquentTestCase;

class NotificationTypeTest extends EloquentTestCase
{
    protected $relations = [
        'notifications' => Notification::class,
    ];

    protected function getEloquentModelName(): string
    {
        return NotificationType::class;
    }

    public function testTypes()
    {
        $this->assertEquals(1, NotificationType::TRANSFER);
        $this->assertEquals(2, NotificationType::OTHER);
        $this->assertEquals(3, NotificationType::EXPORT_STUDENT);
        $this->assertEquals(4, NotificationType::EXPORT_TEACHER);
    }
}
