<?php

use App\Models\Notification;
use App\Models\NotificationType;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(NotificationType::class, relations: [
    'notifications' => Notification::class,
]));

EloquentModelTestCase::registerModelTests();

test('types', function () {
    $this->assertEquals(1, NotificationType::TRANSFER);
    $this->assertEquals(2, NotificationType::OTHER);
    $this->assertEquals(3, NotificationType::EXPORT_STUDENT);
    $this->assertEquals(4, NotificationType::EXPORT_TEACHER);
});
