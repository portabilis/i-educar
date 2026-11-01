<?php

use iEducar\Modules\Educacenso\Model\LocalizacaoDiferenciadaPessoa;
use iEducar\Modules\Educacenso\Validator\DifferentiatedLocationValidator;
use Tests\TestCase;

uses(TestCase::class);

test('differentiated location is area assentamento and location zone is urbana', function () {
    $differentiatedLocation = LocalizacaoDiferenciadaPessoa::AREA_ASSENTAMENTO;
    $locationZone = App_Model_ZonaLocalizacao::URBANA;
    $validator = new DifferentiatedLocationValidator($differentiatedLocation, $locationZone);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O campo: Localização diferenciada de residência não pode ser preenchido com <b>Área de assentamento</b> quando o campo: Zona de residência for <b>Urbana</b>.', $validator->getMessage());
});

test('differentiated location is area assentamento and location zone is null', function () {
    $differentiatedLocation = LocalizacaoDiferenciadaPessoa::AREA_ASSENTAMENTO;
    $locationZone = null;
    $validator = new DifferentiatedLocationValidator($differentiatedLocation, $locationZone);

    $this->assertTrue($validator->isValid());
});

test('differentiated location is area assentamento and location zone is rural', function () {
    $differentiatedLocation = LocalizacaoDiferenciadaPessoa::AREA_ASSENTAMENTO;
    $locationZone = App_Model_ZonaLocalizacao::RURAL;
    $validator = new DifferentiatedLocationValidator($differentiatedLocation, $locationZone);

    $this->assertTrue($validator->isValid());
});

test('differentiated location not is area assentamento and location zone is urbana', function () {
    $anotherDifferentiatedLocations = [
        LocalizacaoDiferenciadaPessoa::TERRA_INDIGENA,
        LocalizacaoDiferenciadaPessoa::COMUNIDADES_REMANESCENTES_QUILOMBOS,
        LocalizacaoDiferenciadaPessoa::NAO_SE_APLICA,
    ];

    $differentiatedLocation = $anotherDifferentiatedLocations[array_rand($anotherDifferentiatedLocations)];
    $locationZone = App_Model_ZonaLocalizacao::URBANA;
    $validator = new DifferentiatedLocationValidator($differentiatedLocation, $locationZone);

    $this->assertTrue($validator->isValid());
});
