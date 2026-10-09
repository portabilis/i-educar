<?php

namespace Tests\Unit\Intranet;

use App\Models\LegacyDiscipline;
use clsPmieducarQuadroHorarioHorariosAux;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

/**
 * A classe legada do rascunho das aulas: as linhas que a tela de aulas guarda,
 * por identificador, enquanto o usuário monta o dia. A tela só cadastra e
 * limpa; a edição, a listagem e as consultas são testadas direto na classe.
 */
class ClsPmieducarQuadroHorarioHorariosAuxTest extends TestCase
{
    use DatabaseTransactions;

    private int $institution;

    private int $school;

    private int $grade;

    private int $timetable;

    private int $ana;

    private int $bia;

    private LegacyDiscipline $arts;

    private LegacyDiscipline $botany;

    protected function setUp(): void
    {
        parent::setUp();

        $user = LegacyUserFactory::new()->admin()->create();
        $scenario = EnrollmentScenario::for($user, 2030);

        $this->institution = $scenario->institution;
        $this->school = $scenario->school->getKey();
        $this->grade = $scenario->grade->getKey();
        $this->arts = $scenario->discipline('Artes');
        $this->botany = $scenario->discipline('Botânica');
        $this->ana = EmployeeScenario::named('Ana Alves', $this->institution)->employee->getKey();
        $this->bia = EmployeeScenario::named('Bia Borges', $this->institution)->employee->getKey();

        $this->timetable = DB::table('pmieducar.quadro_horario')->insertGetId([
            'ref_usuario_cad' => $user->getKey(),
            'ref_cod_turma' => $scenario->schoolClass()->getKey(),
            'data_cadastro' => now(),
            'ativo' => 1,
            'ano' => 2030,
        ], 'cod_quadro_horario');
    }

    /**
     * Um rascunho de aula de Artes da Ana, na segunda, das 8h às 9h, salvo o que vier em `$arguments`.
     *
     * @param array<string, mixed> $arguments Argumentos nomeados do construtor.
     */
    private function draft(array $arguments = []): clsPmieducarQuadroHorarioHorariosAux
    {
        return new clsPmieducarQuadroHorarioHorariosAux(...$arguments + [
            'ref_cod_quadro_horario' => $this->timetable,
            'ref_cod_disciplina' => $this->arts->getKey(),
            'ref_cod_escola' => $this->school,
            'ref_cod_serie' => $this->grade,
            'ref_cod_instituicao_servidor' => $this->institution,
            'ref_servidor' => $this->ana,
            'dia_semana' => 2,
            'hora_inicial' => '08:00',
            'hora_final' => '09:00',
            'identificador' => '20300001',
        ]);
    }

    private function row(int $sequence): ?object
    {
        return DB::table('pmieducar.quadro_horario_horarios_aux')
            ->where('ref_cod_quadro_horario', $this->timetable)
            ->where('sequencial', $sequence)
            ->first();
    }

    public function test_cadastra_o_rascunho_e_numera_em_sequencia(): void
    {
        $this->assertTrue($this->draft()->cadastra());
        $this->assertTrue($this->draft(['ref_cod_disciplina' => $this->botany->getKey(), 'identificador' => null])->cadastra());

        $first = $this->row(1);

        $this->assertSame($this->arts->getKey(), $first->ref_cod_disciplina);
        $this->assertSame($this->school, $first->ref_cod_escola);
        $this->assertSame($this->grade, $first->ref_cod_serie);
        $this->assertSame($this->institution, $first->ref_cod_instituicao_servidor);
        $this->assertSame($this->ana, $first->ref_servidor);
        $this->assertSame(2, $first->dia_semana);
        $this->assertSame('08:00:00', $first->hora_inicial);
        $this->assertSame('09:00:00', $first->hora_final);
        $this->assertSame('20300001', $first->identificador);
        $this->assertNotNull($first->data_cadastro);
        $this->assertSame($this->botany->getKey(), $this->row(2)->ref_cod_disciplina);
        $this->assertNull($this->row(2)->identificador);
    }

    public function test_nao_cadastra_sem_alguma_das_chaves(): void
    {
        $this->assertFalse($this->draft(['ref_cod_quadro_horario' => null])->cadastra());
        $this->assertFalse($this->draft(['ref_cod_disciplina' => null])->cadastra());
        $this->assertFalse($this->draft(['ref_servidor' => null])->cadastra());
        $this->assertFalse($this->draft(['dia_semana' => null])->cadastra());
        $this->assertFalse($this->draft(['hora_inicial' => null])->cadastra());
        $this->assertFalse($this->draft(['hora_final' => null])->cadastra());
    }

    public function test_ao_ser_criada_a_classe_apaga_os_rascunhos_com_mais_de_tres_horas(): void
    {
        $this->draft()->cadastra();
        $this->draft()->cadastra();

        DB::table('pmieducar.quadro_horario_horarios_aux')
            ->where('ref_cod_quadro_horario', $this->timetable)
            ->where('sequencial', 1)
            ->update(['data_cadastro' => DB::raw("NOW() - interval '4 hours'")]);

        new clsPmieducarQuadroHorarioHorariosAux;

        $this->assertNull($this->row(1));
        $this->assertNotNull($this->row(2));
    }

    public function test_edita_cada_campo_do_rascunho(): void
    {
        $this->draft()->cadastra();

        $edited = $this->draft([
            'sequencial' => 1,
            'ref_cod_disciplina' => $this->botany->getKey(),
            'ref_servidor' => $this->bia,
            'dia_semana' => 5,
            'hora_inicial' => '10:00',
            'hora_final' => '11:00',
            'identificador' => '20300002',
        ])->edita();

        $row = $this->row(1);

        $this->assertTrue($edited);
        $this->assertSame($this->botany->getKey(), $row->ref_cod_disciplina);
        $this->assertSame($this->bia, $row->ref_servidor);
        $this->assertSame(5, $row->dia_semana);
        $this->assertSame('10:00:00', $row->hora_inicial);
        $this->assertSame('11:00:00', $row->hora_final);
        $this->assertSame('20300002', $row->identificador);
    }

    public function test_nao_edita_sem_a_chave_ou_sem_nada_a_alterar(): void
    {
        $this->draft()->cadastra();

        $this->assertFalse($this->draft(['hora_inicial' => '10:00'])->edita());
        $this->assertFalse((new clsPmieducarQuadroHorarioHorariosAux($this->timetable, 1))->edita());
        $this->assertSame('08:00:00', $this->row(1)->hora_inicial);
    }

    public function test_detalhe_e_existe_pela_chave(): void
    {
        $this->draft()->cadastra();

        $detail = (new clsPmieducarQuadroHorarioHorariosAux($this->timetable, 1))->detalhe();

        $this->assertEquals($this->arts->getKey(), $detail['ref_cod_disciplina']);
        $this->assertSame('20300001', $detail['identificador']);
        $this->assertTrue((new clsPmieducarQuadroHorarioHorariosAux($this->timetable, 1))->existe());
        $this->assertFalse((new clsPmieducarQuadroHorarioHorariosAux($this->timetable, 2))->existe());
        $this->assertFalse((new clsPmieducarQuadroHorarioHorariosAux)->existe());
        $this->assertFalse((new clsPmieducarQuadroHorarioHorariosAux)->detalhe());
    }

    public function test_lista_filtra_por_cada_parametro(): void
    {
        $this->draft()->cadastra();
        $this->draft([
            'ref_cod_disciplina' => $this->botany->getKey(),
            'ref_servidor' => $this->bia,
            'dia_semana' => 4,
            'hora_inicial' => '13:00',
            'hora_final' => '14:00',
            'identificador' => '20300002',
        ])->cadastra();

        DB::table('pmieducar.quadro_horario_horarios_aux')->where('ref_cod_quadro_horario', $this->timetable)->where('sequencial', 1)->update(['data_cadastro' => DB::raw("NOW() - interval '2 hours'")]);

        // O corte sai do relógio do banco, que é o que a classe usa.
        $cutoff = DB::selectOne("SELECT (NOW() - interval '1 hour')::text AS at")->at;

        $sequences = function (...$filters) {
            $object = new clsPmieducarQuadroHorarioHorariosAux;
            $object->setOrderby('sequencial');

            return array_map('intval', array_column($object->lista(...$filters + ['int_ref_cod_escola' => $this->school]) ?: [], 'sequencial'));
        };

        $this->assertSame([1, 2], $sequences());
        $this->assertSame([2], $sequences(int_ref_cod_disciplina: $this->botany->getKey()));
        $this->assertSame([1, 2], $sequences(int_ref_cod_serie: $this->grade, int_ref_cod_instituicao_servidor: $this->institution));
        $this->assertSame([2], $sequences(int_ref_servidor: $this->bia));
        $this->assertSame([2], $sequences(int_dia_semana: 4));
        $this->assertSame([2], $sequences(time_hora_inicial_ini: '12:00'));
        $this->assertSame([1], $sequences(time_hora_inicial_fim: '12:00'));
        $this->assertSame([2], $sequences(time_hora_final_ini: '12:00'));
        $this->assertSame([1], $sequences(time_hora_final_fim: '12:00'));
        $this->assertSame([2], $sequences(str_identificador: '0002'));
        $this->assertSame([2], $sequences(str_data_cadastro_ini: $cutoff));
        $this->assertSame([1], $sequences(str_data_cadastro_fim: $cutoff));
        $this->assertSame([], $sequences(int_dia_semana: 7));
    }

    public function test_lista_com_um_so_campo_devolve_os_valores_soltos(): void
    {
        $this->draft()->cadastra();

        $object = new clsPmieducarQuadroHorarioHorariosAux;
        $object->setCamposLista('sequencial');

        $this->assertEquals([1], $object->lista(int_ref_cod_escola: $this->school));
    }

    public function test_exclui_o_rascunho_de_uma_aula_ou_todos_de_um_identificador(): void
    {
        $this->draft()->cadastra();
        $this->draft(['ref_cod_disciplina' => $this->botany->getKey()])->cadastra();
        $this->draft(['identificador' => '20300002'])->cadastra();

        $object = new clsPmieducarQuadroHorarioHorariosAux;

        $this->assertTrue($object->excluiRegistro($this->timetable, $this->grade, $this->school, $this->botany->getKey(), $this->institution, $this->ana, '20300001'));
        $this->assertNull($this->row(2));
        $this->assertNotNull($this->row(1));

        $this->assertTrue($object->excluirTodos('20300001'));
        $this->assertNull($this->row(1));
        $this->assertNotNull($this->row(3));
    }

    public function test_as_exclusoes_exigem_identificador_numerico(): void
    {
        $this->draft(['identificador' => 'abc'])->cadastra();

        $object = new clsPmieducarQuadroHorarioHorariosAux;

        $this->assertFalse($object->excluiRegistro($this->timetable, $this->grade, $this->school, $this->arts->getKey(), $this->institution, $this->ana, 'abc'));
        $this->assertFalse($object->excluirTodos('abc'));
        $this->assertNotNull($this->row(1));
    }
}
