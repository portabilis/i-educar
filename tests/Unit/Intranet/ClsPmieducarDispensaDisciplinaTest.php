<?php

namespace Tests\Unit\Intranet;

use App\Models\LegacyExemptionStage;
use clsPmieducarDispensaDisciplina;
use Database\Factories\LegacyDisciplineExemptionFactory;
use Database\Factories\LegacyExemptionTypeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

/**
 * O que a classe legada de dispensa de componente faz e as telas de dispensa
 * não alcançam: as datas informadas no construtor, as recusas sem os códigos,
 * os filtros da listagem e a consulta das dispensas por etapa.
 */
class ClsPmieducarDispensaDisciplinaTest extends TestCase
{
    use DatabaseTransactions;

    private int $user;

    private int $registration;

    private int $grade;

    private int $school;

    private int $arts;

    private int $botany;

    private int $medical;

    private int $report;

    protected function setUp(): void
    {
        parent::setUp();

        $user = LegacyUserFactory::new()->admin()->create();
        $scenario = EnrollmentScenario::for($user, 2030);

        $this->user = $user->getKey();
        $this->registration = $scenario->registration()->getKey();
        $this->grade = $scenario->grade->getKey();
        $this->school = $scenario->school->getKey();
        $this->arts = $scenario->discipline('Artes Visuais')->getKey();
        $this->botany = $scenario->discipline('Botânica')->getKey();
        $this->medical = LegacyExemptionTypeFactory::new()->create(['nm_tipo' => 'Atestado médico'])->getKey();
        $this->report = LegacyExemptionTypeFactory::new()->create(['nm_tipo' => 'Laudo'])->getKey();
    }

    private function exemption(int $discipline, array $attributes = []): int
    {
        return LegacyDisciplineExemptionFactory::new()->create($attributes + [
            'ref_cod_matricula' => $this->registration,
            'ref_cod_disciplina' => $discipline,
            'ref_cod_escola' => $this->school,
            'ref_cod_serie' => $this->grade,
            'ref_cod_tipo_dispensa' => $this->medical,
            'ref_usuario_cad' => $this->user,
            'ref_usuario_exc' => null,
        ])->getKey();
    }

    private function object(int $discipline, array $overrides = []): clsPmieducarDispensaDisciplina
    {
        return new clsPmieducarDispensaDisciplina(...$overrides + [
            'ref_cod_matricula' => $this->registration,
            'ref_cod_serie' => $this->grade,
            'ref_cod_escola' => $this->school,
            'ref_cod_disciplina' => $discipline,
            'ref_usuario_exc' => $this->user,
            'ref_usuario_cad' => $this->user,
            'ref_cod_tipo_dispensa' => $this->medical,
        ]);
    }

    private function row(int $id): ?object
    {
        return DB::table('pmieducar.dispensa_disciplina')->where('cod_dispensa', $id)->first();
    }

    public function test_cadastra_e_devolve_o_codigo_da_dispensa(): void
    {
        $id = $this->object($this->arts, ['observacao' => 'Atestado de 60 dias', 'ativo' => 1])->cadastra();

        $row = $this->row($id);

        $this->assertSame($this->arts, $row->ref_cod_disciplina);
        $this->assertSame($this->medical, $row->ref_cod_tipo_dispensa);
        $this->assertSame('Atestado de 60 dias', $row->observacao);
        $this->assertSame(1, $row->ativo);
        $this->assertSame($this->user, $row->ref_usuario_cad);
    }

    public function test_guarda_as_datas_e_o_codigo_informados_no_construtor(): void
    {
        $object = $this->object($this->arts, [
            'data_cadastro' => '2030-03-10 08:00:00',
            'data_exclusao' => '2030-04-10 08:00:00',
            'cod_dispensa' => 77,
        ]);

        $this->assertSame('2030-03-10 08:00:00', $object->data_cadastro);
        $this->assertSame('2030-04-10 08:00:00', $object->data_exclusao);
        $this->assertSame(77, $object->cod_dispensa);
    }

    public function test_ignora_no_construtor_o_que_nao_existe(): void
    {
        $object = new clsPmieducarDispensaDisciplina(
            ref_cod_matricula: 999999999,
            ref_cod_serie: $this->grade,
            ref_cod_escola: $this->school,
            // Componente que não é da série.
            ref_cod_disciplina: 999999999,
            ref_usuario_exc: 999999999,
            ref_usuario_cad: 999999999,
            ref_cod_tipo_dispensa: 999999999,
        );

        $this->assertNull($object->ref_cod_matricula);
        $this->assertNull($object->ref_cod_disciplina);
        $this->assertNull($object->ref_usuario_exc);
        $this->assertNull($object->ref_usuario_cad);
        $this->assertNull($object->ref_cod_tipo_dispensa);
    }

    public function test_nao_grava_nem_consulta_sem_os_codigos(): void
    {
        $empty = new clsPmieducarDispensaDisciplina;

        $this->assertFalse($empty->cadastra());
        $this->assertFalse($empty->edita());
        $this->assertFalse($empty->detalhe());
        $this->assertFalse($empty->existe());
        $this->assertFalse($empty->excluir());
    }

    public function test_edita_tipo_observacao_e_data_de_cadastro(): void
    {
        $id = $this->exemption($this->arts);

        $edited = $this->object($this->arts, [
            'ref_cod_tipo_dispensa' => $this->report,
            'observacao' => 'Laudo novo',
            'data_cadastro' => '2030-03-10 08:00:00',
            'ativo' => 1,
        ])->edita();

        $row = $this->row($id);

        $this->assertTrue($edited);
        $this->assertSame($this->report, $row->ref_cod_tipo_dispensa);
        $this->assertSame('Laudo novo', $row->observacao);
        $this->assertStringStartsWith('2030-03-10 08:00:00', $row->data_cadastro);
        $this->assertNotNull($row->data_exclusao);
    }

    public function test_detalha_e_confirma_a_existencia(): void
    {
        $id = $this->exemption($this->arts);

        $this->assertEquals($id, $this->object($this->arts)->detalhe()['cod_dispensa']);
        $this->assertNotFalse($this->object($this->arts)->existe());
        $this->assertFalse($this->object($this->botany)->existe());
    }

    public function test_exclui_a_dispensa(): void
    {
        $id = $this->exemption($this->arts);
        $kept = $this->exemption($this->botany);

        $this->object($this->arts)->excluir();

        $this->assertNull($this->row($id));
        $this->assertNotNull($this->row($kept));
    }

    public function test_lista_por_cada_filtro(): void
    {
        $arts = $this->exemption($this->arts, [
            'observacao' => 'Atestado de 60 dias',
            'data_cadastro' => '2030-03-10 08:00:00',
            'data_exclusao' => '2030-06-10 08:00:00',
            'ref_usuario_exc' => $this->user,
        ]);

        $this->exemption($this->botany, [
            'ref_cod_tipo_dispensa' => $this->report,
            'observacao' => 'Laudo permanente',
            'data_cadastro' => '2030-05-10 08:00:00',
            'data_exclusao' => '2030-08-10 08:00:00',
        ]);

        $list = fn (array $filters) => array_column((new clsPmieducarDispensaDisciplina)->lista(...$filters + [
            'int_ref_cod_matricula' => $this->registration,
        ]) ?: [], 'cod_dispensa');

        $this->assertCount(2, $list([]));

        foreach ([
            ['int_ref_cod_serie' => $this->grade, 'int_ref_cod_escola' => $this->school, 'int_ref_cod_disciplina' => $this->arts],
            ['int_ref_usuario_exc' => $this->user],
            ['int_ref_usuario_cad' => $this->user, 'int_ref_cod_tipo_dispensa' => $this->medical],
            ['date_data_cadastro_ini' => '2030-03-01', 'date_data_cadastro_fim' => '2030-04-01'],
            ['date_data_exclusao_ini' => '2030-06-01', 'date_data_exclusao_fim' => '2030-07-01'],
            ['str_observacao' => 'Atestado'],
        ] as $filters) {
            $this->assertEquals([$arts], $list($filters), json_encode($filters));
        }

        $this->assertSame([], $list(['int_ativo' => 0]));
    }

    public function test_lista_com_um_campo_so(): void
    {
        $id = $this->exemption($this->arts);

        $object = new clsPmieducarDispensaDisciplina;
        $object->setCamposLista('cod_dispensa');

        $this->assertEquals([$id], $object->lista(int_ref_cod_matricula: $this->registration));
    }

    public function test_lista_as_dispensas_da_matricula_por_etapa(): void
    {
        $partial = $this->exemption($this->arts);
        $whole = $this->exemption($this->botany);

        LegacyExemptionStage::query()->create(['ref_cod_dispensa' => $partial, 'etapa' => 1]);
        LegacyExemptionStage::query()->create(['ref_cod_dispensa' => $whole, 'etapa' => 1]);
        LegacyExemptionStage::query()->create(['ref_cod_dispensa' => $whole, 'etapa' => 2]);

        $object = new clsPmieducarDispensaDisciplina;

        $first = $object->disciplinaDispensadaEtapa($this->registration, $this->grade, $this->school, 1);
        $second = $object->disciplinaDispensadaEtapa($this->registration, $this->grade, $this->school, 2);

        $this->assertEqualsCanonicalizing([$this->arts, $this->botany], array_column($first, 'ref_cod_disciplina'));
        $this->assertEquals([$this->botany], array_column($second, 'ref_cod_disciplina'));
        $this->assertFalse($object->disciplinaDispensadaEtapa($this->registration, $this->grade, $this->school, 3));

        $object->setCamposLista('ref_cod_disciplina');

        $this->assertEquals([$this->botany], $object->disciplinaDispensadaEtapa($this->registration, $this->grade, $this->school, 2));
    }
}
