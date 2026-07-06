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
        bool $fgbAtivo = false,
        int $cod26 = 0,
        int $cod38 = 0
    ): CargaHorariaTotalValidator {
        return new CargaHorariaTotalValidator(
            iftpAtivo: $iftpAtivo,
            fgbAtivo: $fgbAtivo,
            etapaEducacenso: $etapa,
            cargaHorariaTotal: $carga,
            tipoCursoIntinerario: $tipoCurso,
            codCursoProfissional: $cod26,
            codCursoProfissionalIntinerario: $cod38,
        );
    }

    public function test_campo_nao_aplicavel_sempre_valido(): void
    {
        $this->assertTrue($this->validator(false, 25, 99999)->isValid());
        $this->assertTrue($this->validator(false, 0, null)->isValid());
    }

    public function test_habilitacao_por_iftp_ou_por_etapa(): void
    {
        $this->assertTrue($this->validator(true, 0, 200)->isValid());
        $this->assertTrue($this->validator(false, 39, null)->isValid());
        $this->assertTrue($this->validator(false, 64, null)->isValid());
        $this->assertTrue($this->validator(false, 74, null)->isValid());
    }

    public function test_valor_vazio_nao_bloqueia(): void
    {
        $this->assertTrue($this->validator(true, 0, null)->isValid());
        $this->assertTrue($this->validator(false, 74, '')->isValid());
    }

    public function test_valor_fora_de_um_a_9999_e_invalido(): void
    {
        $this->assertFalse($this->validator(true, 0, 0)->isValid());
        $this->assertFalse($this->validator(true, 0, 10000)->isValid());
        $this->assertTrue($this->validator(true, 0, 1)->isValid());
        $this->assertTrue($this->validator(true, 0, 9999)->isValid());
    }

    public function test_minimo_fixo_por_etapa(): void
    {
        $this->assertFalse($this->validator(false, 67, 1199)->isValid());
        $this->assertTrue($this->validator(false, 67, 1200)->isValid());
        $this->assertFalse($this->validator(false, 68, 159)->isValid());
        $this->assertTrue($this->validator(false, 68, 160)->isValid());
        $this->assertFalse($this->validator(false, 73, 759)->isValid());
        $this->assertTrue($this->validator(false, 73, 760)->isValid());
        $this->assertFalse($this->validator(false, 74, 2399)->isValid());
        $this->assertTrue($this->validator(false, 74, 2400)->isValid());
        $this->assertFalse($this->validator(false, 75, 159)->isValid());
        $this->assertTrue($this->validator(false, 75, 160)->isValid());
    }

    public function test_minimo_pelo_codigo_do_curso_campo_26(): void
    {
        // Cursos: 1001 exige 1200, 1000 exige 160, 1013 exige 800
        $this->assertFalse($this->validator(false, 39, 1199, cod26: 1001)->isValid());
        $this->assertTrue($this->validator(false, 39, 1200, cod26: 1001)->isValid());
        $this->assertFalse($this->validator(false, 40, 159, cod26: 1000)->isValid());
        $this->assertTrue($this->validator(false, 40, 160, cod26: 1000)->isValid());
        $this->assertFalse($this->validator(false, 64, 799, cod26: 1013)->isValid());
        $this->assertTrue($this->validator(false, 64, 800, cod26: 1013)->isValid());
        // Curso não cadastrado não impõe mínimo
        $this->assertTrue($this->validator(false, 39, 1, cod26: 999999)->isValid());
    }

    public function test_iftp_qualificacao_minimo_160_sem_teto(): void
    {
        $this->assertFalse($this->validator(true, 0, 159, tipoCurso: 2)->isValid());
        $this->assertTrue($this->validator(true, 0, 160, tipoCurso: 2)->isValid());
        $this->assertTrue($this->validator(true, 0, 801, tipoCurso: 2)->isValid());
        $this->assertTrue($this->validator(true, 0, 9999, tipoCurso: 2)->isValid());
    }

    public function test_iftp_tecnico_com_fgb_minimo_3000(): void
    {
        $this->assertFalse($this->validator(true, 25, 2999, tipoCurso: 1, fgbAtivo: true)->isValid());
        $this->assertTrue($this->validator(true, 25, 3000, tipoCurso: 1, fgbAtivo: true)->isValid());
        $this->assertTrue($this->validator(true, 27, 3000, tipoCurso: 1, fgbAtivo: true)->isValid());
    }

    public function test_iftp_tecnico_sem_fgb_minimo_pelo_codigo_do_curso_campo_38(): void
    {
        $this->assertFalse($this->validator(true, 0, 1199, tipoCurso: 1, cod38: 1001)->isValid());
        $this->assertTrue($this->validator(true, 0, 1200, tipoCurso: 1, cod38: 1001)->isValid());
        // Sem FGB na etapa 25: usa o mínimo do campo 38 (1200), não os 3000
        $this->assertFalse($this->validator(true, 25, 1199, tipoCurso: 1, fgbAtivo: false, cod38: 1001)->isValid());
        $this->assertTrue($this->validator(true, 25, 1200, tipoCurso: 1, fgbAtivo: false, cod38: 1001)->isValid());
    }

    public function test_etapa_tem_precedencia_sobre_iftp(): void
    {
        // Etapa 39 com IFTP ativo: usa o mínimo do campo 26 (1200), não do campo 38 (160)
        $this->assertFalse($this->validator(true, 39, 200, tipoCurso: 1, cod26: 1001, cod38: 1000)->isValid());
        $this->assertTrue($this->validator(true, 39, 1200, tipoCurso: 1, cod26: 1001, cod38: 1000)->isValid());
        // Etapa 74 (mínimo fixo 2400) tem precedência sobre o campo 38
        $this->assertFalse($this->validator(true, 74, 200, tipoCurso: 1, cod38: 1000)->isValid());
        $this->assertTrue($this->validator(true, 74, 2400, tipoCurso: 1, cod38: 1000)->isValid());
    }
}
