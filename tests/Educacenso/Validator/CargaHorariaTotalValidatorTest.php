<?php

use iEducar\Modules\Educacenso\Validator\CargaHorariaTotalValidator;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->validator = function (
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
    };
});

test('campo nao aplicavel vazio e valido', function () {
    $this->assertTrue(($this->validator)(false, 25, null)->isValid());
    $this->assertTrue(($this->validator)(false, 0, null)->isValid());
});

test('campo nao aplicavel preenchido e invalido', function () {
    $validator = ($this->validator)(false, 25, 200);

    $this->assertFalse($validator->isValid());
    $this->assertSame('não deve ser preenchida para esta turma.', $validator->getMessage());
});

test('habilitacao por iftp ou por etapa', function () {
    $this->assertTrue(($this->validator)(true, 0, 200)->isValid());
    $this->assertTrue(($this->validator)(false, 39, null)->isValid());
    $this->assertTrue(($this->validator)(false, 64, null)->isValid());
    $this->assertTrue(($this->validator)(false, 74, null)->isValid());
});

test('valor vazio nao bloqueia', function () {
    $this->assertTrue(($this->validator)(true, 0, null)->isValid());
    $this->assertTrue(($this->validator)(false, 74, '')->isValid());
});

test('valor fora de um a 9999 e invalido', function () {
    $this->assertFalse(($this->validator)(true, 0, 0)->isValid());
    $this->assertFalse(($this->validator)(true, 0, 10000)->isValid());
    $this->assertTrue(($this->validator)(true, 0, 1)->isValid());
    $this->assertTrue(($this->validator)(true, 0, 9999)->isValid());
});

test('minimo fixo por etapa', function () {
    $this->assertFalse(($this->validator)(false, 67, 1199)->isValid());
    $this->assertTrue(($this->validator)(false, 67, 1200)->isValid());
    $this->assertFalse(($this->validator)(false, 68, 159)->isValid());
    $this->assertTrue(($this->validator)(false, 68, 160)->isValid());
    $this->assertFalse(($this->validator)(false, 73, 759)->isValid());
    $this->assertTrue(($this->validator)(false, 73, 760)->isValid());
    $this->assertFalse(($this->validator)(false, 74, 2399)->isValid());
    $this->assertTrue(($this->validator)(false, 74, 2400)->isValid());
    $this->assertFalse(($this->validator)(false, 75, 159)->isValid());
    $this->assertTrue(($this->validator)(false, 75, 160)->isValid());
});

test('minimo pelo codigo do curso campo 26', function () {
    // Cursos: 1001 exige 1200, 1000 exige 160, 1013 exige 800
    $this->assertFalse(($this->validator)(false, 39, 1199, cod26: 1001)->isValid());
    $this->assertTrue(($this->validator)(false, 39, 1200, cod26: 1001)->isValid());
    $this->assertFalse(($this->validator)(false, 40, 159, cod26: 1000)->isValid());
    $this->assertTrue(($this->validator)(false, 40, 160, cod26: 1000)->isValid());
    $this->assertFalse(($this->validator)(false, 64, 799, cod26: 1013)->isValid());
    $this->assertTrue(($this->validator)(false, 64, 800, cod26: 1013)->isValid());
    // Curso não cadastrado não impõe mínimo
    $this->assertTrue(($this->validator)(false, 39, 1, cod26: 999999)->isValid());
});

test('iftp qualificacao minimo 160 sem teto', function () {
    $this->assertFalse(($this->validator)(true, 0, 159, tipoCurso: 2)->isValid());
    $this->assertTrue(($this->validator)(true, 0, 160, tipoCurso: 2)->isValid());
    $this->assertTrue(($this->validator)(true, 0, 801, tipoCurso: 2)->isValid());
    $this->assertTrue(($this->validator)(true, 0, 9999, tipoCurso: 2)->isValid());
});

test('iftp tecnico com fgb minimo 3000', function () {
    $this->assertFalse(($this->validator)(true, 25, 2999, tipoCurso: 1, fgbAtivo: true)->isValid());
    $this->assertTrue(($this->validator)(true, 25, 3000, tipoCurso: 1, fgbAtivo: true)->isValid());
    $this->assertTrue(($this->validator)(true, 27, 3000, tipoCurso: 1, fgbAtivo: true)->isValid());
});

test('iftp com fgb exige 3000 independente do tipo do curso', function () {
    $this->assertFalse(($this->validator)(true, 25, 2999, tipoCurso: 2, fgbAtivo: true)->isValid());
    $this->assertTrue(($this->validator)(true, 25, 3000, tipoCurso: 2, fgbAtivo: true)->isValid());
    $this->assertFalse(($this->validator)(true, 29, 2999, tipoCurso: 2, fgbAtivo: true)->isValid());
    $this->assertTrue(($this->validator)(true, 29, 3000, tipoCurso: 2, fgbAtivo: true)->isValid());
});

test('iftp tecnico sem fgb minimo pelo codigo do curso campo 38', function () {
    $this->assertFalse(($this->validator)(true, 0, 1199, tipoCurso: 1, cod38: 1001)->isValid());
    $this->assertTrue(($this->validator)(true, 0, 1200, tipoCurso: 1, cod38: 1001)->isValid());
    // Sem formação geral básica na etapa 25: usa o mínimo do curso técnico (1200), não os 3000
    $this->assertFalse(($this->validator)(true, 25, 1199, tipoCurso: 1, fgbAtivo: false, cod38: 1001)->isValid());
    $this->assertTrue(($this->validator)(true, 25, 1200, tipoCurso: 1, fgbAtivo: false, cod38: 1001)->isValid());
});

test('etapa tem precedencia sobre iftp', function () {
    // Etapa 39 com IFTP ativo: usa o mínimo do curso da etapa (1200), não do curso do itinerário (160)
    $this->assertFalse(($this->validator)(true, 39, 200, tipoCurso: 1, cod26: 1001, cod38: 1000)->isValid());
    $this->assertTrue(($this->validator)(true, 39, 1200, tipoCurso: 1, cod26: 1001, cod38: 1000)->isValid());
    // Etapa 74 (mínimo fixo 2400) tem precedência sobre o curso do itinerário
    $this->assertFalse(($this->validator)(true, 74, 200, tipoCurso: 1, cod38: 1000)->isValid());
    $this->assertTrue(($this->validator)(true, 74, 2400, tipoCurso: 1, cod38: 1000)->isValid());
});
