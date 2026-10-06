<?php

namespace Tests\Unit\Intranet;

use clsPmieducarEscolaSerieDisciplina;
use Database\Factories\LegacyDisciplineDependenceFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * O que a classe legada dos componentes da série na escola faz e a tela de
 * série da escola não alcança: as etapas específicas, a edição com valores
 * próprios, o detalhe, a exclusão de um componente, a comparação com uma
 * lista de componentes e as recusas sem escola ou série.
 */
class ClsPmieducarEscolaSerieDisciplinaTest extends TestCase
{
    use DatabaseTransactions;

    private int $school;

    private int $grade;

    protected function setUp(): void
    {
        parent::setUp();

        LegacyUserFactory::new()->admin()->create();

        $link = LegacySchoolGradeFactory::new()->create();

        $this->school = $link->ref_cod_escola;
        $this->grade = $link->ref_cod_serie;
    }

    private function discipline(): int
    {
        return LegacyDisciplineFactory::new()->create()->getKey();
    }

    private function linked(array $attributes = []): int
    {
        return LegacySchoolGradeDisciplineFactory::new()->create($attributes + [
            'ref_ref_cod_escola' => $this->school,
            'ref_ref_cod_serie' => $this->grade,
        ])->ref_cod_disciplina;
    }

    private function row(int $component): ?object
    {
        return DB::table('pmieducar.escola_serie_disciplina')
            ->where('ref_ref_cod_escola', $this->school)
            ->where('ref_ref_cod_serie', $this->grade)
            ->where('ref_cod_disciplina', $component)
            ->first();
    }

    public function test_cadastra_com_etapas_especificas(): void
    {
        $component = $this->discipline();

        $created = (new clsPmieducarEscolaSerieDisciplina(
            ref_ref_cod_serie: $this->grade,
            ref_ref_cod_escola: $this->school,
            ref_cod_disciplina: $component,
            ativo: 1,
            carga_horaria: 60,
            etapas_especificas: 1,
            etapas_utilizadas: '1,3',
            anos_letivos: [2030],
        ))->cadastra();

        $row = $this->row($component);

        $this->assertTrue($created);
        $this->assertEquals(60, $row->carga_horaria);
        $this->assertSame(1, $row->etapas_especificas);
        $this->assertSame('1,3', $row->etapas_utilizadas);
        $this->assertSame('{2030}', $row->anos_letivos);
    }

    public function test_edita_com_valores_proprios(): void
    {
        $component = $this->linked();

        $edited = (new clsPmieducarEscolaSerieDisciplina(
            ref_ref_cod_serie: $this->grade,
            ref_ref_cod_escola: $this->school,
            ref_cod_disciplina: $component,
            ativo: 1,
            carga_horaria: 90,
            etapas_especificas: 1,
            etapas_utilizadas: '2,4',
            anos_letivos: [2030, 2031],
            hora_falta: 0.75,
            aulas_por_semana: 4,
        ))->edita();

        $row = $this->row($component);

        $this->assertTrue($edited);
        $this->assertEquals(90, $row->carga_horaria);
        $this->assertEquals(0.75, $row->hora_falta);
        $this->assertEquals(4, $row->aulas_por_semana);
        $this->assertSame('2,4', $row->etapas_utilizadas);
        $this->assertSame('{2030,2031}', $row->anos_letivos);
    }

    public function test_nao_grava_nem_consulta_sem_escola_e_serie(): void
    {
        $component = $this->linked();
        $orphan = new clsPmieducarEscolaSerieDisciplina(ref_ref_cod_serie: $this->grade, ref_cod_disciplina: $component);

        $this->assertFalse($orphan->cadastra());
        $this->assertFalse($orphan->edita());
        $this->assertFalse($orphan->detalhe());
        $this->assertFalse($orphan->excluir());
        $this->assertFalse($orphan->excluirTodos());
        $this->assertFalse($orphan->diferente([]));
        $this->assertFalse($orphan->excluirNaoSelecionados([$component]));
        $this->assertFalse($orphan->existeDependencia([$component]));
        $this->assertNotNull($this->row($component));
    }

    public function test_detalha_e_exclui_um_componente(): void
    {
        $component = $this->linked(['carga_horaria' => 70]);
        $other = $this->linked();

        $object = new clsPmieducarEscolaSerieDisciplina($this->grade, $this->school, $component);

        $this->assertEquals(70, $object->detalhe()['carga_horaria']);
        $this->assertTrue($object->excluir());
        $this->assertNull($this->row($component));
        $this->assertNotNull($this->row($other));
    }

    public function test_lista_por_componente_e_com_um_campo_so(): void
    {
        $component = $this->linked();
        $this->linked();

        $object = new clsPmieducarEscolaSerieDisciplina;

        $this->assertSame([$component], array_column($object->lista($this->grade, $this->school, $component), 'ref_cod_disciplina'));
        $this->assertFalse($object->lista($this->grade, $this->school, $this->discipline()));

        $object->setCamposLista('ref_cod_disciplina');

        $this->assertSame([$component], $object->lista($this->grade, $this->school, $component));
    }

    public function test_aponta_os_componentes_vinculados_que_nao_estao_na_lista(): void
    {
        $kept = $this->linked();
        $left = $this->linked();

        $different = (new clsPmieducarEscolaSerieDisciplina($this->grade, $this->school))->diferente([[$kept]]);

        $this->assertSame([$left], array_column($different, 'ref_cod_disciplina'));
    }

    public function test_acusa_dependencia_dentro_ou_fora_da_lista(): void
    {
        $withDependence = $this->linked();
        $without = $this->linked();

        LegacyDisciplineDependenceFactory::new()->create([
            'ref_cod_escola' => $this->school,
            'ref_cod_serie' => $this->grade,
            'ref_cod_disciplina' => $withDependence,
        ]);

        $object = new clsPmieducarEscolaSerieDisciplina($this->grade, $this->school);

        // Na exclusão, importa a dependência dos componentes da lista.
        $this->assertTrue($object->existeDependencia([$withDependence], exclusao: true));
        $this->assertFalse($object->existeDependencia([$without], exclusao: true));

        // Na edição, importa a dependência dos que ficaram fora da lista.
        $this->assertTrue($object->existeDependencia([$without]));
        $this->assertFalse($object->existeDependencia([$withDependence]));
    }
}
