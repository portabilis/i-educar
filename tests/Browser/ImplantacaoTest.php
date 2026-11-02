<?php

use App\Models\LegacyCourse;
use App\Models\LegacyGrade;
use App\Models\LegacyUser;
use App\Models\LegacyUserType;
use Tests\Browser\Support\Form;
use Tests\Browser\Support\Menus;
use Tests\Browser\Support\Scenario;

/**
 * O roteiro de implantação, do primeiro acesso até a estrutura pedagógica.
 *
 * É um teste só, encadeado numa única sessão de browser, porque o que ele prova
 * é o encadeamento: cada etapa usa o que a anterior gravou, na mesma ordem em que
 * um município implanta o sistema. Os testes por tela (InstitutionTest,
 * UserTypeTest, UserTest, CourseTest, GradeTest) cobrem os detalhes de cada CRUD
 * com os pré-requisitos montados por factory; aqui nada é montado por baixo.
 *
 * Cada etapa guarda uma imagem em tests/Browser/Screenshots, que é o material de
 * comparação entre a interface atual e a do rebranding.
 *
 * O roteiro para antes de matricular alunos: escola, ano letivo, calendário,
 * série da escola, servidor, turma e aluno são as próximas etapas.
 */
it('percorre o roteiro de implantação', function () {
    // Estado de uma instalação recém-feita: dados de referência, instituição e o
    // administrador. Numa instalação real isso vem das migrations de
    // database/migrations/data/, que o banco `testing` não roda.
    $scenario = Scenario::install(adminLogin: 'adm-impl');
    $secretary = Scenario::individual('Bruno Secretário');

    // ── Etapa 0: primeiro acesso ──────────────────────────────────────────────
    $page = $scenario->loginAsAdmin()
        ->assertPathIs('/intranet/educar_index.php')
        ->assertSee($scenario->adminName);

    Form::shot($page, '00-primeiro-acesso');

    // ── Etapa 1: ajustar a instituição ────────────────────────────────────────
    // Não há cadastro de instituição pela interface: o Novo() da tela só
    // redireciona, e a listagem não tem botão "Novo". A implantação começa
    // editando a instituição criada na instalação.
    $page = $page->navigate('/intranet/educar_instituicao_lst.php')
        ->assertSee('Instituição - Listagem')
        ->click($scenario->institution->nm_instituicao)
        ->click('Editar')
        ->fill('#nm_instituicao', 'Prefeitura Municipal de Nova Modelópolis')
        ->fill('#nm_responsavel', 'Secretaria Municipal de Educação');

    $page = Form::save($page)->assertPathIs('/intranet/educar_instituicao_lst.php');

    expect($scenario->institution->fresh()->nm_instituicao)->toBe('Prefeitura Municipal de Nova Modelópolis');

    Form::shot($page, '01-instituicao');

    // ── Etapa 2: criar um tipo de usuário com permissões ──────────────────────
    $page = $page->navigate('/usuarios/tipos/novo')->assertSee('Tipo de Usuário');

    // O nível é Institucional, e não Escola, de propósito: o Usuario.js exige ao
    // menos uma escola vinculada quando o tipo é de nível Escola, e escola ainda
    // não existe neste ponto do roteiro. Usuários de escola entram depois da
    // etapa de cadastro das escolas.
    $page = Form::fill($page, [
        'name' => 'Secretaria Municipal',
        'description' => 'Secretaria municipal de educação',
    ])->select('level', 'Institucional')
        // Botão de atalho que marca "Cadastra" para todos os itens de menu.
        ->click('Cadastra')
        ->click('Salvar')
        ->waitForEvent('networkidle')
        ->assertPathIs('/usuarios/tipos');

    $type = LegacyUserType::query()->where('nm_tipo', 'Secretaria Municipal')->sole();

    expect($type->nivel)->toBe(App_Model_NivelTipoUsuario::INSTITUCIONAL)
        ->and($type->menus()->wherePivot('cadastra', 1)->count())->toBe(Menus::count());

    Form::shot($page, '02-tipo-de-usuario');

    // ── Etapa 3: criar o usuário da secretaria ────────────────────────────────
    // A tela busca a pessoa num popup com o input desabilitado; chegar com
    // ?ref_pessoa= é o que a própria interface faz depois da busca.
    $page = $page->navigate('/intranet/educar_usuario_cad.php?ref_pessoa=' . $secretary->idpes)
        ->assertSee('Bruno Secretário');

    $page = Form::fill($page, [
        'matricula' => 'bruno',
        '_senha' => Scenario::PASSWORD,
    ]);

    // O select de instituição é preenchido por AJAX depois da página abrir, daí
    // a cascata com espera explícita.
    $page = Form::cascade($page, [
        'ref_cod_tipo_usuario' => 'Secretaria Municipal',
        'ref_cod_funcionario_vinculo' => 'Efetivo',
        'ref_cod_instituicao' => $scenario->institution->getKey(),
    ]);

    $page = Form::save($page)->assertPathIs('/intranet/educar_usuario_lst.php');

    expect(LegacyUser::query()->find($secretary->idpes))->not->toBeNull();

    Form::shot($page, '03-usuario');

    // ── Etapa 4: criar o curso ────────────────────────────────────────────────
    $page = $page->navigate('/intranet/educar_curso_cad.php');

    $page = Form::cascade($page, [
        'ref_cod_instituicao' => $scenario->institution->getKey(),
        'ref_cod_nivel_ensino' => Scenario::EDUCATION_LEVEL,
        'ref_cod_tipo_ensino' => Scenario::EDUCATION_TYPE,
    ]);

    $page = Form::fill($page, [
        'nm_curso' => Scenario::COURSE,
        'sgl_curso' => 'EF',
        'qtd_etapas' => '4',
        'hora_falta' => '50',
        'carga_horaria' => '800',
    ])->select('modalidade_curso', 'Ensino regular');

    $page = Form::save($page)->assertPathIs('/intranet/educar_curso_lst.php');

    $course = LegacyCourse::query()->where('nm_curso', Scenario::COURSE)->sole();

    Form::shot($page, '04-curso');

    // ── Etapa 5: criar a série do curso ───────────────────────────────────────
    $page = $page->navigate('/intranet/educar_serie_cad.php');

    $page = Form::cascade($page, [
        'ref_cod_instituicao' => $scenario->institution->getKey(),
        'ref_cod_curso' => $course->getKey(),
    ]);

    $page = Form::fill($page, [
        'nm_serie' => Scenario::GRADE,
        'carga_horaria' => '800',
        'dias_letivos' => '200',
        // Nomes com colchetes precisam vir como seletor de atributo: o plugin do
        // Pest tentaria interpretá-los como seletor CSS.
        '[name="anos_letivos[0]"]' => (string) now()->year,
    ]);

    $page = $page->select('etapa_curso', '1')
        ->select('concluinte', 'não')
        ->select('[name="regras_avaliacao_id[0]"]', Scenario::EVALUATION_RULE);

    $page = Form::save($page)->assertPathIs('/intranet/educar_serie_lst.php');

    expect(LegacyGrade::query()->where('nm_serie', Scenario::GRADE)->sole()->ref_cod_curso)
        ->toBe($course->getKey());

    Form::shot($page, '05-serie');

    // Próxima etapa do roteiro: escola, ano letivo e calendário — e só depois
    // turma, aluno e matrícula.
});
