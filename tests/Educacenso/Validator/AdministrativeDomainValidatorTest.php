<?php

namespace Tests\Educacenso\Validator;

use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\EsferaAdministrativa;
use iEducar\Modules\Educacenso\Model\Regulamentacao;
use iEducar\Modules\Educacenso\Validator\AdministrativeDomainValidator;
use Tests\TestCase;

class AdministrativeDomainValidatorTest extends TestCase
{
    private const BRASILIA = 5300108;

    private const OUTRO_MUNICIPIO = 4205407;

    private function validator(
        int $domain,
        int $dependence,
        int $city = self::OUTRO_MUNICIPIO,
        int $regulations = Regulamentacao::SIM
    ): AdministrativeDomainValidator {
        return new AdministrativeDomainValidator($domain, $regulations, $dependence, $city);
    }

    public function test_dependencia_federal_aceita_esfera_federal_e_estadual()
    {
        $validator = $this->validator(EsferaAdministrativa::FEDERAL_E_ESTADUAL, DependenciaAdministrativaEscola::FEDERAL);

        $this->assertTrue($validator->isValid());
    }

    public function test_dependencia_estadual_aceita_esfera_federal_e_estadual()
    {
        $validator = $this->validator(EsferaAdministrativa::FEDERAL_E_ESTADUAL, DependenciaAdministrativaEscola::ESTADUAL);

        $this->assertTrue($validator->isValid());
    }

    public function test_dependencia_estadual_aceita_esfera_federal()
    {
        $validator = $this->validator(EsferaAdministrativa::FEDERAL, DependenciaAdministrativaEscola::ESTADUAL);

        $this->assertTrue($validator->isValid());
    }

    public function test_dependencia_federal_aceita_esfera_estadual()
    {
        $validator = $this->validator(EsferaAdministrativa::ESTADUAL, DependenciaAdministrativaEscola::FEDERAL);

        $this->assertTrue($validator->isValid());
    }

    public function test_dependencia_federal_nao_aceita_esfera_municipal()
    {
        $validator = $this->validator(EsferaAdministrativa::MUNICIPAL, DependenciaAdministrativaEscola::FEDERAL);

        $this->assertFalse($validator->isValid());
    }

    public function test_dependencia_estadual_nao_aceita_esfera_estadual_e_municipal()
    {
        $validator = $this->validator(EsferaAdministrativa::ESTADUAL_E_MUNICIPAL, DependenciaAdministrativaEscola::ESTADUAL);

        $this->assertFalse($validator->isValid());
    }

    public function test_dependencia_municipal_aceita_esfera_municipal()
    {
        $validator = $this->validator(EsferaAdministrativa::MUNICIPAL, DependenciaAdministrativaEscola::MUNICIPAL);

        $this->assertTrue($validator->isValid());
    }

    public function test_brasilia_nao_aceita_esfera_municipal()
    {
        $validator = $this->validator(EsferaAdministrativa::MUNICIPAL, DependenciaAdministrativaEscola::MUNICIPAL, self::BRASILIA);

        $this->assertFalse($validator->isValid());
    }

    public function test_regulamentacao_nao_sempre_valido()
    {
        $validator = $this->validator(EsferaAdministrativa::MUNICIPAL, DependenciaAdministrativaEscola::FEDERAL, self::OUTRO_MUNICIPIO, Regulamentacao::NAO);

        $this->assertTrue($validator->isValid());
    }
}
