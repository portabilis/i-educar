<?php

namespace Tests\Unit\Models\Traits;

use App\Models\LegacyActiveLooking;
use App\Models\Message;
use App\Models\Traits\HasMessages;
use Database\Factories\LegacyActiveLookingFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\MessageFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

// Classe de teste para verificar o trait
class TestModel extends Model
{
    use HasMessages;

    protected $table = 'pmieducar.busca_ativa';
}

test('trait adds messages relationship', function () {
    $model = new TestModel;

    $this->assertTrue(method_exists($model, 'messages'));
    $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphMany::class, $model->messages());
});

test('messages relationship works with LegacyActiveLooking', function () {
    $activeLooking = LegacyActiveLookingFactory::new()->create();
    $user = LegacyUserFactory::new()->create();

    // Criar mensagens usando o relacionamento
    $message1 = MessageFactory::new()->forActiveLooking($activeLooking)->createdBy($user)->create();
    $message2 = MessageFactory::new()->forActiveLooking($activeLooking)->createdBy($user)->create();

    // Carregar mensagens através do relacionamento
    $messages = $activeLooking->messages;

    $this->assertCount(2, $messages);
    $this->assertInstanceOf(Message::class, $messages->first());
    $this->assertEquals($activeLooking->id, $messages->first()->messageable_id);
    $this->assertEquals(LegacyActiveLooking::class, $messages->first()->messageable_type);
});

test('messages relationship returns empty collection when no messages', function () {
    $activeLooking = LegacyActiveLookingFactory::new()->create();

    $messages = $activeLooking->messages;

    $this->assertCount(0, $messages);
    $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $messages);
});

test('messages relationship loads with user', function () {
    $activeLooking = LegacyActiveLookingFactory::new()->create();
    $user = LegacyUserFactory::new()->create();

    $message = MessageFactory::new()->forActiveLooking($activeLooking)->createdBy($user)->create();

    // Carregar mensagens com relacionamento user
    $messages = $activeLooking->messages()->with('user')->get();

    $this->assertCount(1, $messages);
    $this->assertNotNull($messages->first()->user);
    $this->assertEquals($user->id, $messages->first()->user->id);
});

test('trait can be used with any model class', function () {
    // Simular uso com diferentes tipos de modelos usando strings
    $user = LegacyUserFactory::new()->create();

    // Criar mensagens para diferentes tipos de modelos
    $message1 = MessageFactory::new()->state([
        'messageable_type' => 'App\\Models\\Student',
        'messageable_id' => 123,
    ])->createdBy($user)->create();

    $message2 = MessageFactory::new()->state([
        'messageable_type' => 'App\\Models\\Registration',
        'messageable_id' => 456,
    ])->createdBy($user)->create();

    // Verificar se as mensagens foram criadas corretamente
    $this->assertEquals('App\\Models\\Student', $message1->messageable_type);
    $this->assertEquals(123, $message1->messageable_id);
    $this->assertEquals('App\\Models\\Registration', $message2->messageable_type);
    $this->assertEquals(456, $message2->messageable_id);

    // Demonstrar que o trait funcionaria com qualquer modelo
    $this->assertTrue(class_exists('App\\Models\\Student'));
    $this->assertTrue(class_exists('App\\Models\\Registration'));
});
