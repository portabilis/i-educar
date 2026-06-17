<?php

namespace Tests\Educacenso\Validator;

use iEducar\Modules\Educacenso\Validator\CargaHorariaTotalValidator;
use Tests\TestCase;

class CargaHorariaTotalValidatorTest extends TestCase
{
    private const CURSO_TECNICO = 1;

    private const QUALIFICACAO = 2;

    // Curso técnico com carga mínima 1200 na Tabela INEP (cursos_carga_horaria_minima.json)
    private const COD_CURSO_TECNICO = 1001;

    public function test_iftp_inativo_sempre_valido()
    {
        $validator = new CargaHorariaTotalValidator(false, null, null, null);

        $this->assertTrue($validator->isValid());
    }

    public function test_carga_nula_e_opcional_quando_iftp()
    {
        $validator = new CargaHorariaTotalValidator(true, null, self::CURSO_TECNICO, self::COD_CURSO_TECNICO);

        $this->assertTrue($validator->isValid());
    }

    public function test_carga_vazia_e_opcional_quando_iftp()
    {
        $validator = new CargaHorariaTotalValidator(true, '', self::CURSO_TECNICO, self::COD_CURSO_TECNICO);

        $this->assertTrue($validator->isValid());
    }

    public function test_carga_zero_invalida()
    {
        $validator = new CargaHorariaTotalValidator(true, 0, self::CURSO_TECNICO, self::COD_CURSO_TECNICO);

        $this->assertFalse($validator->isValid());
    }

    public function test_carga_acima_de_9999_invalida()
    {
        $validator = new CargaHorariaTotalValidator(true, 10000, self::QUALIFICACAO, null);

        $this->assertFalse($validator->isValid());
    }

    public function test_qualificacao_abaixo_de_160_invalida()
    {
        $validator = new CargaHorariaTotalValidator(true, 100, self::QUALIFICACAO, null);

        $this->assertFalse($validator->isValid());
    }

    public function test_qualificacao_acima_de_800_invalida()
    {
        $validator = new CargaHorariaTotalValidator(true, 900, self::QUALIFICACAO, null);

        $this->assertFalse($validator->isValid());
    }

    public function test_qualificacao_dentro_da_faixa_valida()
    {
        $validator = new CargaHorariaTotalValidator(true, 400, self::QUALIFICACAO, null);

        $this->assertTrue($validator->isValid());
    }

    public function test_tecnico_abaixo_da_carga_minima_do_curso_invalida()
    {
        $validator = new CargaHorariaTotalValidator(true, 1000, self::CURSO_TECNICO, self::COD_CURSO_TECNICO);

        $this->assertFalse($validator->isValid());
    }

    public function test_tecnico_igual_a_carga_minima_do_curso_valida()
    {
        $validator = new CargaHorariaTotalValidator(true, 1200, self::CURSO_TECNICO, self::COD_CURSO_TECNICO);

        $this->assertTrue($validator->isValid());
    }

    public function test_tecnico_acima_de_2000_valida()
    {
        $validator = new CargaHorariaTotalValidator(true, 2500, self::CURSO_TECNICO, self::COD_CURSO_TECNICO);

        $this->assertTrue($validator->isValid());
    }

    public function test_tecnico_sem_carga_minima_cadastrada_valida()
    {
        $validator = new CargaHorariaTotalValidator(true, 500, self::CURSO_TECNICO, 999999);

        $this->assertTrue($validator->isValid());
    }
}
