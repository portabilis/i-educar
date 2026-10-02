<?php

namespace Tests\Unit\Intranet;

use App\Events\StudentCreated;
use App\Models\LegacyStudent;
use clsPmieducarAluno;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\ReligionFactory;
use Database\Factories\StudentInepFactory;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\Request\Support\StudentScenario;
use Tests\TestCase;

/**
 * O que a classe legada de aluno faz e as telas de aluno e de pessoa não
 * alcançam: o construtor, o cadastro e a edição campo a campo, cada filtro da
 * listagem, a leitura por pessoa, o INEP, o responsável e a exclusão.
 */
class ClsPmieducarAlunoTest extends TestCase
{
    use DatabaseTransactions;

    private int $user;

    private StudentScenario $ana;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = LegacyUserFactory::new()->admin()->create()->getKey();
        $this->ana = StudentScenario::named('Ana Alves', ['ref_usuario_cad' => $this->user, 'analfabeto' => 0]);
    }

    private function row(int $student): object
    {
        return DB::table('pmieducar.aluno')->where('cod_aluno', $student)->first();
    }

    public function test_construtor_so_aceita_a_pessoa_que_e_pessoa_fisica(): void
    {
        $person = StudentScenario::individual('Bia Borges');

        $withPerson = new clsPmieducarAluno(ref_idpes: $person->getKey());
        $withoutPerson = new clsPmieducarAluno(ref_idpes: 999999999);

        $this->assertSame($person->getKey(), $withPerson->ref_idpes);
        $this->assertNull($withoutPerson->ref_idpes);
    }

    public function test_construtor_guarda_os_demais_campos_conforme_o_tipo(): void
    {
        $student = new clsPmieducarAluno(
            cod_aluno: $this->ana->student->getKey(),
            ref_cod_religiao: 7,
            ref_usuario_exc: $this->user,
            ref_usuario_cad: $this->user,
            data_cadastro: '2030-02-10',
            data_exclusao: true,
            ativo: 1,
            analfabeto: 1,
            tipo_responsavel: 'm',
            aluno_estado_id: '111.222.333-4',
            autorizado_um: 'Tia Teresa',
            parentesco_um: 'Tia',
            autorizado_dois: 'Tio Tomás',
            parentesco_dois: 'Tio',
            autorizado_tres: 'Avó Ana',
            parentesco_tres: 'Avó',
            autorizado_quatro: 'Avô Abel',
            parentesco_quatro: 'Avô',
            autorizado_cinco: 'Vizinha Vera',
            parentesco_cinco: 'Vizinha',
            tipo_transporte: 2,
            rota_transporte: 'Rota 5',
            utiliza_transporte_rural: true,
        );

        $this->assertSame($this->ana->student->getKey(), $student->cod_aluno);
        $this->assertSame(7, $student->ref_cod_religiao);
        $this->assertSame('2030-02-10', $student->data_cadastro);
        $this->assertTrue($student->data_exclusao);
        $this->assertSame('m', $student->tipo_responsavel);
        $this->assertSame('Vizinha', $student->parentesco_cinco);
        $this->assertSame('Rota 5', $student->rota_transporte);
        $this->assertTrue($student->utiliza_transporte_rural);
        $this->assertSame(2, $student->tipo_transporte);
    }

    public function test_construtor_aceita_o_texto_null_como_religiao_pelo_parametro_de_beneficio(): void
    {
        // Peculiaridade: é o parâmetro do benefício, e não o da religião, que
        // libera a religião "NULL".
        $student = new clsPmieducarAluno(ref_cod_aluno_beneficio: 'NULL', ref_cod_religiao: 'NULL');

        $this->assertSame('NULL', $student->ref_cod_religiao);
    }

    public function test_cadastra_o_aluno_com_os_campos_minimos_e_avisa_o_sistema(): void
    {
        Event::fake([StudentCreated::class]);

        $person = StudentScenario::individual('Bia Borges');

        $id = (new clsPmieducarAluno(ref_idpes: $person->getKey(), ref_usuario_cad: $this->user))->cadastra();

        $this->assertIsNumeric($id);
        $this->assertSame($person->getKey(), $this->row($id)->ref_idpes);
        $this->assertSame(1, $this->row($id)->ativo);
        $this->assertFalse($this->row($id)->utiliza_transporte_rural);
        $this->assertSame(' ', $this->row($id)->tipo_responsavel);

        Event::assertDispatched(StudentCreated::class, fn (StudentCreated $event) => $event->student->getKey() === (int) $id);
    }

    public function test_cadastra_o_aluno_com_todos_os_campos(): void
    {
        Event::fake([StudentCreated::class]);

        $person = StudentScenario::individual('Bia Borges');
        $religion = ReligionFactory::new()->create();

        $student = new clsPmieducarAluno(
            ref_cod_religiao: $religion->getKey(),
            ref_usuario_cad: $this->user,
            ref_idpes: $person->getKey(),
            analfabeto: 1,
            tipo_responsavel: 'p',
            aluno_estado_id: '111.222.333-4',
            autorizado_um: 'Tia Teresa',
            parentesco_um: 'Tia',
            autorizado_dois: 'Tio Tomás',
            parentesco_dois: 'Tio',
            autorizado_tres: 'Avó Ana',
            parentesco_tres: 'Avó',
            autorizado_quatro: 'Avô Abel',
            parentesco_quatro: 'Avô',
            autorizado_cinco: 'Vizinha Vera',
            parentesco_cinco: 'Vizinha',
            tipo_transporte: 2,
            rota_transporte: 'Rota 5',
            utiliza_transporte_rural: true,
        );
        $student->emancipado = true;
        $student->recursos_prova_inep = '{1,2}';
        $student->recebe_escolarizacao_em_outro_espaco = 1;
        $student->justificativa_falta_documentacao = 2;
        $student->url_documento = '[{"url":"https://arquivos.test/rg.pdf"}]';
        $student->url_laudo_medico = '[{"url":"https://arquivos.test/laudo.pdf"}]';
        $student->codigo_sistema = 'EXT-9';
        $student->veiculo_transporte_escolar = '1';

        $row = $this->row($student->cadastra());

        $this->assertSame($religion->getKey(), $row->ref_cod_religiao);
        $this->assertSame(1, $row->analfabeto);
        $this->assertSame('p', $row->tipo_responsavel);
        $this->assertSame('111.222.333-4', $row->aluno_estado_id);
        $this->assertSame('Tia Teresa', $row->autorizado_um);
        $this->assertSame('Vizinha', $row->parentesco_cinco);
        $this->assertSame(2, $row->tipo_transporte);
        $this->assertSame('Rota 5', $row->rota_transporte);
        $this->assertTrue($row->utiliza_transporte_rural);
        $this->assertTrue($row->emancipado);
        $this->assertSame('{1,2}', $row->recursos_prova_inep);
        $this->assertSame(1, $row->recebe_escolarizacao_em_outro_espaco);
        $this->assertSame(2, $row->justificativa_falta_documentacao);
        $this->assertSame('[{"url":"https://arquivos.test/rg.pdf"}]', $row->url_documento);
        $this->assertSame('[{"url":"https://arquivos.test/laudo.pdf"}]', $row->url_laudo_medico);
        $this->assertSame('EXT-9', $row->codigo_sistema);
        $this->assertSame('{1}', $row->veiculo_transporte_escolar);
    }

    public function test_nao_cadastra_sem_a_pessoa(): void
    {
        $this->assertFalse((new clsPmieducarAluno(ref_usuario_cad: $this->user))->cadastra());
    }

    public function test_edita_cada_campo_do_aluno(): void
    {
        $person = StudentScenario::individual('Bia Borges');
        $religion = ReligionFactory::new()->create();

        $student = new clsPmieducarAluno(
            cod_aluno: $this->ana->student->getKey(),
            ref_cod_religiao: $religion->getKey(),
            ref_usuario_exc: $this->user,
            ref_usuario_cad: $this->user,
            ref_idpes: $person->getKey(),
            data_cadastro: '2030-02-10 08:00:00',
            data_exclusao: true,
            ativo: 1,
            analfabeto: 1,
            tipo_responsavel: 'r',
            aluno_estado_id: '111.222.333-4',
            autorizado_um: 'Tia Teresa',
            parentesco_um: 'Tia',
            autorizado_dois: 'Tio Tomás',
            parentesco_dois: 'Tio',
            autorizado_tres: 'Avó Ana',
            parentesco_tres: 'Avó',
            autorizado_quatro: 'Avô Abel',
            parentesco_quatro: 'Avô',
            autorizado_cinco: 'Vizinha Vera',
            parentesco_cinco: 'Vizinha',
            tipo_transporte: 1,
            rota_transporte: 'Rota 5',
            utiliza_transporte_rural: true,
        );
        $student->emancipado = true;
        $student->recursos_prova_inep = '{1}';
        $student->recebe_escolarizacao_em_outro_espaco = 1;
        $student->justificativa_falta_documentacao = 2;
        $student->url_documento = '[{"url":"https://arquivos.test/rg.pdf"}]';
        $student->url_laudo_medico = '[{"url":"https://arquivos.test/laudo.pdf"}]';
        $student->codigo_sistema = 'EXT-9';
        $student->veiculo_transporte_escolar = '1';

        $this->assertTrue($student->edita());

        $row = $this->row($this->ana->student->getKey());

        $this->assertSame($religion->getKey(), $row->ref_cod_religiao);
        $this->assertSame($person->getKey(), $row->ref_idpes);
        $this->assertSame($this->user, $row->ref_usuario_exc);
        $this->assertSame('2030-02-10 08:00:00', $row->data_cadastro);
        $this->assertNotNull($row->data_exclusao);
        $this->assertSame(1, $row->analfabeto);
        $this->assertSame('r', $row->tipo_responsavel);
        $this->assertSame('111.222.333-4', $row->aluno_estado_id);
        $this->assertSame('Avô Abel', $row->autorizado_quatro);
        $this->assertSame('Vizinha', $row->parentesco_cinco);
        $this->assertSame(1, $row->tipo_transporte);
        $this->assertSame('Rota 5', $row->rota_transporte);
        $this->assertTrue($row->utiliza_transporte_rural);
        $this->assertTrue($row->emancipado);
        $this->assertSame('{1}', $row->recursos_prova_inep);
        $this->assertSame(1, $row->recebe_escolarizacao_em_outro_espaco);
        $this->assertSame(2, $row->justificativa_falta_documentacao);
        $this->assertSame('EXT-9', $row->codigo_sistema);
        $this->assertSame('{1}', $row->veiculo_transporte_escolar);
    }

    public function test_edita_limpando_os_campos_com_o_texto_null_ou_vazio(): void
    {
        $this->ana->student->update([
            'ref_cod_religiao' => ReligionFactory::new()->create()->getKey(),
            'tipo_responsavel' => 'm',
            'autorizado_um' => 'Tia Teresa',
            'parentesco_um' => 'Tia',
            'autorizado_dois' => 'Tio Tomás',
            'parentesco_dois' => 'Tio',
            'autorizado_tres' => 'Avó Ana',
            'parentesco_tres' => 'Avó',
            'autorizado_quatro' => 'Avô Abel',
            'parentesco_quatro' => 'Avô',
            'autorizado_cinco' => 'Vizinha Vera',
            'parentesco_cinco' => 'Vizinha',
            'recursos_prova_inep' => '{1}',
            'justificativa_falta_documentacao' => 2,
            'veiculo_transporte_escolar' => '{1}',
        ]);

        // A religião "NULL" só entra pelo parâmetro do benefício (ver o teste do construtor).
        $student = new clsPmieducarAluno(
            cod_aluno: $this->ana->student->getKey(),
            ref_cod_aluno_beneficio: 'NULL',
            ref_cod_religiao: 'NULL',
            tipo_responsavel: '',
            autorizado_um: 'NULL',
            parentesco_um: 'NULL',
            autorizado_dois: 'NULL',
            parentesco_dois: 'NULL',
            autorizado_tres: 'NULL',
            parentesco_tres: 'NULL',
            autorizado_quatro: 'NULL',
            parentesco_quatro: 'NULL',
            autorizado_cinco: 'NULL',
            parentesco_cinco: 'NULL',
        );
        $student->recursos_prova_inep = null;
        $student->justificativa_falta_documentacao = null;
        $student->veiculo_transporte_escolar = null;

        $this->assertTrue($student->edita());

        $row = $this->row($this->ana->student->getKey());

        $this->assertNull($row->ref_cod_religiao);
        $this->assertSame(' ', $row->tipo_responsavel);
        $this->assertNull($row->autorizado_um);
        $this->assertNull($row->parentesco_um);
        $this->assertNull($row->autorizado_dois);
        $this->assertNull($row->parentesco_dois);
        $this->assertNull($row->autorizado_tres);
        $this->assertNull($row->parentesco_tres);
        $this->assertNull($row->autorizado_quatro);
        $this->assertNull($row->parentesco_quatro);
        $this->assertNull($row->autorizado_cinco);
        $this->assertNull($row->parentesco_cinco);
        $this->assertNull($row->recursos_prova_inep);
        $this->assertNull($row->justificativa_falta_documentacao);
        $this->assertNull($row->veiculo_transporte_escolar);
    }

    public function test_nao_edita_sem_o_codigo(): void
    {
        $this->assertFalse((new clsPmieducarAluno(ref_usuario_exc: $this->user))->edita());
    }

    public function test_lista_os_alunos_por_cada_filtro(): void
    {
        $religion = ReligionFactory::new()->create();
        $other = LegacyUserFactory::new()->create()->getKey();

        $bia = StudentScenario::named('Bia Borges', [
            'ref_cod_religiao' => $religion->getKey(),
            'ref_usuario_cad' => $other,
            'ref_usuario_exc' => $other,
            'analfabeto' => 1,
            'data_cadastro' => '2029-06-15 08:00:00',
            'data_exclusao' => '2029-12-20 08:00:00',
            'tipo_responsavel' => 'm',
        ]);
        $this->ana->student->update(['data_cadastro' => '2030-02-10 08:00:00', 'data_exclusao' => null, 'ativo' => 1]);
        $bia->student->update(['ativo' => 0]);
        $registration = LegacyRegistrationFactory::new()->create(['ref_cod_aluno' => $bia->student, 'ativo' => 1, 'ultima_matricula' => 1]);

        $only = fn ($rows) => $rows === false ? false : array_map('intval', array_column($rows, 'cod_aluno'));
        $ana = $this->ana->student->getKey();
        $bia = $bia->student->getKey();

        $this->assertSame([$ana], $only((new clsPmieducarAluno)->lista(int_cod_aluno: $ana)));
        $this->assertSame([$bia], $only((new clsPmieducarAluno)->lista(int_ref_cod_religiao: $religion->getKey())));
        $this->assertSame([$bia], $only((new clsPmieducarAluno)->lista(int_ref_usuario_exc: $other)));
        $this->assertSame([$bia], $only((new clsPmieducarAluno)->lista(int_ref_usuario_cad: $other)));
        $this->assertSame([$ana], $only((new clsPmieducarAluno)->lista(int_ref_idpes: $this->ana->individual->getKey())));
        $this->assertSame([$ana], $only((new clsPmieducarAluno)->lista(date_data_cadastro_ini: '2030-01-01', str_nome_aluno: 'Alves')));
        $this->assertSame([$bia], $only((new clsPmieducarAluno)->lista(date_data_cadastro_fim: '2029-12-31', str_nome_aluno: 'Borges')));
        $this->assertSame([$bia], $only((new clsPmieducarAluno)->lista(date_data_exclusao_ini: '2029-01-01', date_data_exclusao_fim: '2029-12-31')));
        $this->assertSame([$ana], $only((new clsPmieducarAluno)->lista(int_ativo: 1, str_nome_aluno: 'Alves')));
        $this->assertSame([$bia], $only((new clsPmieducarAluno)->lista(int_analfabeto: 1, str_nome_aluno: 'Borges')));
        $this->assertSame([$ana], $only((new clsPmieducarAluno)->lista(str_nome_aluno: 'ana alv')));
        $this->assertSame([$bia], $only((new clsPmieducarAluno)->lista(int_ref_cod_escola: $registration->ref_ref_cod_escola)));
    }

    public function test_lista_traz_o_nome_o_nome_social_e_o_total_e_ordena_pelo_nome_social(): void
    {
        $this->ana->individual->update(['nome_social' => 'Zoe Alves']);
        $bia = StudentScenario::named('Bia Alves');

        $rows = (new clsPmieducarAluno)->lista(str_nome_aluno: 'Alves');

        $this->assertSame(['Bia Alves', 'Ana Alves'], array_column($rows, 'nome_aluno'));
        $this->assertSame([null, 'Zoe Alves'], array_column($rows, 'nome_social'));
        $this->assertSame(2, $rows[0]['_total']);

        $student = new clsPmieducarAluno;
        $student->setLimite(1, 0);

        $this->assertSame([$bia->student->getKey()], array_column($student->lista(str_nome_aluno: 'Alves'), 'cod_aluno'));
        $this->assertFalse((new clsPmieducarAluno)->lista(str_nome_aluno: 'ninguém'));
    }

    public function test_defeito_a_listagem_ignora_os_campos_escolhidos(): void
    {
        // lista() redefine os campos para todos antes de montar a consulta.
        $student = new clsPmieducarAluno;
        $student->setCamposLista('cod_aluno');

        $rows = $student->lista(str_nome_aluno: 'Ana Alves');

        $this->assertArrayHasKey('ref_idpes', $rows[0]);
    }

    public function test_defeito_o_filtro_pelo_tipo_de_responsavel_nunca_vale(): void
    {
        // O tipo é uma letra e a classe só aplica o filtro quando ele é numérico.
        $this->ana->student->update(['tipo_responsavel' => 'm']);

        $rows = (new clsPmieducarAluno)->lista(str_nome_aluno: 'Ana Alves', str_tipo_responsavel: 'p');

        $this->assertSame([$this->ana->student->getKey()], array_map('intval', array_column($rows, 'cod_aluno')));

        // Com um número o filtro entra, e não acha ninguém.
        $this->assertFalse((new clsPmieducarAluno)->lista(str_nome_aluno: 'Ana Alves', str_tipo_responsavel: 1));
    }

    public function test_defeito_o_filtro_pelo_responsavel_monta_um_sql_que_o_banco_recusa(): void
    {
        // A subconsulta referencia a tabela como `aluno`, mas o apelido é `a`.
        $this->expectException(QueryException::class);

        (new clsPmieducarAluno)->lista(str_nome_responsavel: 'Marta', int_cpf_responsavel: 12345678909);
    }

    public function test_detalhe_le_pelo_codigo_ou_pela_pessoa(): void
    {
        $byCode = (new clsPmieducarAluno(cod_aluno: $this->ana->student->getKey()))->detalhe();
        $byPerson = (new clsPmieducarAluno(ref_idpes: $this->ana->individual->getKey()))->detalhe();

        $this->assertSame($this->ana->individual->getKey(), $byCode['ref_idpes']);
        $this->assertSame($this->ana->student->getKey(), $byPerson['cod_aluno']);
        $this->assertFalse((new clsPmieducarAluno)->detalhe());

        $this->assertSame(1, (new clsPmieducarAluno(cod_aluno: $this->ana->student->getKey()))->existe()[0]);
        $this->assertSame(1, (new clsPmieducarAluno(ref_idpes: $this->ana->individual->getKey()))->existe()[0]);
        $this->assertFalse((new clsPmieducarAluno)->existe());
    }

    public function test_verifica_o_inep_do_aluno(): void
    {
        StudentInepFactory::new()->create(['student_id' => $this->ana->student, 'number' => '123456789012']);

        $this->assertSame(123456789012, (new clsPmieducarAluno)->verificaInep($this->ana->student->getKey())['cod_aluno_inep']);
        $this->assertFalse((new clsPmieducarAluno)->verificaInep(StudentScenario::named('Bia Borges')->student->getKey()));
        $this->assertNull((new clsPmieducarAluno)->verificaInep());
    }

    public function test_responsavel_conforme_o_tipo(): void
    {
        $mother = $this->ana->mother('Marta Alves', ['cpf' => '12345678909']);
        $father = $this->ana->father('Pedro Alves');
        $guardian = $this->ana->guardian('Rita Ramos');

        $student = new clsPmieducarAluno(cod_aluno: $this->ana->student->getKey());

        $this->ana->guardianType('m');
        $this->assertSame('Marta Alves', $student->getResponsavelAluno()['nome_responsavel']);
        $this->assertSame('123.456.789-09', $student->getResponsavelAluno()['cpf_responsavel']);

        $this->ana->guardianType('p');
        $this->assertSame('Pedro Alves', $student->getResponsavelAluno()['nome_responsavel']);
        $this->assertSame('Não informado', $student->getResponsavelAluno()['cpf_responsavel']);

        $this->ana->guardianType('r');
        $this->assertSame('Rita Ramos', $student->getResponsavelAluno()['nome_responsavel']);
        $this->assertSame(
            '<a target="_blank" href="/intranet/atendidos_det.php?cod_pessoa=' . $guardian->getKey() . '">Rita Ramos</a>',
            $student->getResponsavelAluno(exibirUrl: true)['nome_responsavel']
        );

        $this->ana->guardianType('a');
        $this->assertSame('Pedro Alves, Marta Alves', $student->getResponsavelAluno()['nome_responsavel']);
        $this->assertSame('Não informado, 123.456.789-09', $student->getResponsavelAluno()['cpf_responsavel']);

        // Sem tipo, o primeiro que existir, na ordem pai, mãe, responsável.
        $this->ana->guardianType(null);
        $this->assertSame('Pedro Alves', $student->getResponsavelAluno()['nome_responsavel']);

        $this->assertFalse((new clsPmieducarAluno)->getResponsavelAluno());
    }

    public function test_exclui_desativando_o_aluno(): void
    {
        $this->assertTrue((new clsPmieducarAluno(cod_aluno: $this->ana->student->getKey(), ref_usuario_exc: $this->user))->excluir());

        $row = $this->row($this->ana->student->getKey());

        $this->assertSame(0, $row->ativo);
        $this->assertNotNull($row->data_exclusao);
        $this->assertSame($this->user, $row->ref_usuario_exc);

        $this->assertFalse((new clsPmieducarAluno(cod_aluno: $this->ana->student->getKey()))->excluir());
        $this->assertNull(LegacyStudent::query()->active()->find($this->ana->student->getKey()));
    }
}
