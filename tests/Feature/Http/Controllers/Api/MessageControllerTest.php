<?php

use App\Models\LegacyActiveLooking;
use Database\Factories\LegacyActiveLookingFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Database\Factories\MessageFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(DatabaseTransactions::class, TestCase::class);

test('index messages successfully', function () {
    $activeLooking = LegacyActiveLookingFactory::new()->create();
    $user = LegacyUserFactory::new()->create();
    MessageFactory::new()->forActiveLooking($activeLooking)->createdBy($user)->create();
    MessageFactory::new()->forActiveLooking($activeLooking)->createdBy($user)->create();

    $response = $this->actingAs($user)->getJson('/api/messages?messageable_type=' . LegacyActiveLooking::class . '&messageable_id=' . $activeLooking->id);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'description',
                    'created_at',
                    'updated_at',
                    'user' => [
                        'id',
                        'name',
                    ],
                    'can_edit',
                    'can_delete',
                ],
            ],
        ]);

    $this->assertCount(2, $response->json('data'));
});

test('index messages with invalid data', function () {
    $user = LegacyUserFactory::new()->create();

    $response = $this->actingAs($user)->getJson('/api/messages');

    $response->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'messageable_type',
                'messageable_id',
            ],
        ]);
});

test('store message successfully', function () {
    $activeLooking = LegacyActiveLookingFactory::new()->create();
    $user = LegacyUserFactory::new()->create();
    $messageText = 'Test message via API';

    $response = $this->actingAs($user)->postJson('/api/messages', [
        'messageable_type' => LegacyActiveLooking::class,
        'messageable_id' => $activeLooking->id,
        'description' => $messageText,
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'description',
                'created_at',
                'updated_at',
                'user' => [
                    'id',
                    'name',
                ],
                'can_edit',
                'can_delete',
            ],
            'message',
        ]);

    $this->assertDatabaseHas('messages', [
        'messageable_type' => LegacyActiveLooking::class,
        'messageable_id' => $activeLooking->id,
        'description' => $messageText,
        'user_id' => $user->id,
    ]);
});

test('store message with invalid data', function () {
    $user = LegacyUserFactory::new()->admin()->create();

    $response = $this->actingAs($user)->postJson('/api/messages', [
        'messageable_type' => LegacyActiveLooking::class,
        'messageable_id' => 1,
        'description' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'description',
            ],
        ]);
});

test('store message with observation too long', function () {
    $user = LegacyUserFactory::new()->admin()->create();
    $observation = str_repeat('a', 901);

    $response = $this->actingAs($user)->postJson('/api/messages', [
        'messageable_type' => LegacyActiveLooking::class,
        'messageable_id' => 1,
        'description' => $observation,
    ]);

    $response->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'description',
            ],
        ]);
});

test('show message successfully', function () {
    $user = LegacyUserFactory::new()->create();
    $message = MessageFactory::new()->createdBy($user)->create();

    $response = $this->actingAs($user)->getJson("/api/messages/{$message->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $message->id,
                'description' => $message->description,
            ],
        ]);
});

test('show message not found', function () {
    $user = LegacyUserFactory::new()->create();

    $response = $this->actingAs($user)->getJson('/api/messages/99999');

    $response->assertStatus(404)
        ->assertJson([
            'message' => 'Mensagem não encontrada',
        ]);
});

test('update message successfully', function () {
    $user = LegacyUserFactory::new()->create();
    $message = MessageFactory::new()->createdBy($user)->create();
    $newMessageText = 'Updated message via API';

    $response = $this->actingAs($user)->putJson("/api/messages/{$message->id}", [
        'description' => $newMessageText,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'description' => $newMessageText,
            ],
        ]);

    $this->assertDatabaseHas('messages', [
        'id' => $message->id,
        'description' => $newMessageText,
    ]);
});

test('update message without permission', function () {
    $user1 = LegacyUserFactory::new()->admin()->create();
    $user2 = LegacyUserFactory::new()->state(['ref_cod_tipo_usuario' => function () {
        return LegacyUserTypeFactory::new()->create(['nivel' => 4]); // ESCOLA
    }])->create();
    $message = MessageFactory::new()->createdBy($user1)->create();
    $newObservation = 'Updated observation';

    $response = $this->actingAs($user2)->putJson("/api/messages/{$message->id}", [
        'description' => $newObservation,
    ]);

    $response->assertStatus(403)
        ->assertJson([
            'message' => 'Você não tem permissão para editar esta mensagem',
        ]);
});

test('update message with invalid message id', function () {
    $user = LegacyUserFactory::new()->admin()->create();
    $newObservation = 'Updated observation';

    $response = $this->actingAs($user)->putJson('/api/messages/99999', [
        'description' => $newObservation,
    ]);

    $response->assertStatus(404)
        ->assertJson([
            'message' => 'No query results for model [App\\Models\\Message] 99999',
        ]);
});

test('update message with invalid data', function () {
    $user = LegacyUserFactory::new()->create();
    $message = MessageFactory::new()->createdBy($user)->create();

    $response = $this->actingAs($user)->putJson("/api/messages/{$message->id}", [
        'description' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'description',
            ],
        ]);
});

test('delete message successfully', function () {
    $user = LegacyUserFactory::new()->create();
    $message = MessageFactory::new()->createdBy($user)->create();

    $response = $this->actingAs($user)->deleteJson("/api/messages/{$message->id}");

    $response->assertStatus(200);

    $this->assertSoftDeleted('messages', [
        'id' => $message->id,
    ]);
});

test('delete message without permission', function () {
    $user1 = LegacyUserFactory::new()->admin()->create();
    $user2 = LegacyUserFactory::new()->state(['ref_cod_tipo_usuario' => function () {
        return LegacyUserTypeFactory::new()->create(['nivel' => 4]); // ESCOLA
    }])->create();
    $message = MessageFactory::new()->createdBy($user1)->create();

    $response = $this->actingAs($user2)->deleteJson("/api/messages/{$message->id}");

    $response->assertStatus(403)
        ->assertJson([
            'message' => 'Você não tem permissão para excluir esta mensagem',
        ]);
});

test('delete message with invalid message id', function () {
    $user = LegacyUserFactory::new()->admin()->create();

    $response = $this->actingAs($user)->deleteJson('/api/messages/99999');

    $response->assertStatus(404)
        ->assertJson([
            'message' => 'No query results for model [App\\Models\\Message] 99999',
        ]);
});
