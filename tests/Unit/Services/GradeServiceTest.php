<?php

use App\Services\GradeService;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * O que o serviço de série faz e as telas de série não alcançam: as guardas
 * de cada operação, as regras de avaliação gravadas na própria série e o
 * corte etário pela data base de matrícula da instituição.
 */
uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->required = function (): array {
        return [
            'ref_usuario_cad' => $this->user,
            'ref_cod_curso' => $this->course,
            'nm_serie' => 'Série Alfa',
            'etapa_curso' => 1,
            'concluinte' => 1,
            'carga_horaria' => 800,
            'dias_letivos' => 200,
        ];
    };

    $this->row = function (int $id): object {
        return DB::table('pmieducar.serie')->where('cod_serie', $id)->first();
    };

    $this->service = new GradeService;

    $user = LegacyUserFactory::new()->admin()->create();

    $this->user = $user->getKey();
    $this->institution = $user->ref_cod_instituicao;
    $this->course = LegacyCourseFactory::new()->create(['ref_cod_instituicao' => $this->institution])->getKey();
});

test('cadastra com as regras de avaliacao e os campos opcionais', function () {
    $rule = LegacyEvaluationRuleFactory::new()->create()->getKey();
    $differentiated = LegacyEvaluationRuleFactory::new()->create()->getKey();

    $grade = $this->service->create(($this->required)() + [
        'regra_avaliacao_id' => $rule,
        'regra_avaliacao_diferenciada_id' => $differentiated,
        'idade_inicial' => 6,
        'idade_final' => 7,
        'idade_ideal' => 'não numérico',
        'etapa_educacenso' => 14,
        'descricao' => 'Descrição Alfa',
        'observacao_historico' => ['não é texto'],
        'alerta_faixa_etaria' => 1,
    ]);

    $row = ($this->row)($grade->getKey());

    $this->assertSame('Série Alfa', $row->nm_serie);
    $this->assertSame($rule, $row->regra_avaliacao_id);
    $this->assertSame($differentiated, $row->regra_avaliacao_diferenciada_id);
    $this->assertEquals(6, $row->idade_inicial);
    $this->assertEquals(7, $row->idade_final);
    $this->assertNull($row->idade_ideal);
    $this->assertSame(14, $row->etapa_educacenso);
    $this->assertSame('Descrição Alfa', $row->descricao);
    $this->assertNull($row->observacao_historico);
    $this->assertTrue($row->alerta_faixa_etaria);
    $this->assertFalse($row->bloquear_matricula_faixa_etaria);
    $this->assertSame(1, $row->ativo);
});

test('nao cadastra sem os campos obrigatorios', function () {
    foreach (array_keys(($this->required)()) as $field) {
        $data = ($this->required)();
        unset($data[$field]);

        $this->assertNull($this->service->create($data), "cadastrou sem {$field}");
    }

    $this->assertDatabaseMissing('pmieducar.serie', ['nm_serie' => 'Série Alfa']);
});

test('estoura ao receber uma regra de avaliacao que nao existe', function () {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Nenhum registro encontrado');

    $this->service->create(($this->required)() + ['regra_avaliacao_id' => 999999999]);
});

test('estoura ao receber uma regra diferenciada que nao existe', function () {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Nenhum registro encontrado');

    $this->service->create(($this->required)() + ['regra_avaliacao_diferenciada_id' => 999999999]);
});

test('edita as opcoes ligadas e as regras de avaliacao', function () {
    $rule = LegacyEvaluationRuleFactory::new()->create()->getKey();
    $id = LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->course])->getKey();

    $this->assertTrue($this->service->update($id, [
        'ref_usuario_exc' => $this->user,
        'regra_avaliacao_id' => $rule,
        'regra_avaliacao_diferenciada_id' => $rule,
        'alerta_faixa_etaria' => true,
        'bloquear_matricula_faixa_etaria' => true,
        'exigir_inep' => true,
        'importar_serie_pre_matricula' => true,
        'etapa_educacenso' => 14,
        'descricao' => 'Descrição Beta',
    ]));

    $row = ($this->row)($id);

    $this->assertSame($this->user, $row->ref_usuario_exc);
    $this->assertSame($rule, $row->regra_avaliacao_id);
    $this->assertSame($rule, $row->regra_avaliacao_diferenciada_id);
    $this->assertTrue($row->alerta_faixa_etaria);
    $this->assertTrue($row->bloquear_matricula_faixa_etaria);
    $this->assertTrue($row->exigir_inep);
    $this->assertTrue($row->importar_serie_pre_matricula);
    $this->assertSame(14, $row->etapa_educacenso);
    $this->assertSame('Descrição Beta', $row->descricao);
});

test('na edicao o que nao veio preserva ou limpa conforme o campo', function () {
    $rule = LegacyEvaluationRuleFactory::new()->create()->getKey();
    $grade = LegacyGradeFactory::new()->create([
        'ref_cod_curso' => $this->course,
        'nm_serie' => 'Série Gama',
        'carga_horaria' => 800,
        'idade_inicial' => 6,
        'idade_final' => 7,
        'regra_avaliacao_id' => $rule,
        'regra_avaliacao_diferenciada_id' => $rule,
        'alerta_faixa_etaria' => true,
    ]);

    $this->assertTrue($this->service->update($grade->getKey(), [
        'ref_usuario_exc' => $this->user,
        'ref_cod_curso' => 'abc',
        'nm_serie' => null,
    ]));

    $row = ($this->row)($grade->getKey());

    // Os campos de identificação ficam como estavam.
    $this->assertSame($this->course, $row->ref_cod_curso);
    $this->assertSame('Série Gama', $row->nm_serie);
    $this->assertEquals(800, $row->carga_horaria);
    $this->assertSame($rule, $row->regra_avaliacao_id);

    // As idades, a regra diferenciada e as opções são limpas.
    $this->assertNull($row->idade_inicial);
    $this->assertNull($row->idade_final);
    $this->assertNull($row->regra_avaliacao_diferenciada_id);
    $this->assertFalse($row->alerta_faixa_etaria);
});

test('nao edita nem exclui sem o codigo e o usuario', function () {
    $id = LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->course, 'nm_serie' => 'Série Delta'])->getKey();

    $this->assertFalse($this->service->update('abc', ['ref_usuario_exc' => $this->user, 'nm_serie' => 'Outra']));
    $this->assertFalse($this->service->update($id, ['nm_serie' => 'Outra']));
    $this->assertFalse($this->service->delete('abc', $this->user));
    $this->assertFalse($this->service->delete($id, 'abc'));

    $row = ($this->row)($id);

    $this->assertSame('Série Delta', $row->nm_serie);
    $this->assertSame(1, $row->ativo);
});

test('a exclusao so desativa a serie', function () {
    $id = LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->course, 'idade_inicial' => 6, 'idade_final' => 7])->getKey();

    $this->assertTrue($this->service->delete($id, $this->user));

    $row = ($this->row)($id);

    $this->assertSame(0, $row->ativo);
    $this->assertSame($this->user, $row->ref_usuario_exc);
    $this->assertNotNull($row->data_exclusao);
    $this->assertEquals(6, $row->idade_inicial);
    $this->assertEquals(7, $row->idade_final);
});

test('confere o corte etario pela data base da instituicao', function () {
    DB::table('pmieducar.instituicao')->where('cod_instituicao', $this->institution)->update(['data_base_matricula' => '1990-03-31']);

    $id = LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->course, 'idade_inicial' => 6, 'idade_final' => 7])->getKey();

    // Em 31/03/2026: quem nasceu em 2020 tem 6 anos (ou 5, depois de março); em 2015, tem 10.
    $this->assertTrue($this->service->isAgeWithinRange($id, '2020-01-15', 2026));
    $this->assertTrue($this->service->isAgeWithinRange($id, new DateTime('2019-01-15'), 2026));
    $this->assertFalse($this->service->isAgeWithinRange($id, '2020-06-15', 2026));
    $this->assertFalse($this->service->isAgeWithinRange($id, '2015-06-15', 2026));
});

test('sem data base de matricula ou sem a serie qualquer idade serve', function () {
    DB::table('pmieducar.instituicao')->where('cod_instituicao', $this->institution)->update(['data_base_matricula' => null]);

    $id = LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->course, 'idade_inicial' => 6, 'idade_final' => 7])->getKey();

    $this->assertTrue($this->service->isAgeWithinRange($id, '1980-06-15', 2026));
    $this->assertTrue($this->service->isAgeWithinRange(999999999, '1980-06-15', 2026));
});
