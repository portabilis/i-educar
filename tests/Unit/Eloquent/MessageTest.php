<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyActiveLooking;
use App\Models\LegacyUser;
use App\Models\Message;
use Database\Factories\LegacyActiveLookingFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\MessageFactory;
use Tests\EloquentTestCase;

class MessageTest extends EloquentTestCase
{
    protected $relations = [
        'messageable' => LegacyActiveLooking::class,
        'user' => LegacyUser::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Message::class;
    }

    public function test_message_belongs_to_active_looking()
    {
        $activeLooking = LegacyActiveLookingFactory::new()->create();
        $message = MessageFactory::new()->forActiveLooking($activeLooking)->create();

        $this->assertInstanceOf(LegacyActiveLooking::class, $message->messageable);
        $this->assertEquals($activeLooking->id, $message->messageable->id);
    }

    public function test_message_belongs_to_user()
    {
        $user = LegacyUserFactory::new()->create();
        $message = MessageFactory::new()->createdBy($user)->create();

        $this->assertInstanceOf(LegacyUser::class, $message->user);
        $this->assertEquals($user->id, $message->user->id);
    }

    public function test_message_can_have_null_user()
    {
        $message = MessageFactory::new()->withoutUser()->create();

        $this->assertNull($message->user);
    }

    public function test_soft_deletes_work()
    {
        $message = MessageFactory::new()->create();
        $messageId = $message->id;

        $message->delete();

        $this->assertDatabaseHas('messages', [
            'id' => $messageId,
            'deleted_at' => $message->fresh()->deleted_at,
        ]);
    }

    public function test_factory_creates_valid_message()
    {
        $message = MessageFactory::new()->create();

        $this->assertInstanceOf(Message::class, $message);
        $this->assertNotNull($message->description);
        $this->assertNotNull($message->messageable_type);
        $this->assertNotNull($message->messageable_id);
        $this->assertNotNull($message->user_id);
    }

    public function test_factory_without_user_creates_valid_message()
    {
        $message = MessageFactory::new()->withoutUser()->create();

        $this->assertInstanceOf(Message::class, $message);
        $this->assertNull($message->user_id);
        $this->assertNull($message->user);
    }

    public function test_factory_short_observation()
    {
        $message = MessageFactory::new()->shortObservation()->create();

        $this->assertInstanceOf(Message::class, $message);
        $this->assertLessThan(200, strlen($message->description));
    }

    public function test_factory_long_observation()
    {
        $message = MessageFactory::new()->longObservation()->create();

        $this->assertInstanceOf(Message::class, $message);
        $this->assertGreaterThan(200, strlen($message->description));
    }
}
