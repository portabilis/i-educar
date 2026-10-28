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

test('unification person invalid pessoa principal', function () {
    $request = [
        'tipoacao' => 'Novo',
    ];

    $data = [
        'pessoas' => collect([
            [
                'idpes' => $this->individualOne->getKey(),
                'pessoaprincipal' => true,
            ],
            [
                'idpes' => $this->individualTwo->getKey(),
                'pessoa_principal' => false,
            ],
        ]),
    ];

    $payload = array_merge($request, $data);

    $this->post('/intranet/educar_unifica_pessoa.php', $payload)
        ->assertSuccessful()
        ->assertSee('Dados enviados inválidos, recarregue a tela e tente novamente!');
});

test('unification person not present pessoa principal', function () {
    $request = [
        'tipoacao' => 'Novo',
    ];

    $data = [
        'pessoas' => collect([
            [
                'idpes' => $this->individualOne->getKey(),
                'pessoa_principal' => false,
            ],
            [
                'idpes' => $this->individualTwo->getKey(),
                'pessoa_principal' => false,
            ],
        ]),
    ];

    $payload = array_merge($request, $data);

    $this->post('/intranet/educar_unifica_pessoa.php', $payload)
        ->assertSuccessful()
        ->assertSee('Pessoa principal não informada');
});

test('unification person duplicate pessoa principal', function () {
    $request = [
        'tipoacao' => 'Novo',
    ];

    $data = [
        'pessoas' => collect([
            [
                'idpes' => $this->individualOne->getKey(),
                'pessoa_principal' => true,
            ],
            [
                'idpes' => $this->individualTwo->getKey(),
                'pessoa_principal' => true,
            ],
        ]),
    ];

    $payload = array_merge($request, $data);

    $this->post('/intranet/educar_unifica_pessoa.php', $payload)
        ->assertSuccessful()
        ->assertSee('Não pode haver mais de uma pessoa principal');
});
