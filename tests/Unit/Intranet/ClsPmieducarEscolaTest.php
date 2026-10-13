<?php

use Database\Factories\DistrictFactory;
use Database\Factories\EducacensoInstitutionFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyOrganizationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolCourseFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\RegionalTypeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * O que a classe legada de escola aceita e o formulário de escola não envia:
 * as colunas antigas de equipamentos e de quantidades de matrículas, os códigos
 * que o formulário só preenche por busca, e os atalhos de consulta.
 */
uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    /**
     * Uma escola com todas as propriedades que a classe sabe gravar.
     *
     * @return array{clsPmieducarEscola, array<string, mixed>}
     */
    $this->completa = function (int $value): array {
        $escola = new clsPmieducarEscola(
            ref_usuario_cad: $this->user,
            ref_usuario_exc: $this->user,
            ref_cod_instituicao: $this->institution,
            zona_localizacao: 1,
            ref_idpes: LegacyOrganizationFactory::new()->create()->idpes,
            sigla: 'ECLS',
            data_cadastro: '2026-01-10',
            data_exclusao: '2026-01-11',
            bloquear_lancamento_diario_anos_letivos_encerrados: 1,
            utiliza_regra_diferenciada: true,
        );

        // Colunas numéricas sem regra própria: o que entra é o que fica.
        $expected = array_fill_keys([
            'acesso',
            'antenas_parabolicas',
            'atividade_complementar',
            'cargo_gestor',
            'categoria_escola_privada',
            'codigo_inep_escola_compartilhada',
            'codigo_inep_escola_compartilhada2',
            'codigo_inep_escola_compartilhada3',
            'codigo_inep_escola_compartilhada4',
            'codigo_inep_escola_compartilhada5',
            'codigo_inep_escola_compartilhada6',
            'computadores',
            'computadores_administrativo',
            'computadores_alunos',
            'conveniada_com_poder_publico',
            'copiadoras',
            'faxs',
            'fundamental_ciclo',
            'impressoras',
            'impressoras_multifuncionais',
            'inep_escola_sede',
            'maquinas_fotograficas',
            'materiais_didaticos_especificos',
            'qtd_agronomos_horticultores',
            'qtd_apoio_pedagogico',
            'qtd_assistente_social',
            'qtd_atendimento_educacional_especializado',
            'qtd_auxiliar_administrativo',
            'qtd_auxiliar_servicos_gerais',
            'qtd_bibliotecarios',
            'qtd_bombeiro',
            'qtd_coordenador_turno',
            'qtd_edu_eja_ensino_fund',
            'qtd_edu_eja_ensino_med',
            'qtd_edu_especial_classe_especial_int',
            'qtd_edu_especial_classe_especial_par',
            'qtd_ensino_regular_creche_int',
            'qtd_ensino_regular_creche_par',
            'qtd_ensino_regular_ensino_fund_anos_finais_int',
            'qtd_ensino_regular_ensino_fund_anos_finais_par',
            'qtd_ensino_regular_ensino_fund_anos_iniciais_int',
            'qtd_ensino_regular_ensino_fund_anos_iniciais_par',
            'qtd_ensino_regular_ensino_med_anos_iniciais_int',
            'qtd_ensino_regular_ensino_med_anos_iniciais_par',
            'qtd_ensino_regular_pre_escola_int',
            'qtd_ensino_regular_pre_escola_par',
            'qtd_fonoaudiologo',
            'qtd_matriculas_atividade_complementar',
            'qtd_nutricionistas',
            'qtd_orientador_comunitario',
            'qtd_profissionais_preparacao',
            'qtd_psicologo',
            'qtd_revisor_braile',
            'qtd_segurancas',
            'qtd_tecnicos',
            'qtd_tradutor_interprete_libras_outro_ambiente',
            'retroprojetores',
            'total_funcionario',
            'videocassetes',
        ], $value) + [
            'ref_idpes_gestor' => LegacyIndividualFactory::new()->create()->idpes,
            'ref_idpes_secretario_escolar' => LegacyIndividualFactory::new()->create()->idpes,
            'codigo_ies' => EducacensoInstitutionFactory::new()->create()->getKey(),
            'regional_type_id' => RegionalTypeFactory::new()->create()->getKey(),
            'decreto_criacao' => "Decreto {$value}",
            'lei_conclusao_ensino_medio' => "Lei {$value}",
            'email_gestor' => "gestor{$value}@escola.test",
            'cnpj_mantenedora_principal' => '11222333000181',
            'iddis' => DistrictFactory::new()->create()->getKey(),
        ];

        foreach ($expected as $property => $propertyValue) {
            $escola->{$property} = $propertyValue;
        }

        // Vetores do Postgres: a classe recebe a lista já como texto.
        $escola->mantenedora_escola_privada = '1';
        $escola->organizacao_ensino = '1';

        return [$escola, $expected + ['mantenedora_escola_privada' => '{1}', 'organizacao_ensino' => '{1}']];
    };

    $this->row = function (int $id): object {
        return DB::table('pmieducar.escola')->where('cod_escola', $id)->first();
    };

    $user = LegacyUserFactory::new()->admin()->create();

    $this->user = $user->getKey();
    $this->institution = $user->ref_cod_instituicao;
});

test('cadastra as colunas que o formulario nao envia', function () {
    [$escola, $expected] = ($this->completa)(2);

    $id = $escola->cadastra();
    $row = ($this->row)($id);

    foreach ($expected as $column => $value) {
        $this->assertEquals($value, $row->{$column}, "a coluna [{$column}] não foi gravada no cadastro");
    }

    $this->assertSame('ECLS', $row->sigla);
    $this->assertSame(1, $row->ativo);
    $this->assertSame(1, $row->bloquear_lancamento_diario_anos_letivos_encerrados);
    $this->assertSame($this->user, $row->ref_usuario_exc);
    $this->assertTrue($row->utiliza_regra_diferenciada);
});

test('edita as colunas que o formulario nao envia', function () {
    $id = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->institution])->getKey();

    [$escola, $expected] = ($this->completa)(3);
    $escola->cod_escola = $id;

    $this->assertTrue($escola->edita());

    $row = ($this->row)($id);

    foreach (array_diff_key($expected, ['mantenedora_escola_privada' => true, 'organizacao_ensino' => true]) as $column => $value) {
        $this->assertEquals($value, $row->{$column}, "a coluna [{$column}] não foi gravada na edição");
    }

    $this->assertSame($this->user, $row->ref_usuario_cad);
    $this->assertSame('2026-01-10 00:00:00', $row->data_cadastro);
    $this->assertTrue($row->utiliza_regra_diferenciada);
});

test('nao cadastra sem os campos obrigatorios', function () {
    ob_start();
    $result = (new clsPmieducarEscola(ref_usuario_cad: $this->user))->cadastra();
    $debug = ob_get_clean();

    $this->assertFalse($result);
    // Defeito conhecido: a recusa escreve na saída uma linha de depuração.
    $this->assertNotSame('', $debug);
});

test('nao edita nem exclui sem o codigo da escola', function () {
    $this->assertFalse((new clsPmieducarEscola)->edita());
    $this->assertFalse((new clsPmieducarEscola)->excluir());
});

test('detalhe e existencia exigem um codigo numerico', function () {
    $school = LegacySchoolFactory::new()->withName('Escola Alfa')->create(['ref_cod_instituicao' => $this->institution]);

    $this->assertSame('Escola Alfa', (new clsPmieducarEscola($school->getKey()))->detalhe()['nome']);
    $this->assertNotEmpty((new clsPmieducarEscola($school->getKey()))->existe());
    $this->assertEmpty((new clsPmieducarEscola(0))->existe());
    $this->assertFalse((new clsPmieducarEscola)->detalhe());
    $this->assertFalse((new clsPmieducarEscola)->existe());
});

test('lista por cada filtro', function () {
    $school = LegacySchoolFactory::new()->withName('Escola Alfa')->create([
        'ref_cod_instituicao' => $this->institution,
        'ref_usuario_cad' => $this->user,
        'ref_usuario_exc' => $this->user,
        'sigla' => 'EALFA',
        'zona_localizacao' => 2,
    ]);
    $id = $school->getKey();

    $found = static fn ($lista) => is_array($lista) ? array_column($lista, 'cod_escola') : [];

    $this->assertContains($id, $found((new clsPmieducarEscola)->lista(int_cod_escola: $id)));
    $this->assertContains($id, $found((new clsPmieducarEscola)->lista(int_ref_usuario_cad: $this->user, int_ref_usuario_exc: $this->user)));
    $this->assertContains($id, $found((new clsPmieducarEscola)->lista(zona_localizacao: 2, int_ref_idpes: $school->ref_idpes)));
    $this->assertContains($id, $found((new clsPmieducarEscola)->lista(str_sigla: 'EALFA', int_ativo: 1)));
    $this->assertContains($id, $found((new clsPmieducarEscola)->lista(str_nome: 'escola alfa')));
    $this->assertSame([], $found((new clsPmieducarEscola)->lista(str_sigla: 'sigla-que-nao-existe')));

    $school->update(['ativo' => 0]);

    $this->assertContains($id, $found((new clsPmieducarEscola)->lista(int_cod_escola: $id, int_ativo: 0)));
    $this->assertNotContains($id, $found((new clsPmieducarEscola)->lista(int_cod_escola: $id)));
});

test('lista separa escolas com e sem curso', function () {
    $with = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->institution]);
    $without = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->institution]);
    LegacySchoolCourseFactory::new()->create(['ref_cod_escola' => $with]);

    $found = static fn ($lista) => is_array($lista) ? array_column($lista, 'cod_escola') : [];

    $semCurso = $found((new clsPmieducarEscola)->lista(int_ref_cod_instituicao: $this->institution, escola_sem_avaliacao: true));
    $comCurso = $found((new clsPmieducarEscola)->lista(int_ref_cod_instituicao: $this->institution, escola_sem_avaliacao: false));

    $this->assertContains($without->getKey(), $semCurso);
    $this->assertNotContains($with->getKey(), $semCurso);
    $this->assertContains($with->getKey(), $comCurso);
    $this->assertNotContains($without->getKey(), $comCurso);
});

test('lista so as escolas do usuario de nivel escola', function () {
    $linked = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->institution]);
    $other = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->institution]);
    $user = LegacyUserFactory::new()->school()->withSchool($linked)->create();

    // A classe descobre o nível pelo usuário guardado na sessão legada.
    $_SESSION['id_pessoa'] = $user->getKey();

    $lista = (new clsPmieducarEscola)->lista(int_ref_cod_instituicao: $this->institution, cod_usuario: $user->getKey());

    $this->assertSame([$linked->getKey()], array_column($lista, 'cod_escola'));
    $this->assertNotContains($other->getKey(), array_column($lista, 'cod_escola'));
});

test('lista um campo so devolve os valores soltos', function () {
    LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'sigla' => 'EALFA']);

    // Só funciona com um campo que os filtros também usam: a consulta
    // externa filtra sempre por "ativo", e o campo pedido é o único que a
    // interna devolve.
    $escola = new clsPmieducarEscola;
    $escola->setCamposLista('ativo');

    $this->assertContains(1, $escola->lista());
});

test('diz se a escola tem turmas do fundamental em ciclos no ano', function () {
    $empty = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->institution]);
    $schoolClass = LegacySchoolClassFactory::new()->create(['etapa_educacenso' => 4, 'ano' => now()->year]);

    $this->assertFalse((bool) (new clsPmieducarEscola($empty->getKey()))->possuiTurmasDoEnsinoFundamentalEmCiclos());
    $this->assertTrue((bool) (new clsPmieducarEscola($schoolClass->ref_ref_cod_escola))->possuiTurmasDoEnsinoFundamentalEmCiclos());
});
