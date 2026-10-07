<?php

use App\Models\LegacySchool;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\SchoolManagerFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = LegacySchoolFactory::new()->withName('Escola Alfa')->create([
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
    ]);
    SchoolManagerFactory::new()->create(['school_id' => $this->school, 'chief' => true]);

    $this->url = '/intranet/educar_escola_cad.php?cod_escola=' . $this->school->getKey();
});

test('abre o formulário de uma escola nova', function () {
    $this->get('/intranet/educar_escola_cad.php')
        ->assertOk()
        ->assertSee('Escola');
});

test('edita a sigla da escola', function () {
    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), ['sigla' => 'EALFA']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($this->url, $payload)->assertRedirect('educar_escola_lst.php');

    expect($this->school->refresh()->sigla)->toBe('EALFA');
});

test('exclui a escola', function () {
    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post($this->url, $payload)->assertRedirect('educar_escola_lst.php');

    expect(LegacySchool::query()->whereKey($this->school->getKey())->value('ativo'))->toBe(0);
});
