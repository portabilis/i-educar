<?php

use App\Events\RegistrationEvent;
use App\Listeners\AcceptTransferRequestListener;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyTransferRequestFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('a nova matrícula atende a solicitação de transferência pendente do aluno na mesma série e ano', function () {
    $old = LegacyRegistrationFactory::new()->create(['ano' => 2030]);
    $transfer = LegacyTransferRequestFactory::new()->create(['ref_cod_matricula_saida' => $old, 'ref_cod_matricula_entrada' => null]);
    $new = LegacyRegistrationFactory::new()->create(['ref_cod_aluno' => $old->ref_cod_aluno, 'ref_ref_cod_serie' => $old->ref_ref_cod_serie, 'ano' => 2030]);
    $listener = app(AcceptTransferRequestListener::class);

    $listener->handle(new RegistrationEvent(LegacyRegistrationFactory::new()->create(['ano' => 2030])));

    expect($transfer->refresh()->ref_cod_matricula_entrada)->toBeNull();

    $listener->handle(new RegistrationEvent($new));

    expect($transfer->refresh()->ref_cod_matricula_entrada)->toBe($new->getKey());
});
