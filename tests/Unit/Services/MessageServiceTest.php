<?php

use App\Models\LegacyActiveLooking;
use App\Models\Message;
use App\Services\MessageService;
use Database\Factories\LegacyActiveLookingFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Database\Factories\MessageFactory;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->messageService = new MessageService;
});

test('create message successfully', function () {
    $activeLooking = LegacyActiveLookingFactory::new()->create();
    $user = LegacyUserFactory::new()->create();
    $description = 'Test description';

    $message = $this->messageService->create(
        LegacyActiveLooking::class,
        $activeLooking->id,
        $user->id,
        $description
    );

    $this->assertInstanceOf(Message::class, $message);
    $this->assertEquals($description, $message->description);
    $this->assertEquals($user->id, $message->user_id);
    $this->assertEquals(LegacyActiveLooking::class, $message->messageable_type);
    $this->assertEquals($activeLooking->id, $message->messageable_id);
    $this->assertNotNull($message->user);
    $this->assertEquals($user->id, $message->user->id);
});

test('update message successfully', function () {
    $user = LegacyUserFactory::new()->create();
    $message = MessageFactory::new()->createdBy($user)->create();
    $newDescription = 'Updated description';

    $updatedMessage = $this->messageService->update(
        $message->id,
        $user->id,
        $newDescription
    );

    $this->assertInstanceOf(Message::class, $updatedMessage);
    $this->assertEquals($newDescription, $updatedMessage->description);
    $this->assertEquals($user->id, $updatedMessage->user_id);
});

test('update message without permission', function () {
    $user1 = LegacyUserFactory::new()->admin()->create();
    $user2 = LegacyUserFactory::new()->state(['ref_cod_tipo_usuario' => function () {
        return LegacyUserTypeFactory::new()->create(['nivel' => 4]); // ESCOLA
    }])->create();
    $message = MessageFactory::new()->createdBy($user1)->create();
    $newDescription = 'Updated description';

    $this->expectException(AuthorizationException::class);
    $this->expectExceptionMessage('Você não tem permissão para editar esta mensagem');
    $this->messageService->update($message->id, $user2->id, $newDescription);
});

test('update message with invalid message id', function () {
    $user = LegacyUserFactory::new()->create();
    $newDescription = 'Updated description';

    $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
    $this->messageService->update(99999, $user->id, $newDescription);
});

test('delete message successfully', function () {
    $user = LegacyUserFactory::new()->create();
    $message = MessageFactory::new()->createdBy($user)->create();

    $result = $this->messageService->delete($message->id, $user->id);

    $this->assertTrue($result);
    $this->assertDatabaseHas('messages', [
        'id' => $message->id,
        'deleted_at' => $message->fresh()->deleted_at,
    ]);
});

test('delete message without permission', function () {
    $user1 = LegacyUserFactory::new()->admin()->create();
    $user2 = LegacyUserFactory::new()->state(['ref_cod_tipo_usuario' => function () {
        return LegacyUserTypeFactory::new()->create(['nivel' => 4]); // ESCOLA
    }])->create();
    $message = MessageFactory::new()->createdBy($user1)->create();

    $this->expectException(AuthorizationException::class);
    $this->expectExceptionMessage('Você não tem permissão para excluir esta mensagem');
    $this->messageService->delete($message->id, $user2->id);
});

test('delete message with invalid message id', function () {
    $user = LegacyUserFactory::new()->create();

    $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
    $this->messageService->delete(99999, $user->id);
});

test('find message successfully', function () {
    $message = MessageFactory::new()->create();

    $foundMessage = $this->messageService->find($message->id);

    $this->assertInstanceOf(Message::class, $foundMessage);
    $this->assertEquals($message->id, $foundMessage->id);
    $this->assertNotNull($foundMessage->user);
});

test('find message with invalid id', function () {
    $foundMessage = $this->messageService->find(99999);

    $this->assertNull($foundMessage);
});

test('get messages by type and id', function () {
    $activeLooking = LegacyActiveLookingFactory::new()->create();
    $messages = MessageFactory::new()
        ->count(3)
        ->forActiveLooking($activeLooking)
        ->create();

    $foundMessages = $this->messageService->get(LegacyActiveLooking::class, $activeLooking->id);

    $this->assertCount(3, $foundMessages);
    $this->assertInstanceOf(Message::class, $foundMessages->first());
    $this->assertEquals($activeLooking->id, $foundMessages->first()->messageable_id);
});

test('get messages by type and id returns empty collection', function () {
    $activeLooking = LegacyActiveLookingFactory::new()->create();

    $foundMessages = $this->messageService->get(LegacyActiveLooking::class, $activeLooking->id);

    $this->assertCount(0, $foundMessages);
});

test('poli institutional user can edit any message', function () {
    $poliUser = LegacyUserFactory::new()->admin()->create();
    $regularUser = LegacyUserFactory::new()->create();
    $message = MessageFactory::new()->createdBy($regularUser)->create();
    $newDescription = 'Description edited by poli-institutional';

    $updatedMessage = $this->messageService->update(
        $message->id,
        $poliUser->id,
        $newDescription
    );

    $this->assertInstanceOf(Message::class, $updatedMessage);
    $this->assertEquals($newDescription, $updatedMessage->description);
});

test('poli institutional user can delete any message', function () {
    $poliUser = LegacyUserFactory::new()->admin()->create();
    $regularUser = LegacyUserFactory::new()->create();
    $message = MessageFactory::new()->createdBy($regularUser)->create();

    $result = $this->messageService->delete($message->id, $poliUser->id);

    $this->assertTrue($result);
    $this->assertDatabaseHas('messages', [
        'id' => $message->id,
        'deleted_at' => $message->fresh()->deleted_at,
    ]);
});

test('institutional user can edit any message', function () {
    $institutionalUser = LegacyUserFactory::new()->institutional()->create();
    $regularUser = LegacyUserFactory::new()->create();
    $message = MessageFactory::new()->createdBy($regularUser)->create();
    $newDescription = 'Description edited by institutional';

    $updatedMessage = $this->messageService->update(
        $message->id,
        $institutionalUser->id,
        $newDescription
    );

    $this->assertInstanceOf(Message::class, $updatedMessage);
    $this->assertEquals($newDescription, $updatedMessage->description);
});

test('institutional user can delete any message', function () {
    $institutionalUser = LegacyUserFactory::new()->institutional()->create();
    $regularUser = LegacyUserFactory::new()->create();
    $message = MessageFactory::new()->createdBy($regularUser)->create();

    $result = $this->messageService->delete($message->id, $institutionalUser->id);

    $this->assertTrue($result);
    $this->assertDatabaseHas('messages', [
        'id' => $message->id,
        'deleted_at' => $message->fresh()->deleted_at,
    ]);
});

test('regular user can edit own message', function () {
    $user = LegacyUserFactory::new()->create();
    $message = MessageFactory::new()->createdBy($user)->create();
    $newDescription = 'Updated description by owner';

    $updatedMessage = $this->messageService->update(
        $message->id,
        $user->id,
        $newDescription
    );

    $this->assertInstanceOf(Message::class, $updatedMessage);
    $this->assertEquals($newDescription, $updatedMessage->description);
});

test('regular user cannot edit other user message', function () {
    $user1 = LegacyUserFactory::new()->state(['ref_cod_tipo_usuario' => function () {
        return LegacyUserTypeFactory::new()->create(['nivel' => 4]); // ESCOLA
    }])->create();
    $user2 = LegacyUserFactory::new()->state(['ref_cod_tipo_usuario' => function () {
        return LegacyUserTypeFactory::new()->create(['nivel' => 4]); // ESCOLA
    }])->create();
    $message = MessageFactory::new()->createdBy($user1)->create();
    $newDescription = 'Updated description by other user';

    $this->expectException(AuthorizationException::class);
    $this->expectExceptionMessage('Você não tem permissão para editar esta mensagem');
    $this->messageService->update($message->id, $user2->id, $newDescription);
});

test('poli institutional user can edit message without user', function () {
    $poliUser = LegacyUserFactory::new()->admin()->create();
    $message = MessageFactory::new()->withoutUser()->create();
    $newDescription = 'Updated description by poli-institutional';

    $updatedMessage = $this->messageService->update(
        $message->id,
        $poliUser->id,
        $newDescription
    );

    $this->assertInstanceOf(Message::class, $updatedMessage);
    $this->assertEquals($newDescription, $updatedMessage->description);
});

test('institutional user can edit message without user', function () {
    $institutionalUser = LegacyUserFactory::new()->institutional()->create();
    $message = MessageFactory::new()->withoutUser()->create();
    $newDescription = 'Updated description by institutional user';

    $updatedMessage = $this->messageService->update(
        $message->id,
        $institutionalUser->id,
        $newDescription
    );

    $this->assertInstanceOf(Message::class, $updatedMessage);
    $this->assertEquals($newDescription, $updatedMessage->description);
});

test('regular user cannot edit message without user', function () {
    $regularUser = LegacyUserFactory::new()->state(['ref_cod_tipo_usuario' => function () {
        return LegacyUserTypeFactory::new()->create(['nivel' => 4]); // ESCOLA
    }])->create();
    $message = MessageFactory::new()->withoutUser()->create();
    $newDescription = 'Updated description by regular user';

    $this->expectException(AuthorizationException::class);
    $this->expectExceptionMessage('Você não tem permissão para editar esta mensagem');
    $this->messageService->update($message->id, $regularUser->id, $newDescription);
});

test('service is generic and accepts any model type', function () {
    $user = LegacyUserFactory::new()->create();
    $description = 'Test description for any model';

    // Teste com LegacyActiveLooking (modelo atual)
    $activeLooking = LegacyActiveLookingFactory::new()->create();
    $message1 = $this->messageService->create(
        LegacyActiveLooking::class,
        $activeLooking->id,
        $user->id,
        $description . ' - ActiveLooking'
    );

    $this->assertInstanceOf(Message::class, $message1);
    $this->assertEquals(LegacyActiveLooking::class, $message1->messageable_type);
    $this->assertEquals($activeLooking->id, $message1->messageable_id);

    // Teste com string de classe (simulando outro modelo)
    $message2 = $this->messageService->create(
        'App\\Models\\Student',
        999,
        $user->id,
        $description . ' - Student'
    );

    $this->assertInstanceOf(Message::class, $message2);
    $this->assertEquals('App\\Models\\Student', $message2->messageable_type);
    $this->assertEquals(999, $message2->messageable_id);

    // Teste com outro modelo fictício
    $message3 = $this->messageService->create(
        'App\\Models\\Registration',
        888,
        $user->id,
        $description . ' - Registration'
    );

    $this->assertInstanceOf(Message::class, $message3);
    $this->assertEquals('App\\Models\\Registration', $message3->messageable_type);
    $this->assertEquals(888, $message3->messageable_id);
});

test('get messages works with any model type string', function () {
    $user = LegacyUserFactory::new()->create();

    // Criar mensagens para diferentes tipos de modelos
    $activeLooking = LegacyActiveLookingFactory::new()->create();

    // Mensagens para ActiveLooking
    MessageFactory::new()->count(2)->forActiveLooking($activeLooking)->createdBy($user)->create();

    // Mensagens para Student (string)
    MessageFactory::new()->count(3)->state([
        'messageable_type' => 'App\\Models\\Student',
        'messageable_id' => 999,
    ])->createdBy($user)->create();

    // Mensagens para Registration (string)
    MessageFactory::new()->count(1)->state([
        'messageable_type' => 'App\\Models\\Registration',
        'messageable_id' => 888,
    ])->createdBy($user)->create();

    // Buscar mensagens por tipo
    $activeLookingMessages = $this->messageService->get(LegacyActiveLooking::class, $activeLooking->id);
    $studentMessages = $this->messageService->get('App\\Models\\Student', 999);
    $registrationMessages = $this->messageService->get('App\\Models\\Registration', 888);

    $this->assertCount(2, $activeLookingMessages);
    $this->assertCount(3, $studentMessages);
    $this->assertCount(1, $registrationMessages);

    // Verificar se os tipos estão corretos
    $this->assertEquals(LegacyActiveLooking::class, $activeLookingMessages->first()->messageable_type);
    $this->assertEquals('App\\Models\\Student', $studentMessages->first()->messageable_type);
    $this->assertEquals('App\\Models\\Registration', $registrationMessages->first()->messageable_type);
});
