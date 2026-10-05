<?php

namespace Tests\Unit\Intranet;

use App\Models\LegacyDiscipline;
use clsPmieducarQuadroHorarioHorarios;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

/**
 * A classe legada das aulas de um quadro de horários. A tela de aulas cobre o
 * cadastro e a edição comuns; aqui ficam o substituto, os filtros da listagem,
 * as consultas avulsas e as recusas por falta de chave.
 */
class ClsPmieducarQuadroHorarioHorariosTest extends TestCase
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
     * Uma aula de Artes da Ana, na segunda, das 8h às 9h, salvo o que vier em `$arguments`.
     *
     * @param array<string, mixed> $arguments Argumentos nomeados do construtor.
     */
    private function lesson(array $arguments = []): clsPmieducarQuadroHorarioHorarios
    {
        return new clsPmieducarQuadroHorarioHorarios(...$arguments + [
            'ref_cod_quadro_horario' => $this->timetable,
            'ref_ref_cod_serie' => $this->grade,
            'ref_ref_cod_escola' => $this->school,
            'ref_ref_cod_disciplina' => $this->arts->getKey(),
            'ref_cod_instituicao_servidor' => $this->institution,
            'ref_servidor' => $this->ana,
            'hora_inicial' => '08:00',
            'hora_final' => '09:00',
            'ativo' => 1,
            'dia_semana' => 2,
        ]);
    }

    private function row(int $sequence): ?object
    {
        return DB::table('pmieducar.quadro_horario_horarios')
            ->where('ref_cod_quadro_horario', $this->timetable)
            ->where('sequencial', $sequence)
            ->first();
    }

    public function test_cadastra_a_aula_com_substituto_e_numera_em_sequencia(): void
    {
        $this->assertTrue($this->lesson(['ref_cod_instituicao_substituto' => $this->institution, 'ref_servidor_substituto' => $this->bia])->cadastra());
        $this->assertTrue($this->lesson(['ref_ref_cod_disciplina' => $this->botany->getKey(), 'hora_inicial' => '09:00', 'hora_final' => '10:00'])->cadastra());

        $first = $this->row(1);
        $second = $this->row(2);

        $this->assertSame($this->arts->getKey(), $first->ref_cod_disciplina);
        $this->assertSame($this->ana, $first->ref_servidor);
        $this->assertSame($this->bia, $first->ref_servidor_substituto);
        $this->assertSame($this->institution, $first->ref_cod_instituicao_substituto);
        $this->assertSame('08:00:00', $first->hora_inicial);
        $this->assertSame(2, $first->dia_semana);
        $this->assertSame(1, $first->ativo);
        $this->assertSame($this->botany->getKey(), $second->ref_cod_disciplina);
        $this->assertNull($second->ref_servidor_substituto);
    }

    public function test_ignora_o_substituto_sem_a_instituicao_e_o_componente_que_nao_e_da_serie(): void
    {
        $withoutInstitution = $this->lesson(['ref_servidor_substituto' => $this->bia]);
        $foreign = $this->lesson(['ref_ref_cod_disciplina' => 999999999]);

        $this->assertNull($withoutInstitution->ref_servidor_substituto);
        $this->assertNull($foreign->ref_ref_cod_disciplina);
        $this->assertFalse($foreign->cadastra());
    }

    public function test_nao_cadastra_sem_servidor_horario_ou_dia(): void
    {
        $this->assertFalse($this->lesson(['ref_servidor' => null])->cadastra());
        $this->assertFalse($this->lesson(['hora_inicial' => null])->cadastra());
        $this->assertFalse($this->lesson(['hora_final' => null])->cadastra());
        $this->assertFalse($this->lesson(['dia_semana' => null])->cadastra());
        $this->assertFalse($this->lesson(['ref_cod_quadro_horario' => null])->cadastra());
    }

    public function test_edita_cada_campo_da_aula(): void
    {
        $this->lesson()->cadastra();

        $edited = $this->lesson([
            'sequencial' => 1,
            'ref_cod_instituicao_substituto' => $this->institution,
            'ref_servidor_substituto' => $this->ana,
            'ref_servidor' => $this->bia,
            'hora_inicial' => '10:00',
            'hora_final' => '11:00',
            'data_cadastro' => '2031-01-02 03:04:05',
            'data_exclusao' => '2000-01-01',
            'ativo' => 0,
            'dia_semana' => 5,
        ])->edita();

        $row = $this->row(1);

        $this->assertTrue($edited);
        $this->assertSame($this->bia, $row->ref_servidor);
        $this->assertSame($this->ana, $row->ref_servidor_substituto);
        $this->assertSame($this->institution, $row->ref_cod_instituicao_substituto);
        $this->assertSame('10:00:00', $row->hora_inicial);
        $this->assertSame('11:00:00', $row->hora_final);
        $this->assertSame('2031-01-02 03:04:05', $row->data_cadastro);
        $this->assertSame(0, $row->ativo);
        $this->assertSame(5, $row->dia_semana);
        // A data de exclusão informada é ignorada: a edição grava sempre o momento atual.
        $this->assertSame(now()->format('Y-m-d'), substr($row->data_exclusao, 0, 10));
    }

    public function test_editar_sem_informar_o_substituto_o_apaga(): void
    {
        $this->lesson(['ref_cod_instituicao_substituto' => $this->institution, 'ref_servidor_substituto' => $this->bia])->cadastra();

        $this->assertTrue($this->lesson(['sequencial' => 1])->edita());

        $this->assertNull($this->row(1)->ref_servidor_substituto);
        $this->assertNull($this->row(1)->ref_cod_instituicao_substituto);
    }

    public function test_nao_edita_nem_exclui_sem_o_sequencial(): void
    {
        $this->lesson()->cadastra();

        $this->assertFalse($this->lesson(['hora_inicial' => '10:00'])->edita());
        $this->assertFalse($this->lesson()->excluir());
        $this->assertFalse($this->lesson()->existe());
        $this->assertSame('08:00:00', $this->row(1)->hora_inicial);
        $this->assertSame(1, $this->row(1)->ativo);
    }

    public function test_exclui_desativando_a_aula_que_continua_existindo(): void
    {
        $this->lesson()->cadastra();

        $this->assertTrue($this->lesson(['sequencial' => 1])->excluir());
        $this->assertSame(0, $this->row(1)->ativo);
        $this->assertNotEmpty($this->lesson(['sequencial' => 1])->existe());
        $this->assertFalse($this->lesson(['sequencial' => 2])->existe());
    }

    public function test_detalhe_pela_chave_ou_pelo_horario_do_servidor_na_escola(): void
    {
        $this->lesson()->cadastra();

        $byKey = $this->lesson(['sequencial' => 1])->detalhe();
        $bySchedule = (new clsPmieducarQuadroHorarioHorarios(
            ref_cod_instituicao_servidor: $this->institution,
            ref_servidor: $this->ana,
            hora_inicial: '08:00',
            hora_final: '09:00',
            dia_semana: 2,
        ))->detalhe($this->school);

        $this->assertEquals($this->arts->getKey(), $byKey['ref_cod_disciplina']);
        $this->assertEquals('08:00:00', $byKey['hora_inicial']);
        $this->assertEquals(1, $bySchedule['sequencial']);
        $this->assertFalse((new clsPmieducarQuadroHorarioHorarios)->detalhe());
        $this->assertFalse((new clsPmieducarQuadroHorarioHorarios)->detalhe($this->school));
    }

    public function test_lista_filtra_por_cada_parametro(): void
    {
        $this->lesson(['data_cadastro' => '2030-03-10'])->cadastra();
        $this->lesson([
            'ref_ref_cod_disciplina' => $this->botany->getKey(),
            'ref_cod_instituicao_substituto' => $this->institution,
            'ref_servidor_substituto' => $this->ana,
            'ref_servidor' => $this->bia,
            'hora_inicial' => '13:00',
            'hora_final' => '14:00',
            'dia_semana' => 4,
        ])->cadastra();
        $this->lesson(['hora_inicial' => '19:00', 'hora_final' => '20:00', 'dia_semana' => 6])->cadastra();

        DB::table('pmieducar.quadro_horario_horarios')->where('ref_cod_quadro_horario', $this->timetable)->where('sequencial', 1)->update(['data_cadastro' => '2030-03-10 08:00:00']);
        DB::table('pmieducar.quadro_horario_horarios')->where('ref_cod_quadro_horario', $this->timetable)->where('sequencial', 2)->update(['data_cadastro' => '2030-06-10 08:00:00', 'data_exclusao' => '2030-07-10 08:00:00']);
        DB::table('pmieducar.quadro_horario_horarios')->where('ref_cod_quadro_horario', $this->timetable)->where('sequencial', 3)->update(['data_cadastro' => '2030-03-10 08:00:00', 'data_exclusao' => '2030-04-10 08:00:00', 'ativo' => 0]);

        $sequences = function (...$filters) {
            $object = new clsPmieducarQuadroHorarioHorarios;
            $object->setOrderby('sequencial');

            return array_map('intval', array_column($object->lista(...$filters + ['int_ref_cod_quadro_horario' => $this->timetable]) ?: [], 'sequencial'));
        };

        $this->assertSame([1, 2], $sequences());
        $this->assertSame([3], $sequences(int_ativo: 0));
        $this->assertSame([1, 2], $sequences(int_ref_ref_cod_serie: $this->grade, int_ref_ref_cod_escola: $this->school));
        $this->assertSame([2], $sequences(int_ref_ref_cod_disciplina: $this->botany->getKey()));
        $this->assertSame([2], $sequences(int_sequencial: 2));
        $this->assertSame([2], $sequences(int_ref_cod_instituicao_substituto: $this->institution));
        $this->assertSame([1, 2], $sequences(int_ref_cod_instituicao_servidor: $this->institution));
        $this->assertSame([2], $sequences(int_ref_servidor_substituto: $this->ana));
        $this->assertSame([2], $sequences(int_ref_servidor: $this->bia));
        $this->assertSame([2], $sequences(time_hora_inicial_ini: '12:00'));
        $this->assertSame([1], $sequences(time_hora_inicial_fim: '12:00'));
        $this->assertSame([2], $sequences(time_hora_final_ini: '12:00'));
        $this->assertSame([1], $sequences(time_hora_final_fim: '12:00'));
        $this->assertSame([2], $sequences(date_data_cadastro_ini: '2030-06-01'));
        $this->assertSame([1], $sequences(date_data_cadastro_fim: '2030-06-01'));
        $this->assertSame([2], $sequences(date_data_exclusao_ini: '2030-07-01'));
        $this->assertSame([3], $sequences(date_data_exclusao_fim: '2030-07-01', int_ativo: 0));
        $this->assertSame([2], $sequences(int_dia_semana: 4));
        $this->assertSame([], $sequences(int_dia_semana: 7));
    }

    public function test_lista_pode_se_restringir_ao_quadro_do_ultimo_ano_letivo_da_escola(): void
    {
        $this->lesson()->cadastra();

        $found = fn () => (new clsPmieducarQuadroHorarioHorarios)->lista(int_ref_cod_quadro_horario: $this->timetable, bool_filtrar_ano: true);

        $this->assertCount(1, $found());

        DB::table('pmieducar.quadro_horario')->where('cod_quadro_horario', $this->timetable)->update(['ano' => 2029]);

        $this->assertFalse($found());
    }

    public function test_lista_com_um_so_campo_devolve_os_valores_soltos(): void
    {
        $this->lesson()->cadastra();

        $object = new clsPmieducarQuadroHorarioHorarios;
        $object->setCamposLista('sequencial');

        $this->assertEquals([1], $object->lista(int_ref_cod_quadro_horario: $this->timetable));
    }

    public function test_substituir_servidor_troca_o_professor_em_todas_as_aulas_dele(): void
    {
        $this->lesson()->cadastra();
        $this->lesson(['ref_ref_cod_disciplina' => $this->botany->getKey(), 'dia_semana' => 3])->cadastra();

        $object = new clsPmieducarQuadroHorarioHorarios(ref_cod_instituicao_servidor: $this->institution, ref_servidor: $this->ana);

        $this->assertTrue($object->substituir_servidor($this->bia));
        $this->assertSame($this->bia, $this->row(1)->ref_servidor);
        $this->assertSame($this->bia, $this->row(2)->ref_servidor);
        $this->assertNotNull($this->row(1)->data_exclusao);
    }

    public function test_nao_substitui_por_quem_nao_e_servidor_da_instituicao_nem_sem_o_servidor_de_origem(): void
    {
        $this->lesson()->cadastra();

        $object = new clsPmieducarQuadroHorarioHorarios(ref_cod_instituicao_servidor: $this->institution, ref_servidor: $this->ana);

        $this->assertFalse($object->substituir_servidor(999999999));
        $this->assertFalse((new clsPmieducarQuadroHorarioHorarios)->substituir_servidor($this->bia));
        $this->assertSame($this->ana, $this->row(1)->ref_servidor);
    }

    public function test_excluir_todos_desativa_as_aulas_do_quadro(): void
    {
        $this->lesson()->cadastra();
        $this->lesson(['dia_semana' => 3])->cadastra();

        $this->assertFalse((new clsPmieducarQuadroHorarioHorarios)->excluirTodos());
        $this->assertSame(1, $this->row(1)->ativo);

        $this->assertTrue((new clsPmieducarQuadroHorarioHorarios(ref_cod_quadro_horario: $this->timetable))->excluirTodos());
        $this->assertSame(0, $this->row(1)->ativo);
        $this->assertSame(0, $this->row(2)->ativo);
    }

    public function test_retorna_horario_exige_todas_as_chaves(): void
    {
        $this->assertFalse((new clsPmieducarQuadroHorarioHorarios)->retornaHorario($this->institution, $this->school, $this->grade, null, 2));
    }
}
