<?php

use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);

    $this->schoolClass = fn (array $attributes = []) => $this->scenario->schoolClass($attributes + ['nm_turma' => 'Turma Alfa']);
    $this->url = fn ($schoolClass) => '/intranet/educar_parecer_turma_cad.php?cod_turma=' . $schoolClass->getKey();
});

test('abre com os pareceres já gravados na turma', function (int $standard) {
    $this->scenario->course->update(['padrao_ano_escolar' => $standard]);

    $schoolClass = ($this->schoolClass)(['parecer_1_etapa' => 'Turma participativa', 'parecer_3_etapa' => 'Evoluiu na leitura']);

    $fields = LegacyFields::from($this->get(($this->url)($schoolClass))->assertOk()->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['cod_turma'])->toBe((string) $schoolClass->getKey())
        ->and($fields['ano'])->toBe('2030')
        ->and($fields['parecer_1_etapa'])->toBe('Turma participativa')
        ->and($fields['parecer_2_etapa'])->toBe('')
        ->and($fields['parecer_3_etapa'])->toBe('Evoluiu na leitura');
})->with([
    'curso com padrão de ano escolar' => 1,
    'curso sem padrão de ano escolar' => 0,
]);

test('grava os quatro pareceres e volta para a turma', function () {
    $schoolClass = ($this->schoolClass)();

    $payload = LegacyFields::from($this->get(($this->url)($schoolClass))->assertOk()->getContent(), [
        'parecer_1_etapa' => 'Global do primeiro semestre',
        'parecer_2_etapa' => 'Educação física do primeiro semestre',
        'parecer_3_etapa' => 'Global do segundo semestre',
        'parecer_4_etapa' => 'Educação física do segundo semestre',
    ]);

    $this->post(($this->url)($schoolClass), $payload)->assertRedirect('educar_turma_det.php?cod_turma=' . $schoolClass->getKey());

    expect($schoolClass->refresh())
        ->parecer_1_etapa->toBe('Global do primeiro semestre')
        ->parecer_2_etapa->toBe('Educação física do primeiro semestre')
        ->parecer_3_etapa->toBe('Global do segundo semestre')
        ->parecer_4_etapa->toBe('Educação física do segundo semestre')
        ->ref_usuario_exc->toBe($this->user->getKey());
});

// Defeito conhecido: a tela grava os pareceres pela edição da classe legada
// de turma, que escreve NULL em tudo o que não recebeu valor. Lançar um
// parecer apaga o regente, a etapa do Educacenso e a disciplina dispensada da
// turma.
test('defeito: gravar os pareceres apaga outros dados da turma', function () {
    $schoolClass = ($this->schoolClass)([
        'ref_cod_regente' => $regent = EmployeeFactory::new()->create(['institution_id' => $this->scenario->institution])->getKey(),
        'ref_cod_instituicao_regente' => $this->scenario->institution,
        'etapa_educacenso' => 14,
        'ref_cod_disciplina_dispensada' => $this->scenario->discipline('Ensino Religioso'),
    ]);

    expect($schoolClass->refresh()->ref_cod_regente)->toBe($regent);

    $payload = LegacyFields::from($this->get(($this->url)($schoolClass))->assertOk()->getContent(), ['parecer_1_etapa' => 'Turma participativa']);

    $this->post(($this->url)($schoolClass), $payload)->assertRedirect('educar_turma_det.php?cod_turma=' . $schoolClass->getKey());

    expect($schoolClass->refresh())
        ->parecer_1_etapa->toBe('Turma participativa')
        ->ref_cod_regente->toBeNull()
        ->etapa_educacenso->toBeNull()
        ->ref_cod_disciplina_dispensada->toBeNull();
});

test('avisa o erro quando a turma não vem no envio', function () {
    $schoolClass = ($this->schoolClass)();

    $payload = LegacyFields::from($this->get(($this->url)($schoolClass))->assertOk()->getContent(), ['cod_turma' => '']);

    $this->post(($this->url)($schoolClass), $payload)->assertOk()->assertSee('Erro ao salvar lançamentos.');
});

test('volta para o detalhe da turma quando o usuário só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $schoolClass = ($this->schoolClass)();

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(586, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    $this->get(($this->url)($schoolClass))->assertRedirect();
});
