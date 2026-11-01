<?php

use Database\Factories\LegacyIndividualFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\LoginFirstUser;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, LoginFirstUser::class);

beforeEach(function () {
    $this->loginWithFirstUser();

    $this->individualOne = LegacyIndividualFactory::new()->create();
    $this->individualTwo = LegacyIndividualFactory::new()->create();
});

test('unification person json invalid', function () {
    $request = [
        'tipoacao' => 'Novo',
    ];

    $data = [
        'pessoas' => [
            [
                'idpes' => $this->individualOne->getKey(),
                'pessoa_principal' => true,
            ],
            [
                'idpes' => $this->individualTwo->getKey(),
                'pessoa_principal' => false,
            ],
        ],
    ];

    $payload = array_merge($request, $data);

    $this->post('/intranet/educar_unifica_pessoa.php', $payload)
        ->assertSuccessful()
        ->assertSee('Informações inválidas para unificação');
});

test('unification person count', function () {
    $request = [
        'tipoacao' => 'Novo',
    ];

    $data = [
        'pessoas' => collect([
            [
                'idpes' => $this->individualOne->getKey(),
                'pessoa_principal' => true,
            ],
        ]),
    ];

    $payload = array_merge($request, $data);

    $this->post('/intranet/educar_unifica_pessoa.php', $payload)
        ->assertSuccessful()
        ->assertSee('Informe no mínimo duas pessoas para unificação.');
});
