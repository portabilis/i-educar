<?php

namespace Tests\Unit\Intranet;

use clsPmieducarQuadroHorario;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

/**
 * A classe legada do quadro de horários de uma turma. As telas só usam o
 * cadastro, o detalhe e a exclusão; a edição, os filtros da listagem e as
 * recusas por falta de chave são testados direto na classe.
 */
class ClsPmieducarQuadroHorarioTest extends TestCase
{
    use DatabaseTransactions;

    private int $user;

    private int $other;

    private int $class;

    private EnrollmentScenario $scenario;

    protected function setUp(): void
    {
        parent::setUp();

        $user = LegacyUserFactory::new()->admin()->create();

        $this->user = $user->getKey();
        $this->other = LegacyUserFactory::new()->create()->getKey();
        $this->scenario = EnrollmentScenario::for($user, 2030);
        $this->class = $this->scenario->schoolClass()->getKey();
    }

    private function row(int $id): object
    {
        return DB::table('pmieducar.quadro_horario')->where('cod_quadro_horario', $id)->first();
    }

    /**
     * Um quadro gravado direto na tabela, para os testes de leitura.
     *
     * @param array<string, mixed> $attributes
     */
    private function timetable(array $attributes = []): int
    {
        return DB::table('pmieducar.quadro_horario')->insertGetId($attributes + [
            'ref_usuario_cad' => $this->user,
            'ref_cod_turma' => $this->class,
            'data_cadastro' => '2030-03-10 08:00:00',
            'ativo' => 1,
            'ano' => 2030,
        ], 'cod_quadro_horario');
    }

    public function test_cadastra_o_quadro_da_turma_no_ano(): void
    {
        $id = (new clsPmieducarQuadroHorario(ref_usuario_cad: $this->user, ref_cod_turma: $this->class, ano: 2030))->cadastra();

        $row = $this->row($id);

        $this->assertSame($this->user, $row->ref_usuario_cad);
        $this->assertSame($this->class, $row->ref_cod_turma);
        $this->assertSame(2030, $row->ano);
        $this->assertSame(1, $row->ativo);
        $this->assertNotNull($row->data_cadastro);
        $this->assertNull($row->ref_usuario_exc);
    }

    public function test_cadastra_sem_o_ano(): void
    {
        $id = (new clsPmieducarQuadroHorario(ref_usuario_cad: $this->user, ref_cod_turma: $this->class))->cadastra();

        $this->assertNull($this->row($id)->ano);
    }

    public function test_nao_cadastra_sem_turma_ou_sem_usuario(): void
    {
        $this->assertFalse((new clsPmieducarQuadroHorario(ref_usuario_cad: $this->user))->cadastra());
        $this->assertFalse((new clsPmieducarQuadroHorario(ref_cod_turma: $this->class))->cadastra());
    }

    public function test_edita_cada_campo_e_sempre_marca_a_data_de_exclusao(): void
    {
        $id = $this->timetable();
        $class = $this->scenario->schoolClass()->getKey();

        $edited = (new clsPmieducarQuadroHorario(
            cod_quadro_horario: $id,
            ref_usuario_exc: $this->other,
            ref_usuario_cad: $this->other,
            ref_cod_turma: $class,
            data_cadastro: '2031-01-02 03:04:05',
            data_exclusao: '2000-01-01',
            ativo: 0,
            ano: 2031,
        ))->edita();

        $row = $this->row($id);

        $this->assertTrue($edited);
        $this->assertSame($this->other, $row->ref_usuario_exc);
        $this->assertSame($this->other, $row->ref_usuario_cad);
        $this->assertSame($class, $row->ref_cod_turma);
        $this->assertSame('2031-01-02 03:04:05', $row->data_cadastro);
        $this->assertSame(0, $row->ativo);
        $this->assertSame(2031, $row->ano);
        // A data de exclusão informada é ignorada: a edição grava o momento atual do
        // banco, que pode estar em outro fuso que o PHP.
        $this->assertSame(DB::selectOne('select current_date::text as hoje')->hoje, substr($row->data_exclusao, 0, 10));
    }

    public function test_nao_edita_sem_o_codigo_ou_sem_o_usuario_que_altera(): void
    {
        $id = $this->timetable();

        $this->assertFalse((new clsPmieducarQuadroHorario(cod_quadro_horario: $id, ano: 2031))->edita());
        $this->assertFalse((new clsPmieducarQuadroHorario(ref_usuario_exc: $this->other, ano: 2031))->edita());
        $this->assertSame(2030, $this->row($id)->ano);
    }

    public function test_exclui_desativando_o_quadro(): void
    {
        $id = $this->timetable();

        $this->assertTrue((new clsPmieducarQuadroHorario(cod_quadro_horario: $id, ref_usuario_exc: $this->other))->excluir());

        $row = $this->row($id);

        $this->assertSame(0, $row->ativo);
        $this->assertSame($this->other, $row->ref_usuario_exc);
        $this->assertNotNull($row->data_exclusao);
    }

    public function test_nao_exclui_sem_o_codigo_ou_sem_o_usuario(): void
    {
        $id = $this->timetable();

        $this->assertFalse((new clsPmieducarQuadroHorario(cod_quadro_horario: $id))->excluir());
        $this->assertFalse((new clsPmieducarQuadroHorario(ref_usuario_exc: $this->other))->excluir());
        $this->assertSame(1, $this->row($id)->ativo);
    }

    public function test_detalhe_pelo_codigo_ou_pela_turma_so_enxerga_quadro_ativo(): void
    {
        $id = $this->timetable();

        $byId = (new clsPmieducarQuadroHorario(cod_quadro_horario: $id))->detalhe();
        $byClass = (new clsPmieducarQuadroHorario(ref_cod_turma: $this->class))->detalhe();

        $this->assertEquals($id, $byId['cod_quadro_horario']);
        $this->assertEquals($this->class, $byId['ref_cod_turma']);
        $this->assertEquals($id, $byClass['cod_quadro_horario']);
        $this->assertFalse((new clsPmieducarQuadroHorario)->detalhe());

        DB::table('pmieducar.quadro_horario')->where('cod_quadro_horario', $id)->update(['ativo' => 0]);

        $this->assertFalse((new clsPmieducarQuadroHorario(cod_quadro_horario: $id))->detalhe());
        $this->assertFalse((new clsPmieducarQuadroHorario(ref_cod_turma: $this->class))->detalhe());
    }

    public function test_existe_enxerga_tambem_o_quadro_inativo(): void
    {
        $id = $this->timetable(['ativo' => 0]);

        $this->assertNotEmpty((new clsPmieducarQuadroHorario(cod_quadro_horario: $id))->existe());
        $this->assertFalse((new clsPmieducarQuadroHorario(cod_quadro_horario: 0))->existe());
        $this->assertFalse((new clsPmieducarQuadroHorario)->existe());
    }

    public function test_lista_filtra_por_cada_parametro(): void
    {
        $class = $this->scenario->schoolClass()->getKey();

        $first = $this->timetable();
        $second = $this->timetable([
            'ref_cod_turma' => $class,
            'ref_usuario_cad' => $this->other,
            'ref_usuario_exc' => $this->other,
            'data_cadastro' => '2030-06-10 08:00:00',
            'data_exclusao' => '2030-07-10 08:00:00',
        ]);
        $inactive = $this->timetable(['ativo' => 0, 'ref_usuario_exc' => $this->other, 'data_exclusao' => '2030-04-10 08:00:00']);

        $ids = fn (...$filters) => array_map('intval', array_column((new clsPmieducarQuadroHorario)->lista(...$filters) ?: [], 'cod_quadro_horario'));
        $mine = fn (array $found) => array_values(array_intersect($found, [$first, $second, $inactive]));

        $this->assertSame([$first], $ids(int_cod_quadro_horario: $first));
        $this->assertSame([$second], $ids(int_ref_usuario_exc: $this->other));
        $this->assertSame([$first], $ids(int_ref_usuario_cad: $this->user));
        $this->assertSame([$second], $ids(int_ref_cod_turma: $class));
        $this->assertSame([$second], $mine($ids(date_data_cadastro_ini: '2030-06-01', int_ref_usuario_cad: $this->other)));
        $this->assertSame([$first], $mine($ids(date_data_cadastro_fim: '2030-06-01', int_ref_usuario_cad: $this->user)));
        $this->assertSame([$second], $mine($ids(date_data_exclusao_ini: '2030-07-01', int_ref_usuario_exc: $this->other)));
        $this->assertSame([], $mine($ids(date_data_exclusao_fim: '2030-07-01', int_ref_usuario_exc: $this->other)));
        $this->assertSame([$inactive], $ids(int_ref_usuario_exc: $this->other, int_ativo: 0));
        $this->assertFalse((new clsPmieducarQuadroHorario)->lista(int_cod_quadro_horario: 0));
    }

    public function test_lista_com_um_so_campo_devolve_os_valores_soltos(): void
    {
        $id = $this->timetable();

        $object = new clsPmieducarQuadroHorario;
        $object->setCamposLista('cod_quadro_horario');

        $this->assertEquals([$id], $object->lista(int_ref_cod_turma: $this->class));
    }
}
