<?php

use App\Models\LegacyUser;
use App\Models\Notification;
use App\Models\NotificationType;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(Notification::class, relations: [
    'type' => NotificationType::class,
    'user' => LegacyUser::class,
]));

EloquentModelTestCase::registerModelTests();

test('needs presigner url', function () {
    $expect = in_array($this->model->type_id, [
        NotificationType::EXPORT_STUDENT,
        NotificationType::EXPORT_TEACHER,
    ], true);
    $this->assertEquals($expect, $this->model->needsPresignerUrl());
});
