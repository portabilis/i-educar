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

    $this->deficiency = fn (string $name, array $attributes = []) => LegacyDeficiencyFactory::new()->create($attributes + [
        'nm_deficiencia' => $name,
        'deficiency_type_id' => DeficiencyType::DEFICIENCY,
        'deficiencia_educacenso' => Deficiencias::CEGUEIRA,
        'transtorno_educacenso' => null,
    ]);
    $this->detail = fn ($deficiency) => 'educar_deficiencia_det.php?cod_deficiencia=' . $deficiency->getKey();
    $this->list = fn (array $query = []) => $this->get('/intranet/educar_deficiencia_lst.php?' . http_build_query($query))->assertOk();
});

test('lista em ordem de nome, com o tipo, o valor do Educacenso e o botão Novo', function () {
    $beta = ($this->deficiency)('Registro Beta', [
        'deficiency_type_id' => DeficiencyType::DISORDER,
        'deficiencia_educacenso' => null,
        'transtorno_educacenso' => Transtornos::DISLEXIA,
    ]);
    $alfa = ($this->deficiency)('Registro Alfa');

    ($this->list)(['nm_deficiencia' => 'Registro'])
        ->assertSeeInOrder(['Registro Alfa', 'Deficiência', 'Cegueira', 'Registro Beta', 'Transtorno', 'Dislexia'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertSee(($this->detail)($beta), false)
        ->assertSee('go("educar_deficiencia_cad.php")', false);
});

test('filtra pelo nome, por trecho, sem diferenciar maiúsculas nem acentos', function () {
    $alfa = ($this->deficiency)('Registro Álfa');
    $beta = ($this->deficiency)('Registro Beta');

    ($this->list)(['nm_deficiencia' => 'tro alf'])
        ->assertSee(($this->detail)($alfa), false)
        ->assertDontSee(($this->detail)($beta), false);
});

test('filtra pelo tipo', function () {
    $deficiency = ($this->deficiency)('Registro Alfa');
    $disorder = ($this->deficiency)('Registro Beta', ['deficiency_type_id' => DeficiencyType::DISORDER]);

    ($this->list)(['nm_deficiencia' => 'Registro', 'deficiency_type_id' => DeficiencyType::DISORDER])
        ->assertSee(($this->detail)($disorder), false)
        ->assertDontSee(($this->detail)($deficiency), false);
});

test('pagina de 20 em 20', function () {
    foreach (range(1, 21) as $number) {
        ($this->deficiency)(sprintf('Registro %02d', $number));
    }

    ($this->list)(['nm_deficiencia' => 'Registro'])->assertSee('Registro 20')->assertDontSee('Registro 21');
    ($this->list)(['nm_deficiencia' => 'Registro', 'pagina_formulario' => 2])->assertSee('Registro 21')->assertDontSee('Registro 20');
});

test('não oferece o botão Novo a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $deficiency = ($this->deficiency)('Registro Alfa');

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(631, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    ($this->list)(['nm_deficiencia' => 'Registro'])
        ->assertSee(($this->detail)($deficiency), false)
        ->assertDontSee('go("educar_deficiencia_cad.php")', false);
});
