<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = ['ref_cod_instituicao' => $this->user->ref_cod_instituicao];
    $this->school = LegacySchoolFactory::new()->create($this->institution);
    $this->url = '/intranet/educar_escola_ano_letivo_cad.php?cod_escola=' . $this->school->getKey();
});

test('oferece dez anos a partir de cinco anos atrás', function () {
    $years = array_values(array_filter(array_keys(LegacyForm::from($this->get($this->url)->assertOk()->getContent())->options('ano'))));

    expect($years)->toBe(range(now()->year - 5, now()->year + 4));
});

test('pula os anos já finalizados da escola, mantendo dez opções', function () {
    LegacySchoolAcademicYearFactory::new()->create([
        'ref_cod_escola' => $this->school,
        'ano' => now()->year,
        'andamento' => 2,
    ]);

    $years = array_values(array_filter(array_keys(LegacyForm::from($this->get($this->url)->assertOk()->getContent())->options('ano'))));

    expect($years)->not->toContain(now()->year)
        ->and($years)->toHaveCount(10)
        ->and(end($years))->toBe(now()->year + 5);
});

test('escolhido o ano, segue para a tela das etapas', function () {
    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), ['ano' => now()->year]);

    expect($payload['ref_cod_escola'])->toBe((string) $this->school->getKey());

    $this->post($this->url, $payload)->assertRedirect(
        "educar_ano_letivo_modulo_cad.php?ref_cod_escola={$this->school->getKey()}&ano=" . now()->year
    );
});

test('volta para a listagem de escolas quando o usuário só pode visualizar escola', function () {
    LegacyGeneralConfigurationFactory::new()->create($this->institution + ['active_on_ieducar' => true]);

    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(561, modify: false, remove: false)->create());

    $this->get($this->url)->assertRedirect('educar_escola_lst.php');
});
