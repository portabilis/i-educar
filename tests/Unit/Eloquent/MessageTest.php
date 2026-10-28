<?php

use App\Models\LegacyActiveLooking;
use App\Models\LegacyUser;
use App\Models\Message;
use Database\Factories\LegacyActiveLookingFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\MessageFactory;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(Message::class, relations: [
    'messageable' => LegacyActiveLooking::class,
    'user' => LegacyUser::class,
]));

EloquentModelTestCase::registerModelTests();

test('message belongs to active looking', function () {
    $activeLooking = LegacyActiveLookingFactory::new()->create();
    $message = MessageFactory::new()->forActiveLooking($activeLooking)->create();

    $this->assertInstanceOf(LegacyActiveLooking::class, $message->messageable);
    $this->assertEquals($activeLooking->id, $message->messageable->id);
});

test('message belongs to user', function () {
    $user = LegacyUserFactory::new()->create();
    $message = MessageFactory::new()->createdBy($user)->create();

    $this->assertInstanceOf(LegacyUser::class, $message->user);
    $this->assertEquals($user->id, $message->user->id);
});

test('message can have null user', function () {
    $message = MessageFactory::new()->withoutUser()->create();

    $this->assertNull($message->user);
});

test('soft deletes work', function () {
    $message = MessageFactory::new()->create();
    $messageId = $message->id;

    $message->delete();

    $this->assertDatabaseHas('messages', [
        'id' => $messageId,
        'deleted_at' => $message->fresh()->deleted_at,
    ]);
});

test('factory creates valid message', function () {
    $message = MessageFactory::new()->create();

    $this->assertInstanceOf(Message::class, $message);
    $this->assertNotNull($message->description);
    $this->assertNotNull($message->messageable_type);
    $this->assertNotNull($message->messageable_id);
    $this->assertNotNull($message->user_id);
});

test('factory without user creates valid message', function () {
    $message = MessageFactory::new()->withoutUser()->create();

    $this->assertInstanceOf(Message::class, $message);
    $this->assertNull($message->user_id);
    $this->assertNull($message->user);
});

test('factory short observation', function () {
    $message = MessageFactory::new()->shortObservation()->create();

    $this->assertInstanceOf(Message::class, $message);
    $this->assertLessThan(200, strlen($message->description));
});

test('factory long observation', function () {
    $message = MessageFactory::new()->longObservation()->create();

    $this->assertInstanceOf(Message::class, $message);
    $this->assertGreaterThan(200, strlen($message->description));
});
