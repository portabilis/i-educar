<?php

namespace Tests\Educacenso\Validator;

use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola as Dep;
use iEducar\Modules\Educacenso\Model\EsferaAdministrativa as Esfera;
use iEducar\Modules\Educacenso\Validator\AdministrativeDomainValidator;
use Tests\TestCase;

class AdministrativeDomainValidatorTest extends TestCase
{
    private const BRASILIA = 5300108;

    private const OUTRO_MUNICIPIO = 4205407;

    private function validator(int $domain, int $dependence, int $city = self::OUTRO_MUNICIPIO): AdministrativeDomainValidator
    {
        return new AdministrativeDomainValidator($domain, $dependence, $city);
    }

    public function test_dependencia_federal_aceita_apenas_federal_e_federal_setec(): void
    {
        $this->assertTrue($this->validator(Esfera::FEDERAL, Dep::FEDERAL)->isValid());
        $this->assertTrue($this->validator(Esfera::FEDERAL_SETEC, Dep::FEDERAL)->isValid());
        $this->assertFalse($this->validator(Esfera::ESTADUAL, Dep::FEDERAL)->isValid());
        $this->assertFalse($this->validator(Esfera::MUNICIPAL, Dep::FEDERAL)->isValid());
        $this->assertFalse($this->validator(Esfera::ESTADUAL_E_MUNICIPAL, Dep::FEDERAL)->isValid());
    }

    public function test_dependencia_estadual_aceita_apenas_estadual(): void
    {
        $this->assertTrue($this->validator(Esfera::ESTADUAL, Dep::ESTADUAL)->isValid());
        $this->assertFalse($this->validator(Esfera::FEDERAL, Dep::ESTADUAL)->isValid());
        $this->assertFalse($this->validator(Esfera::FEDERAL_SETEC, Dep::ESTADUAL)->isValid());
        $this->assertFalse($this->validator(Esfera::MUNICIPAL, Dep::ESTADUAL)->isValid());
        $this->assertFalse($this->validator(Esfera::ESTADUAL_E_MUNICIPAL, Dep::ESTADUAL)->isValid());
    }

    public function test_dependencia_municipal_aceita_estadual_municipal_e_estadual_e_municipal(): void
    {
        $this->assertTrue($this->validator(Esfera::ESTADUAL, Dep::MUNICIPAL)->isValid());
        $this->assertTrue($this->validator(Esfera::MUNICIPAL, Dep::MUNICIPAL)->isValid());
        $this->assertTrue($this->validator(Esfera::ESTADUAL_E_MUNICIPAL, Dep::MUNICIPAL)->isValid());
        $this->assertFalse($this->validator(Esfera::FEDERAL, Dep::MUNICIPAL)->isValid());
        $this->assertFalse($this->validator(Esfera::FEDERAL_SETEC, Dep::MUNICIPAL)->isValid());
    }

    public function test_dependencia_privada_aceita_estadual_municipal_e_estadual_e_municipal(): void
    {
        $this->assertTrue($this->validator(Esfera::ESTADUAL, Dep::PRIVADA)->isValid());
        $this->assertTrue($this->validator(Esfera::MUNICIPAL, Dep::PRIVADA)->isValid());
        $this->assertTrue($this->validator(Esfera::ESTADUAL_E_MUNICIPAL, Dep::PRIVADA)->isValid());
        $this->assertFalse($this->validator(Esfera::FEDERAL, Dep::PRIVADA)->isValid());
        $this->assertFalse($this->validator(Esfera::FEDERAL_SETEC, Dep::PRIVADA)->isValid());
    }

    public function test_dependencia_privada_em_brasilia_aceita_apenas_estadual(): void
    {
        $this->assertTrue($this->validator(Esfera::ESTADUAL, Dep::PRIVADA, self::BRASILIA)->isValid());
        $this->assertFalse($this->validator(Esfera::MUNICIPAL, Dep::PRIVADA, self::BRASILIA)->isValid());
        $this->assertFalse($this->validator(Esfera::ESTADUAL_E_MUNICIPAL, Dep::PRIVADA, self::BRASILIA)->isValid());
    }

    public function test_esfera_vazia_e_sempre_valida(): void
    {
        $this->assertTrue($this->validator(0, Dep::FEDERAL)->isValid());
    }

    public function test_valor_legado_cinco_e_invalido_em_qualquer_dependencia(): void
    {
        $this->assertFalse($this->validator(5, Dep::FEDERAL)->isValid());
        $this->assertFalse($this->validator(5, Dep::ESTADUAL)->isValid());
        $this->assertFalse($this->validator(5, Dep::MUNICIPAL)->isValid());
        $this->assertFalse($this->validator(5, Dep::PRIVADA)->isValid());
    }
}
