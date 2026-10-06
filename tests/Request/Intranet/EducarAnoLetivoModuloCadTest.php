<?php

use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyStageTypeFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->school = LegacySchoolFactory::new()->create();
});

test('abre o formulário das etapas de um novo ano letivo', function () {
    $this->get('/intranet/educar_ano_letivo_modulo_cad.php?ref_cod_escola=' . $this->school->getKey() . '&ano=' . now()->year)
        ->assertOk();
});

test('regrava as etapas de um ano letivo existente', function () {
    LegacySchoolAcademicYearFactory::new()->withStageType(LegacyStageTypeFactory::new()->create())->create([
        'ref_cod_escola' => $this->school,
        'andamento' => 0,
    ]);

    $url = '/intranet/educar_ano_letivo_modulo_cad.php?ref_cod_escola=' . $this->school->getKey() . '&ano=' . now()->year;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent());

    $this->post($url, $payload)->assertRedirect();
});
