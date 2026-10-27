<?php

use App\Menu;
use App\Models\LegacyInstitution;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\MenuFactory;
use Database\Factories\StateFactory;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\LegacyFields;

/**
 * As caixas de seleção do formulário. Cada uma vira uma coluna booleana (ou 0/1)
 * da instituição: marcada quando o campo vem no POST, desmarcada quando não vem.
 */
const INSTITUICAO_CAIXAS = [
    'exigir_vinculo_turma_professor',
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
    'bloqueia_matricula_serie_nao_seguinte',
    'reserva_integral_somente_com_renda',
    'controlar_espaco_utilizacao_aluno',
    'altera_atestado_para_declaracao',
    'exigir_dados_socioeconomicos',
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

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->id = $this->user->ref_cod_instituicao;
    $this->url = '/intranet/educar_instituicao_cad.php?cod_instituicao=' . $this->id;

    // A tela monta o SQL sem escapar os textos, então um apóstrofo vindo do
    // faker quebraria qualquer gravação; os textos partem de valores fixos.
    $this->texts = [
        'nm_instituicao' => 'Instituição Alfa',
        'nm_responsavel' => 'Responsável Alfa',
        'logradouro' => 'Rua Alfa',
        'bairro' => 'Bairro Alfa',
        'cidade' => 'Cidade Alfa',
    ];

    $this->form = fn (array $overrides = []) => LegacyFields::from(
        $this->get($this->url)->assertOk()->getContent(),
        $overrides + $this->texts,
    );

    $this->row = fn () => DB::table('pmieducar.instituicao')->where('cod_instituicao', $this->id)->first();
});

test('abre o formulário em edição com os dados da instituição', function () {
    $institution = LegacyInstitution::query()->findOrFail($this->id);

    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent());

    expect($payload['tipoacao'])->toBe('Editar')
        ->and($payload['cod_instituicao'])->toBe((string) $this->id)
        ->and($payload['nm_instituicao'])->toBe($institution->nm_instituicao)
        ->and($payload['data_base'])->toBe('31/03')
        ->and($payload['data_fechamento'])->toBe('31/03');
});

test('trata uma instituição inexistente como cadastro novo', function () {
    $payload = LegacyFields::from($this->get('/intranet/educar_instituicao_cad.php?cod_instituicao=0')->assertOk()->getContent());

    expect($payload['tipoacao'])->toBe('Novo')
        ->and($payload['nm_instituicao'])->toBe('');
});

test('nega o acesso a quem não tem a tela de instituição liberada', function () {
    // Fora do nível de administrador, a instituição precisa estar ativa no
    // i-Educar para o usuário não ser desviado para a tela de suspensão.
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->id, 'active_on_ieducar' => true]);

    $this->actingAs(LegacyUserFactory::new()->institutional()->create());

    $this->get($this->url)->assertRedirect('index.php?negado=1&err=1');
});

test('volta para a listagem quando o usuário só pode visualizar instituição', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->id, 'active_on_ieducar' => true]);

    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(559, modify: false, remove: false)->create());

    $this->get($this->url)->assertRedirect('educar_instituicao_lst.php');
});

test('a ação de cadastrar só volta para a listagem, sem criar instituição', function () {
    $before = LegacyInstitution::query()->count();

    $this->post('/intranet/educar_instituicao_cad.php', ['tipoacao' => 'Novo'] + $this->texts)
        ->assertRedirect('educar_instituicao_lst.php');

    expect(LegacyInstitution::query()->count())->toBe($before);
});

test('a ação de excluir só volta para a listagem, sem desativar a instituição', function () {
    $this->post($this->url, ['tipoacao' => 'Excluir'] + ($this->form)())
        ->assertRedirect('educar_instituicao_lst.php');

    expect(($this->row)()->ativo)->toBe(1);
});

test('grava nome, endereço e contato', function () {
    StateFactory::new()->create(['abbreviation' => 'ZZ']);

    $payload = ($this->form)([
        'cep' => '88900-123',
        'numero' => '742',
        'complemento' => 'Sala 2',
        'ref_sigla_uf' => 'ZZ',
        'ddd_telefone' => '48',
        'telefone' => '34221000',
    ]);

    $this->post($this->url, $payload)->assertRedirect('educar_instituicao_lst.php');

    expect(($this->row)())
        ->nm_instituicao->toBe('Instituição Alfa')
        ->nm_responsavel->toBe('Responsável Alfa')
        ->logradouro->toBe('Rua Alfa')
        ->bairro->toBe('Bairro Alfa')
        ->cidade->toBe('Cidade Alfa')
        ->cep->toEqual(88900123)
        ->numero->toEqual(742)
        ->complemento->toBe('Sala 2')
        ->ref_sigla_uf->toBe('ZZ')
        ->ddd_telefone->toEqual(48)
        ->telefone->toEqual(34221000)
        ->ref_usuario_cad->toBe($this->user->getKey());
});

test('limpa os campos opcionais enviados em branco', function () {
    DB::table('pmieducar.instituicao')->where('cod_instituicao', $this->id)->update([
        'numero' => 10,
        'complemento' => 'Fundos',
        'ddd_telefone' => 48,
        'telefone' => 34221000,
        'percentagem_maxima_ocupacao_salas' => 80,
        'quantidade_alunos_metro_quadrado' => 2,
    ]);

    $payload = ($this->form)([
        'numero' => '',
        'complemento' => '',
        'ddd_telefone' => '',
        'telefone' => '',
        'percentagem_maxima_ocupacao_salas' => '',
        'quantidade_alunos_metro_quadrado' => '',
        'data_base_remanejamento' => '',
        'data_base_transferencia' => '',
        'data_expiracao_reserva_vaga' => '',
        'data_base' => '',
        'data_fechamento' => '',
        'data_educacenso' => '',
    ]);

    $this->post($this->url, $payload)->assertRedirect('educar_instituicao_lst.php');

    expect(($this->row)())
        ->percentagem_maxima_ocupacao_salas->toBeNull()
        ->quantidade_alunos_metro_quadrado->toBeNull()
        ->data_base_remanejamento->toBeNull()
        ->data_base_transferencia->toBeNull()
        ->data_expiracao_reserva_vaga->toBeNull()
        ->data_base_matricula->toBeNull()
        ->data_fechamento->toBeNull()
        ->data_educacenso->toBeNull();
});

test('converte as datas e o percentual do formato brasileiro', function () {
    $payload = ($this->form)([
        'data_base_remanejamento' => '15/04/2026',
        'data_base_transferencia' => '16/04/2026',
        'data_expiracao_reserva_vaga' => '17/04/2026',
        'data_base' => '20/02',
        'data_fechamento' => '30/11',
        'data_educacenso' => '27/05/2026',
        'percentagem_maxima_ocupacao_salas' => '87,5',
        'quantidade_alunos_metro_quadrado' => '3',
    ]);

    $this->post($this->url, $payload)->assertRedirect('educar_instituicao_lst.php');

    $row = ($this->row)();

    expect($row)
        ->data_base_remanejamento->toBe('2026-04-15')
        ->data_base_transferencia->toBe('2026-04-16')
        ->data_expiracao_reserva_vaga->toBe('2026-04-17')
        ->data_educacenso->toBe('2026-05-27')
        ->percentagem_maxima_ocupacao_salas->toEqual(87.5)
        ->quantidade_alunos_metro_quadrado->toEqual(3);

    // As duas datas de dia e mês são gravadas com um ano qualquer; o que a
    // tela usa e mostra de volta é só o dia e o mês.
    expect(substr($row->data_base_matricula, 5))->toBe('02-20')
        ->and(substr($row->data_fechamento, 5))->toBe('11-30');
});

test('marca todas as caixas de seleção', function () {
    $payload = ($this->form)(array_fill_keys(INSTITUICAO_CAIXAS, 'on'));

    $this->post($this->url, $payload)->assertRedirect('educar_instituicao_lst.php');

    $row = ($this->row)();

    foreach (INSTITUICAO_CAIXAS as $field) {
        expect((bool) $row->{$field})->toBeTrue("a caixa [{$field}] deveria ter sido marcada");
    }
});

test('desmarca todas as caixas de seleção', function () {
    DB::table('pmieducar.instituicao')->where('cod_instituicao', $this->id)->update(
        array_fill_keys(array_diff(INSTITUICAO_CAIXAS, ['exigir_vinculo_turma_professor', 'controlar_espaco_utilizacao_aluno']), true)
        + ['exigir_vinculo_turma_professor' => 1, 'controlar_espaco_utilizacao_aluno' => 1]
    );

    $payload = array_diff_key(($this->form)(), array_flip(INSTITUICAO_CAIXAS));

    $this->post($this->url, $payload)->assertRedirect('educar_instituicao_lst.php');

    $row = ($this->row)();

    foreach (INSTITUICAO_CAIXAS as $field) {
        expect((bool) $row->{$field})->toBeFalse("a caixa [{$field}] deveria ter sido desmarcada");
    }
});

test('oferece e grava o órgão regional de ensino da UF da instituição', function () {
    StateFactory::new()->create(['abbreviation' => 'ZZ']);
    DB::table('pmieducar.instituicao')->where('cod_instituicao', $this->id)->update(['ref_sigla_uf' => 'ZZ']);
    DB::table('modules.educacenso_orgao_regional')->insert(['sigla_uf' => 'ZZ', 'codigo' => '00042']);
    cache()->flush();

    $payload = ($this->form)(['orgao_regional' => '00042']);

    $this->post($this->url, $payload)->assertRedirect('educar_instituicao_lst.php');

    expect(($this->row)()->orgao_regional)->toBe('00042');
});

test('guarda o nome do coordenador de transporte tirado do texto da busca', function () {
    $payload = ($this->form)(['pessoa_coordenador_transporte' => '123 - Coordenador Alfa (CPF: 000.000.001-91)']);

    $this->post($this->url, $payload)->assertRedirect('educar_instituicao_lst.php');

    // Fica o trecho entre o hífen e o parêntese, com os espaços das pontas.
    expect(($this->row)()->coordenador_transporte)->toBe(' Coordenador Alfa ');
});

test('guarda o texto inteiro do coordenador quando ele não vem no formato da busca', function () {
    $payload = ($this->form)(['pessoa_coordenador_transporte' => 'Coordenador Beta']);

    $this->post($this->url, $payload)->assertRedirect('educar_instituicao_lst.php');

    expect(($this->row)()->coordenador_transporte)->toBe('Coordenador Beta');
});

test('troca os menus de atestado por declaração conforme a caixa da instituição', function () {
    // A troca ignora menus sem o código antigo: o filtro é um NOT IN sobre ele.
    $menu = MenuFactory::new()->create(['title' => 'Atestados de frequência', 'description' => 'Atestados', 'old' => 55555]);

    $this->post($this->url, ($this->form)(['altera_atestado_para_declaracao' => 'on']))
        ->assertRedirect('educar_instituicao_lst.php');

    expect(Menu::query()->find($menu->getKey())->title)->toBe('Declarações de frequência');

    $payload = array_diff_key(($this->form)(), ['altera_atestado_para_declaracao' => true]);

    $this->post($this->url, $payload)->assertRedirect('educar_instituicao_lst.php');

    expect(Menu::query()->find($menu->getKey())->title)->toBe('Atestados de frequência');
});

test('o detalhe mostra os dados novos logo depois da edição', function () {
    // O detalhe da instituição é guardado em cache; a edição precisa limpá-lo.
    $this->get('/intranet/educar_instituicao_det.php?cod_instituicao=' . $this->id)->assertOk();

    $this->post($this->url, ($this->form)(['nm_instituicao' => 'Instituição Renomeada']))
        ->assertRedirect('educar_instituicao_lst.php');

    $this->get('/intranet/educar_instituicao_det.php?cod_instituicao=' . $this->id)
        ->assertOk()
        ->assertSee('Instituição Renomeada');
});

test('não grava quando o código da instituição não é numérico', function () {
    $payload = ['cod_instituicao' => 'abc'] + ($this->form)();

    $this->post('/intranet/educar_instituicao_cad.php', $payload)
        ->assertOk()
        ->assertSee('Edição não realizada.');
});

// Defeito conhecido: a classe monta o UPDATE concatenando os textos sem escape,
// e um apóstrofo quebra a instrução. Quando for corrigido, este teste passa a
// falhar — é a deixa para trocá-lo por um que confira o nome gravado.
test('defeito: um apóstrofo no nome da cidade impede a gravação', function () {
    $payload = ($this->form)(['cidade' => "Santa Bárbara d'Oeste"]);

    $this->post($this->url, $payload)->assertServerError();
});
