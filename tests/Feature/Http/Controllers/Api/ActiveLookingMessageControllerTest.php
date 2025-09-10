<?php

use App\Models\LegacyActiveLooking;
use Database\Factories\LegacyActiveLookingFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\MessageFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(DatabaseTransactions::class, TestCase::class);

test('store message returns personalized success message', function () {
    $activeLooking = LegacyActiveLookingFactory::new()->create();
    $user = LegacyUserFactory::new()->create();
    $observation = 'Test observation via API';

    $response = $this->actingAs($user)->postJson('/api/active-looking-messages', [
        'messageable_type' => LegacyActiveLooking::class,
        'messageable_id' => $activeLooking->id,
        'description' => $observation,
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Observação adicionada com sucesso',
        ]);
});

test('update message returns personalized success message', function () {
    $user = LegacyUserFactory::new()->create();
    $message = MessageFactory::new()->createdBy($user)->create();
    $newObservation = 'Updated observation via API';

    $response = $this->actingAs($user)->putJson("/api/active-looking-messages/{$message->id}", [
        'description' => $newObservation,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Observação atualizada com sucesso',
        ]);
});

test('delete message returns personalized success message', function () {
    $user = LegacyUserFactory::new()->create();
    $message = MessageFactory::new()->createdBy($user)->create();

    $response = $this->actingAs($user)->deleteJson("/api/active-looking-messages/{$message->id}");

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Observação excluída com sucesso',
        ]);
});

test('show message not found returns personalized error message', function () {
    $user = LegacyUserFactory::new()->create();

    $response = $this->actingAs($user)->getJson('/api/active-looking-messages/99999');

    $response->assertStatus(404)
        ->assertJson([
            'message' => 'Observação não encontrada',
        ]);
});
