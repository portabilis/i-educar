<?php

use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * O que a classe legada de instituição faz e nenhuma tela alcança: o cadastro
 * (a tela de instituição só edita), a exclusão lógica e os atalhos de consulta.
 */
uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    // As colunas booleanas que o cadastro e a edição gravam explicitamente.
    $this->flags = [
        'gerar_historico_transferencia',
        'controlar_posicao_historicos',
        'matricula_apenas_bairro_escola',
        'restringir_historico_escolar',
        'restringir_multiplas_enturmacoes',
        'permissao_filtro_abandono_transferencia',
        'multiplas_reserva_vaga',
        'permitir_carga_horaria',
        'componente_curricular_turma',
        'reprova_dependencia_ano_concluinte',
        'reserva_integral_somente_com_renda',
        'bloqueia_matricula_serie_nao_seguinte',
        'altera_atestado_para_declaracao',
        'obrigar_campos_censo',
        'obrigar_documento_pessoa',
        'obrigar_cpf',
        'proibir_reclassificacao_educacao_infantil_primeiro_ano',
        'exigir_lancamentos_anteriores',
        'exibir_apenas_professores_alocados',
        'bloquear_vinculo_professor_sem_alocacao_escola',
        'permitir_matricula_fora_periodo_letivo',
        'ordenar_alunos_sequencial_enturmacao',
        'obrigar_telefone_pessoa',
    ];

    $this->nova = function (bool $flags): clsPmieducarInstituicao {
        $instituicao = new clsPmieducarInstituicao(
            ref_usuario_exc: $this->user,
            ref_usuario_cad: $this->user,
            ref_idtlog: 'RUA',
            ref_sigla_uf: 'ZZ',
            cep: 88900123,
            cidade: 'Cidade Alfa',
            bairro: 'Bairro Alfa',
            logradouro: 'Rua Alfa',
            numero: 742,
            complemento: 'Sala 2',
            nm_responsavel: 'Responsável Alfa',
            ddd_telefone: 48,
            telefone: 34221000,
            data_cadastro: '2026-01-10',
            data_exclusao: '2026-01-11',
            ativo: 1,
            nm_instituicao: 'Instituição Alfa',
            controlar_espaco_utilizacao_aluno: 1,
            percentagem_maxima_ocupacao_salas: 87.5,
            quantidade_alunos_metro_quadrado: 3,
            exigir_dados_socioeconomicos: $flags,
            altera_atestado_para_declaracao: $flags,
            obrigar_campos_censo: $flags,
            obrigar_documento_pessoa: $flags,
            exigir_lancamentos_anteriores: $flags,
            exibir_apenas_professores_alocados: $flags,
            bloquear_vinculo_professor_sem_alocacao_escola: $flags,
            permitir_matricula_fora_periodo_letivo: $flags,
            ordenar_alunos_sequencial_enturmacao: $flags,
            obrigar_telefone_pessoa: $flags,
            obrigar_cpf: $flags,
            proibir_reclassificacao_educacao_infantil_primeiro_ano: $flags,
        );

        $instituicao->data_base_remanejamento = '2026-04-15';
        $instituicao->data_base_transferencia = '2026-04-16';
        $instituicao->data_expiracao_reserva_vaga = '2026-04-17';
        $instituicao->data_base_matricula = '2026-02-20';
        $instituicao->data_fechamento = '2026-11-30';
        $instituicao->data_educacenso = '2026-05-27';
        $instituicao->exigir_vinculo_turma_professor = 1;
        $instituicao->coordenador_transporte = '123 - Coordenador Alfa (CPF)';
        $instituicao->orgao_regional = '00042';

        foreach ($this->flags as $flag) {
            $instituicao->{$flag} = $flags;
        }

        // A classe consulta um nome de propriedade com erro de grafia ao montar o INSERT.
        $instituicao->exigir_dados_socieconomicos = $flags;

        return $instituicao;
    };

    $this->row = function (int $id): object {
        return DB::table('pmieducar.instituicao')->where('cod_instituicao', $id)->first();
    };

    $this->user = LegacyUserFactory::new()->admin()->create()->getKey();
});

test('cadastra com todos os campos e as opcoes ligadas', function () {
    $id = ($this->nova)(flags: true)->cadastra();

    $row = ($this->row)($id);

    $this->assertSame('Instituição Alfa', $row->nm_instituicao);
    $this->assertSame('Responsável Alfa', $row->nm_responsavel);
    $this->assertSame('RUA', $row->ref_idtlog);
    $this->assertSame('ZZ', $row->ref_sigla_uf);
    $this->assertEquals(88900123, $row->cep);
    $this->assertEquals(742, $row->numero);
    $this->assertSame('Sala 2', $row->complemento);
    $this->assertEquals(48, $row->ddd_telefone);
    $this->assertEquals(34221000, $row->telefone);
    $this->assertSame('2026-04-15', $row->data_base_remanejamento);
    $this->assertSame('2026-04-16', $row->data_base_transferencia);
    $this->assertSame('2026-04-17', $row->data_expiracao_reserva_vaga);
    $this->assertSame('2026-02-20', $row->data_base_matricula);
    $this->assertSame('2026-11-30', $row->data_fechamento);
    $this->assertSame('2026-05-27', $row->data_educacenso);
    $this->assertEquals(87.5, $row->percentagem_maxima_ocupacao_salas);
    $this->assertEquals(3, $row->quantidade_alunos_metro_quadrado);
    $this->assertSame(' Coordenador Alfa ', $row->coordenador_transporte);
    $this->assertSame('00042', $row->orgao_regional);
    $this->assertSame(1, $row->ativo);

    foreach ($this->flags as $flag) {
        $this->assertTrue($row->{$flag}, "a opção [{$flag}] deveria nascer ligada");
    }
});

test('cadastra com as opcoes desligadas', function () {
    $row = ($this->row)(($this->nova)(flags: false)->cadastra());

    foreach ($this->flags as $flag) {
        $this->assertFalse($row->{$flag}, "a opção [{$flag}] deveria nascer desligada");
    }
});

test('nao cadastra sem os campos obrigatorios', function () {
    $instituicao = new clsPmieducarInstituicao(nm_instituicao: 'Instituição incompleta');

    $this->assertFalse($instituicao->canRegister());
    $this->assertFalse($instituicao->cadastra());
    $this->assertDatabaseMissing('pmieducar.instituicao', ['nm_instituicao' => 'Instituição incompleta']);
});

test('edita os campos que a tela nao envia', function () {
    $id = LegacyInstitutionFactory::new()->create(['nm_instituicao' => 'Instituição Zeta'])->getKey();

    $instituicao = ($this->nova)(flags: true);
    $instituicao->cod_instituicao = $id;
    // Na edição, e só nela, a data do Educacenso é esperada no formato brasileiro.
    $instituicao->data_educacenso = '27/05/2026';

    $this->assertTrue($instituicao->edita());

    $row = ($this->row)($id);

    $this->assertSame('Instituição Alfa', $row->nm_instituicao);
    $this->assertSame('RUA', $row->ref_idtlog);
    $this->assertSame($this->user, $row->ref_usuario_exc);
    $this->assertSame('2026-05-27', $row->data_educacenso);
    $this->assertNotNull($row->data_exclusao);
});

test('nao edita sem o codigo da instituicao', function () {
    $this->assertFalse(($this->nova)(flags: true)->edita());
});

/**
 * Defeito conhecido: a exclusão reaproveita a edição, que lê propriedades
 * que um objeto criado só com o código não tem. Nenhuma tela chama este
 * método. Quando for corrigido, este teste passa a falhar.
 */
test('defeito excluir estoura por propriedade indefinida', function () {
    $id = LegacyInstitutionFactory::new()->create()->getKey();

    $this->expectException(ErrorException::class);
    $this->expectExceptionMessage('matricula_apenas_bairro_escola');

    (new clsPmieducarInstituicao(cod_instituicao: $id))->excluir();
});

test('nao exclui sem o codigo da instituicao', function () {
    $this->assertFalse((new clsPmieducarInstituicao)->excluir());
});

test('diz se a instituicao existe', function () {
    $id = LegacyInstitutionFactory::new()->create()->getKey();

    $this->assertNotEmpty((new clsPmieducarInstituicao(cod_instituicao: $id))->existe());
    $this->assertEmpty((new clsPmieducarInstituicao(cod_instituicao: 0))->existe());
    $this->assertFalse((new clsPmieducarInstituicao)->existe());
});

test('detalhe exige um codigo numerico', function () {
    $id = LegacyInstitutionFactory::new()->create(['nm_instituicao' => 'Instituição Zeta'])->getKey();

    $this->assertSame('Instituição Zeta', (new clsPmieducarInstituicao(cod_instituicao: $id))->detalhe()['nm_instituicao']);
    $this->assertFalse((new clsPmieducarInstituicao)->detalhe());
});

test('devolve a primeira instituicao ativa', function () {
    DB::table('pmieducar.instituicao')->update(['ativo' => 0]);

    $id = LegacyInstitutionFactory::new()->create()->getKey();

    $this->assertEquals($id, (new clsPmieducarInstituicao)->primeiraAtiva()['cod_instituicao']);
});

/**
 * Defeito conhecido: sem instituição ativa a listagem devolve falso e o
 * método tenta contar esse falso.
 */
test('defeito primeira ativa estoura sem instituicao ativa', function () {
    DB::table('pmieducar.instituicao')->update(['ativo' => 0]);

    $this->expectException(TypeError::class);

    (new clsPmieducarInstituicao)->primeiraAtiva();
});

test('lista devolve falso quando nada casa', function () {
    $this->assertFalse((new clsPmieducarInstituicao)->lista(str_nm_instituicao: 'nome-que-nao-existe'));
});

test('lista as inativas quando pedido', function () {
    $id = LegacyInstitutionFactory::new()->create(['nm_instituicao' => 'Instituição Zeta', 'ativo' => 0])->getKey();

    $lista = (new clsPmieducarInstituicao)->lista(int_ativo: 0, str_nm_instituicao: 'Zeta');

    $this->assertCount(1, $lista);
    $this->assertEquals($id, $lista[0]['cod_instituicao']);
    $this->assertEquals(1, $lista[0]['_total']);
});

test('lista um campo so devolve os valores soltos', function () {
    LegacyInstitutionFactory::new()->create(['nm_instituicao' => 'Instituição Zeta']);

    $instituicao = new clsPmieducarInstituicao;
    $instituicao->setCamposLista('nm_instituicao');

    $this->assertSame(['Instituição Zeta'], $instituicao->lista(str_nm_instituicao: 'Zeta'));
});

test('recorta o trecho entre dois marcadores', function () {
    $instituicao = new clsPmieducarInstituicao;

    $this->assertSame(' Coordenador Alfa ', $instituicao->parteString('123 - Coordenador Alfa (CPF)', '-', '('));
    $this->assertSame(' Coordenador Alfa (CPF)', $instituicao->parteString('123 - Coordenador Alfa (CPF)', '-', null));
});
