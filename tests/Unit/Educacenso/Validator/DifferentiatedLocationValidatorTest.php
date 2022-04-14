<?php

namespace Tests\Unit\Educacenso\Validator;

use App_Model_ZonaLocalizacao;
use iEducar\Modules\Educacenso\Model\LocalizacaoDiferenciadaPessoa;
use iEducar\Modules\Educacenso\Validator\DifferentiatedLocationValidator;
use Tests\TestCase;

class DifferentiatedLocationValidatorTest extends TestCase
{
    public function testDifferentiatedLocationIsAreaAssentamentoAndLocationZoneIsUrbana()
    {
        $differentiatedLocation = LocalizacaoDiferenciadaPessoa::AREA_ASSENTAMENTO;
        $locationZone = App_Model_ZonaLocalizacao::URBANA;
        $validator = new DifferentiatedLocationValidator($differentiatedLocation, $locationZone);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O campo: Localização diferenciada de residência não pode ser preenchido com <b>Área de assentamento</b> quando o campo: Zona de residência for <b>Urbana</b>.', $validator->getMessage());
    }

    public function testDifferentiatedLocationIsAreaAssentamentoAndLocationZoneIsNull()
    {
        $differentiatedLocation = LocalizacaoDiferenciadaPessoa::AREA_ASSENTAMENTO;
        $locationZone = null;
        $validator = new DifferentiatedLocationValidator($differentiatedLocation, $locationZone);

        $this->assertTrue($validator->isValid());
    }

    public function testDifferentiatedLocationIsAreaAssentamentoAndLocationZoneIsRural()
    {
        $differentiatedLocation = LocalizacaoDiferenciadaPessoa::AREA_ASSENTAMENTO;
        $locationZone = App_Model_ZonaLocalizacao::RURAL;
        $validator = new DifferentiatedLocationValidator($differentiatedLocation, $locationZone);

        $this->assertTrue($validator->isValid());
    }

    public function testDifferentiatedLocationNotIsAreaAssentamentoAndLocationZoneIsUrbana()
    {
        $anotherDifferentiatedLocations = [
            LocalizacaoDiferenciadaPessoa::TERRA_INDIGENA,
            LocalizacaoDiferenciadaPessoa::COMUNIDADES_REMANESCENTES_QUILOMBOS,
            LocalizacaoDiferenciadaPessoa::NAO_SE_APLICA,
        ];

        $differentiatedLocation = $anotherDifferentiatedLocations[array_rand($anotherDifferentiatedLocations)];
        $locationZone = App_Model_ZonaLocalizacao::URBANA;
        $validator = new DifferentiatedLocationValidator($differentiatedLocation, $locationZone);

        $this->assertTrue($validator->isValid());
    }
}
