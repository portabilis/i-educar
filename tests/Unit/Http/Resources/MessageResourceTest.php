<?php

use App\Http\Resources\MessageResource;
use App\User;
use Database\Factories\LegacyUserFactory;
use Database\Factories\MessageFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('o administrador pode editar e excluir a mensagem de outro usuário', function () {
    $message = MessageFactory::new()->create();
    $this->actingAs(User::query()->findOrFail(LegacyUserFactory::new()->admin()->create()->getKey()));

    expect((new MessageResource($message))->toArray(Request::create('/')))
        ->toMatchArray(['can_edit' => true, 'can_delete' => true]);
});
