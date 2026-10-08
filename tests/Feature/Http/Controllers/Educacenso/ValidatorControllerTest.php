<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($user);
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $user->ref_cod_instituicao, 'active_on_ieducar' => true]);
});

test('valida nome, data de nascimento e certidão pelas regras do censo', function () {
    $this->getJson('/educacenso/validar/nome?value=Ana Alves')->assertOk()->assertExactJson(['success' => true]);
    $this->getJson('/educacenso/validar/nome?value=AAAAna')->assertStatus(422)->assertJson(['success' => false]);
    $this->getJson('/educacenso/validar/data-nascimento?value=2030-01-01')->assertStatus(422)->assertJson(['success' => false]);
    $this->getJson('/educacenso/validar/certidao-nascimento?' . http_build_query(['values' => ['12345678901234567890123456789012', '01/01/2015']]))->assertOk();
    $this->getJson('/educacenso/validar/cpf?value=1')->assertStatus(422)->assertExactJson(['error' => 'Wrong validator', 'success' => false]);
});

test('recusa um validador que não existe', function () {
    $this->getJson('/educacenso/validar/cpf?value=1')
        ->assertStatus(422)
        ->assertExactJson(['error' => 'Wrong validator', 'success' => false]);
});
