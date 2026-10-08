<?php

use Database\Factories\LegacyBenefitFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->benefit = fn (string $name, array $attributes = []) => LegacyBenefitFactory::new()->create($attributes + ['nm_beneficio' => $name]);
    $this->detail = fn ($benefit) => 'educar_aluno_beneficio_det.php?cod_aluno_beneficio=' . $benefit->getKey();
    $this->list = fn (array $query = []) => $this->get('/intranet/educar_aluno_beneficio_lst.php?' . http_build_query($query))->assertOk();
});

test('lista os benefícios em ordem de nome, com o link do detalhe e o botão Novo', function () {
    $beta = ($this->benefit)('Registro Beta');
    $alfa = ($this->benefit)('Registro Alfa');

    ($this->list)(['nm_beneficio' => 'Registro'])
        ->assertSeeInOrder(['Registro Alfa', 'Registro Beta'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertSee(($this->detail)($beta), false)
        ->assertSee('go("educar_aluno_beneficio_cad.php")', false);
});

test('filtra pelo nome, por trecho e sem diferenciar maiúsculas', function () {
    $alfa = ($this->benefit)('Registro Alfa');
    $beta = ($this->benefit)('Registro Beta');

    ($this->list)(['nm_beneficio' => 'tro alf'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertDontSee(($this->detail)($beta), false);
});

test('não lista benefício inativo', function () {
    $inactive = ($this->benefit)('Registro Alfa');
    $inactive->update(['ativo' => 0]);

    ($this->list)(['nm_beneficio' => 'Registro'])->assertDontSee(($this->detail)($inactive), false);
});

test('pagina de 20 em 20', function () {
    foreach (range(1, 21) as $number) {
        ($this->benefit)(sprintf('Registro %02d', $number));
    }

    ($this->list)(['nm_beneficio' => 'Registro'])->assertSee('Registro 20')->assertDontSee('Registro 21');
    ($this->list)(['nm_beneficio' => 'Registro', 'pagina_formulario' => 2])->assertSee('Registro 21')->assertDontSee('Registro 20');
});

test('não oferece o botão Novo a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $benefit = ($this->benefit)('Registro Alfa');

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(581, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    ($this->list)(['nm_beneficio' => 'Registro'])
        ->assertSee(($this->detail)($benefit), false)
        ->assertDontSee('go("educar_aluno_beneficio_cad.php")', false);
});
