<?php

use App\Models\DeficiencyType;
use Database\Factories\LegacyDeficiencyFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use iEducar\Modules\Educacenso\Model\Deficiencias;
use iEducar\Modules\Educacenso\Model\Transtornos;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->deficiency = fn (array $attributes = []) => LegacyDeficiencyFactory::new()->create($attributes + [
        'nm_deficiencia' => 'Registro Alfa',
        'deficiency_type_id' => DeficiencyType::DEFICIENCY,
        'deficiencia_educacenso' => Deficiencias::CEGUEIRA,
        'transtorno_educacenso' => null,
        'exigir_laudo_medico' => true,
        'desconsidera_regra_diferenciada' => true,
    ]);
    $this->url = fn ($deficiency) => '/intranet/educar_deficiencia_det.php?cod_deficiencia=' . $deficiency->getKey();
});

test('mostra os dados da deficiência e os links de novo e de edição', function () {
    $deficiency = ($this->deficiency)();

    $this->get(($this->url)($deficiency))
        ->assertOk()
        ->assertSeeInOrder([
            'Descrição', 'Registro Alfa',
            'Tipo', 'Deficiência',
            'Deficiência no Educacenso', 'Cegueira',
            'Exigir laudo médico', 'Sim',
            'Desconsidera regra diferenciada', 'Sim',
        ])
        ->assertDontSee('Transtorno no Educacenso')
        ->assertSee('educar_deficiencia_cad.php?cod_deficiencia=' . $deficiency->getKey(), false)
        ->assertSee('educar_deficiencia_lst.php', false);
});

test('mostra o transtorno do Educacenso e as marcações desligadas', function () {
    $deficiency = ($this->deficiency)([
        'deficiency_type_id' => DeficiencyType::DISORDER,
        'deficiencia_educacenso' => null,
        'transtorno_educacenso' => Transtornos::DISLEXIA,
        'exigir_laudo_medico' => false,
        'desconsidera_regra_diferenciada' => false,
    ]);

    $this->get(($this->url)($deficiency))
        ->assertOk()
        ->assertSeeInOrder([
            'Tipo', 'Transtorno',
            'Transtorno no Educacenso', 'Dislexia',
            'Exigir laudo médico', 'Não',
            'Desconsidera regra diferenciada', 'Não',
        ])
        ->assertDontSee('Deficiência no Educacenso');
});

test('não oferece novo nem editar a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $deficiency = ($this->deficiency)();

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(631, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    $this->get(($this->url)($deficiency))
        ->assertOk()
        ->assertSee('Registro Alfa')
        ->assertDontSee('educar_deficiencia_cad.php', false);
});

test('volta para a listagem quando a deficiência não existe', function () {
    $this->get('/intranet/educar_deficiencia_det.php?cod_deficiencia=0')->assertRedirect('educar_deficiencia_lst.php');
});
