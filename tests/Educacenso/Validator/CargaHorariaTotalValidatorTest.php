<?php

namespace Tests\Educacenso\Validator;

use iEducar\Modules\Educacenso\Validator\CargaHorariaTotalValidator;
use Tests\TestCase;

class CargaHorariaTotalValidatorTest extends TestCase
{
    private const CURSO_TECNICO = 1;

    private const QUALIFICACAO = 2;

    public function test_iftp_inativo_sempre_valido()
    {
        $validator = new CargaHorariaTotalValidator(false, null, null);

        $this->assertTrue($validator->isValid());
    }

    public function test_iftp_ativo_carga_nula_invalida()
    {
        $validator = new CargaHorariaTotalValidator(true, null, null);

        $this->assertFalse($validator->isValid());
    }

    public function test_iftp_ativo_carga_vazia_invalida()
    {
        $validator = new CargaHorariaTotalValidator(true, '', null);

        $this->assertFalse($validator->isValid());
    }

    public function test_carga_zero_invalida()
    {
        $validator = new CargaHorariaTotalValidator(true, 0, null);

        $this->assertFalse($validator->isValid());
    }

    public function test_carga_acima_de_quatro_digitos_invalida()
    {
        $validator = new CargaHorariaTotalValidator(true, 10000, null);

        $this->assertFalse($validator->isValid());
    }

    public function test_qualificacao_abaixo_de_160_invalida()
    {
        $validator = new CargaHorariaTotalValidator(true, 100, self::QUALIFICACAO);

        $this->assertFalse($validator->isValid());
    }

    public function test_qualificacao_acima_de_800_invalida()
    {
        $validator = new CargaHorariaTotalValidator(true, 900, self::QUALIFICACAO);

        $this->assertFalse($validator->isValid());
    }

    public function test_qualificacao_dentro_da_faixa_valida()
    {
        $validator = new CargaHorariaTotalValidator(true, 400, self::QUALIFICACAO);

        $this->assertTrue($validator->isValid());
    }

    public function test_tecnico_abaixo_de_2000_invalida()
    {
        $validator = new CargaHorariaTotalValidator(true, 1999, self::CURSO_TECNICO);

        $this->assertFalse($validator->isValid());
    }

    public function test_tecnico_igual_a_2000_valida()
    {
        $validator = new CargaHorariaTotalValidator(true, 2000, self::CURSO_TECNICO);

        $this->assertTrue($validator->isValid());
    }

    public function test_tecnico_acima_de_2000_valida()
    {
        $validator = new CargaHorariaTotalValidator(true, 2500, self::CURSO_TECNICO);

        $this->assertTrue($validator->isValid());
    }
}
