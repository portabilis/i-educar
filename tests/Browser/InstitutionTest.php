<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Browser\Scenario;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

it('lista as instituições e abre o detalhe', function () {
    Scenario::enableSystem();
    Scenario::createAdmin(login: '123456', password: 'senha-do-teste', name: 'Ana Diretora');

    $state = Scenario::createAddressing();
    Scenario::createInstitution('Instituição Original', $state);

    Scenario::login(login: '123456', password: 'senha-do-teste')
        ->navigate('/intranet/educar_instituicao_lst.php')
        ->assertSee('Instituição - Listagem')
        ->assertSee('Instituição Original')
        ->click('Instituição Original')
        ->assertPathIs('/intranet/educar_instituicao_det.php')
        ->assertSee('Instituição - Detalhe')
        ->assertSee('Nome da Instituição')
        ->assertNoJavaScriptErrors();
});

it('edita o nome da instituição a partir da listagem', function () {
    Scenario::enableSystem();
    Scenario::createAdmin(login: '123456', password: 'senha-do-teste', name: 'Ana Diretora');

    $state = Scenario::createAddressing();
    $institution = Scenario::createInstitution('Instituição Original', $state);

    Scenario::login(login: '123456', password: 'senha-do-teste')
        ->navigate('/intranet/educar_instituicao_lst.php')
        ->click('Instituição Original')
        ->assertSee('Instituição Original')
        ->click('Editar')
        ->assertPathIs('/intranet/educar_instituicao_cad.php')
        ->fill('#nm_instituicao', 'Instituição Renomeada')
        ->click('Salvar');

    expect($institution->fresh()->nm_instituicao)->toBe('Instituição Renomeada');
});
