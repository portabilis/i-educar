<?php

namespace Tests\Educacenso\Validator;

use iEducar\Modules\Educacenso\Validator\CargaHorariaTotalValidator;
use Tests\TestCase;

class CargaHorariaTotalValidatorTest extends TestCase
{
    private function validator(
        bool $iftpAtivo,
        int $etapa,
        $carga,
        int $tipoCurso = 0,
        int $codCurso = 0
    ): CargaHorariaTotalValidator {
        return new CargaHorariaTotalValidator($iftpAtivo, $etapa, $carga, $tipoCurso, $codCurso);
    }

    public function test_campo_nao_aplicavel_sempre_valido(): void
    {
        // Sem IFTP e etapa fora do conjunto: o campo não se aplica, não valida nada
        $this->assertTrue($this->validator(false, 25, 99999)->isValid());
        $this->assertTrue($this->validator(false, 0, null)->isValid());
    }

    public function test_iftp_habilita_o_campo(): void
    {
        $this->assertTrue($this->validator(true, 0, 200)->isValid());
    }

    public function test_etapa_do_conjunto_habilita_o_campo_sem_iftp(): void
    {
        $this->assertTrue($this->validator(false, 39, 100)->isValid());
    }

    public function test_valor_vazio_nao_bloqueia(): void
    {
        $this->assertTrue($this->validator(true, 0, null)->isValid());
        $this->assertTrue($this->validator(false, 39, '')->isValid());
    }

    public function test_valor_fora_de_um_a_9999_e_invalido(): void
    {
        $this->assertFalse($this->validator(true, 0, 0)->isValid());
        $this->assertFalse($this->validator(true, 0, 10000)->isValid());
    }

    public function test_minimo_por_etapa_conforme_anexo_8(): void
    {
        // 39 e 40 -> 100
        $this->assertFalse($this->validator(false, 39, 99)->isValid());
        $this->assertTrue($this->validator(false, 39, 100)->isValid());
        $this->assertFalse($this->validator(false, 40, 99)->isValid());
        $this->assertTrue($this->validator(false, 40, 100)->isValid());
        // 68 e 75 -> 160
        $this->assertFalse($this->validator(false, 68, 159)->isValid());
        $this->assertTrue($this->validator(false, 68, 160)->isValid());
        $this->assertFalse($this->validator(false, 75, 159)->isValid());
        $this->assertTrue($this->validator(false, 75, 160)->isValid());
        // 73 -> 760
        $this->assertFalse($this->validator(false, 73, 759)->isValid());
        $this->assertTrue($this->validator(false, 73, 760)->isValid());
        // 67 -> 1200
        $this->assertFalse($this->validator(false, 67, 1199)->isValid());
        $this->assertTrue($this->validator(false, 67, 1200)->isValid());
    }

    public function test_regra_2_curso_tecnico_usa_carga_minima_do_curso(): void
    {
        // Código 1001 tem carga mínima 1200 (tipo 1); etapa 0 isola da regra do Anexo 8
        $this->assertFalse($this->validator(true, 0, 500, 1, 1001)->isValid());
        $this->assertTrue($this->validator(true, 0, 1200, 1, 1001)->isValid());
        // Superior a 2000 é permitido
        $this->assertTrue($this->validator(true, 0, 2500, 1, 1001)->isValid());
    }

    public function test_regra_2_curso_sem_carga_minima_cadastrada_e_valido(): void
    {
        $this->assertTrue($this->validator(true, 0, 500, 1, 999999)->isValid());
    }

    public function test_regra_3_qualificacao_profissional_entre_160_e_800(): void
    {
        $this->assertFalse($this->validator(true, 0, 159, 2)->isValid());
        $this->assertTrue($this->validator(true, 0, 160, 2)->isValid());
        $this->assertTrue($this->validator(true, 0, 800, 2)->isValid());
        $this->assertFalse($this->validator(true, 0, 801, 2)->isValid());
    }
}
