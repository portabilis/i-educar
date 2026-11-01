<?php

use App\Events\NotificationEvent;
use Database\Factories\NotificationFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('as tags da notificação têm a base, a categoria e o tipo', function () {
    $notification = NotificationFactory::new()->create();

    expect((new NotificationEvent($notification, '/link', 'cliente'))->tags())
        ->toBe(['cliente', 'notification', $notification->type->name]);
});
