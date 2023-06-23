<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyUser;
use App\Models\Notification;
use App\Models\NotificationType;
use Tests\EloquentTestCase;

class NotificationTest extends EloquentTestCase
{
    protected $relations = [
        'type' => NotificationType::class,
        'user' => LegacyUser::class,
    ];

    protected function getEloquentModelName(): string
    {
        return Notification::class;
    }

    public function testNeedsPresignerUrl(): void
    {
        $expect = in_array($this->model->type_id, [
            NotificationType::EXPORT_STUDENT,
            NotificationType::EXPORT_TEACHER,
        ], true);
        $this->assertEquals($expect, $this->model->needsPresignerUrl());
    }
}
