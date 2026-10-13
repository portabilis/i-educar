<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacySchoolClassTypeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = $this->user->ref_cod_instituicao;

    $this->type = fn (string $name, array $attributes = []) => LegacySchoolClassTypeFactory::new()->create($attributes + [
        'nm_tipo' => $name,
        'ref_cod_instituicao' => $this->institution,
    ]);

    $this->detail = fn ($type) => 'educar_turma_tipo_det.php?cod_turma_tipo=' . $type->getKey();
    $this->list = fn (array $query = []) => $this->get('/intranet/educar_turma_tipo_lst.php?' . http_build_query($query))->assertOk();
});

test('lista os tipos de turma em ordem de nome, com a instituição e o botão Novo', function () {
    $beta = ($this->type)('Registro Beta');
    $alfa = ($this->type)('Registro Alfa');

    ($this->list)(['ref_cod_instituicao' => $this->institution])
        ->assertSeeInOrder(['Registro Alfa', 'Registro Beta'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertSee(($this->detail)($beta), false)
        ->assertSee($this->user->institution->nm_instituicao)
        ->assertSee('go("educar_turma_tipo_cad.php")', false);
});

test('filtra pelo nome, por trecho e sem diferenciar maiúsculas', function () {
    $alfa = ($this->type)('Registro Alfa');
    $beta = ($this->type)('Registro Beta');

    ($this->list)(['nm_tipo' => 'alf'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertDontSee(($this->detail)($beta), false);
});

test('filtra pela instituição', function () {
    $mine = ($this->type)('Registro Alfa');
    $other = ($this->type)('Registro Beta', ['ref_cod_instituicao' => LegacyInstitutionFactory::new()->create()]);

    ($this->list)(['ref_cod_instituicao' => $this->institution])
        ->assertSee(($this->detail)($mine), false)
        ->assertDontSee(($this->detail)($other), false);
});

test('não lista registro inativo', function () {
    $inactive = ($this->type)('Registro Alfa');
    $inactive->update(['ativo' => 0]);

    ($this->list)()->assertDontSee(($this->detail)($inactive), false);
});

test('não oferece o botão Novo a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $type = ($this->type)('Registro Alfa');

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(570, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    ($this->list)()
        ->assertSee(($this->detail)($type), false)
        ->assertDontSee('go("educar_turma_tipo_cad.php")', false);
});
