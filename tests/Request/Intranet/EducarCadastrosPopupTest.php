<?php

use App\Models\LegacyDeficiency;
use App\Models\LegacyEducationLevel;
use App\Models\LegacyEducationType;
use App\Models\LegacyRegimeType;
use App\Models\LegacySchoolClassType;
use App\Models\LegacySchoolingDegree;
use Database\Factories\LegacyDeficiencyFactory;
use Database\Factories\LegacyEducationLevelFactory;
use Database\Factories\LegacyEducationTypeFactory;
use Database\Factories\LegacyRegimeTypeFactory;
use Database\Factories\LegacySchoolClassTypeFactory;
use Database\Factories\LegacySchoolingDegreeFactory;
use Database\Factories\LegacyUserFactory;

// Cadastros rápidos abertos em pop-up a partir de outras telas.
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
});

dataset('cadastros em pop-up', [
    'tipo de ensino' => ['educar_tipo_ensino_cad_pop.php', 'cod_tipo_ensino', LegacyEducationTypeFactory::class, LegacyEducationType::class, ['nm_tipo' => 'Regular']],
    'tipo de turma' => ['educar_turma_tipo_cad_pop.php', 'cod_turma_tipo', LegacySchoolClassTypeFactory::class, LegacySchoolClassType::class, ['nm_tipo' => 'Regular', 'sgl_tipo' => 'REG']],
    'tipo de regime' => ['educar_tipo_regime_cad_pop.php', 'cod_tipo_regime', LegacyRegimeTypeFactory::class, LegacyRegimeType::class, ['nm_tipo' => 'Seriado']],
    'deficiência' => ['educar_deficiencia_cad_pop.php', 'cod_deficiencia', LegacyDeficiencyFactory::class, LegacyDeficiency::class, []],
    'nível de ensino' => ['educar_nivel_ensino_cad_pop.php', 'cod_nivel_ensino', LegacyEducationLevelFactory::class, LegacyEducationLevel::class, ['nm_nivel' => 'Fundamental']],
    'escolaridade' => ['educar_escolaridade_cad_pop.php', 'idesco', LegacySchoolingDegreeFactory::class, LegacySchoolingDegree::class, ['descricao' => 'Superior']],
]);

test('abre vazio, com a lista de instituições e preenchido com um registro', function (string $screen, string $key, string $factory, string $model) {
    $record = $factory::new()->create();

    $this->get("/intranet/{$screen}")->assertOk();
    $this->get("/intranet/{$screen}?precisa_lista=1")->assertOk();
    $this->get("/intranet/{$screen}?{$key}={$record->getKey()}")->assertOk();
})->with('cadastros em pop-up');

test('o tipo de ensino inexistente ou inativo volta para a listagem', function () {
    // A criação sempre grava ativo; a inativação vem depois.
    $inactive = LegacyEducationTypeFactory::new()->create();
    LegacyEducationType::query()->whereKey($inactive->getKey())->update(['ativo' => 0]);

    $this->get('/intranet/educar_tipo_ensino_cad_pop.php?cod_tipo_ensino=0')->assertRedirect('educar_tipo_ensino_lst.php');
    $this->get('/intranet/educar_tipo_ensino_cad_pop.php?cod_tipo_ensino=' . $inactive->getKey())->assertRedirect('educar_tipo_ensino_lst.php');
});
