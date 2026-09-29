<?php

namespace Tests\Browser\Support;

use App\Models\LegacyCourse;
use App\Models\LegacyEvaluationRule;
use App\Models\LegacyGrade;
use App\Models\LegacyIndividual;
use App\Models\LegacyInstitution;
use App\Models\LegacyOrganization;
use App\Models\LegacySchool;
use App\Models\LegacyUser;
use App\Models\LegacyUserType;
use App\Models\State;
use App\Services\MenuCacheService;
use App_Model_NivelTipoUsuario;
use Database\Factories\CityFactory;
use Database\Factories\CountryFactory;
use Database\Factories\DistrictFactory;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyEducationLevelFactory;
use Database\Factories\LegacyEducationTypeFactory;
use Database\Factories\LegacyEmployeeFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyOrganizationFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\LegacyRegimeTypeFactory;
use Database\Factories\LegacyRoleFactory;
use Database\Factories\LegacySchoolClassTypeFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyStageTypeFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Database\Factories\StateFactory;
use Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Sistema recém-instalado, montado por factory, para os testes de browser
 * partirem do "primeiro acesso".
 *
 * Testes de browser passam por HTTP real, então não podem usar o
 * WithoutMiddleware dos testes de página: o estado tem que existir no banco.
 *
 * Todos os valores são fixos, e não vindos do faker, por dois motivos: os testes
 * afirmam o que está na tela, e o código legado monta SQL por concatenação sem
 * escapar — um apóstrofo num nome gerado ("Stephanie d'Oeste") derruba a query e
 * aborta a transação do teste.
 */
final class Scenario
{
    /**
     * Senha usada por todo o cenário.
     *
     * Precisa passar pela política do ValidateUserPasswordService, que a tela de
     * usuário aplica ao gravar: pelo menos 8 caracteres, com maiúsculas,
     * minúsculas, números e símbolos. Quem grava por factory escreve o hash
     * direto e não passa por essa validação, mas a mesma senha é usada nos dois
     * caminhos para o login funcionar igual.
     */
    public const PASSWORD = 'Senha@2026';

    /**
     * Nomes fixos do catálogo. Os testes escolhem as opções dos <select> pelo
     * rótulo (o selectOption do Playwright casa por valor **ou** rótulo), que é o
     * que o usuário vê — e é justamente isso que o rebranding não pode mudar.
     */
    public const ADMIN_USER_TYPE = 'Administrador';

    public const EDUCATION_LEVEL = 'Ano';

    public const EDUCATION_TYPE = 'Ensino Regular';

    public const REGIME_TYPE = 'Seriado';

    public const STAGE_TYPE = 'Bimestre';

    public const SCHOOL_CLASS_TYPE = 'Turma Regular';

    public const ROLE = 'Professor';

    public const PERIOD = 'Matutino';

    public const EVALUATION_RULE = 'Regra de Avaliação Padrão';

    public const COURSE = 'Ensino Fundamental';

    public const GRADE = '1º Ano';

    public const SCHOOL = 'Escola Municipal Modelo';

    /**
     * Dados de referência que, numa instalação real, vêm das migrations de
     * database/migrations/data/. No banco `testing` elas não rodam
     * (LEGACY_SEED_DATA=false, config/legacy.php), e sem essas linhas os
     * `campoLista` obrigatórios ficam só com "Selecione" e a validação do
     * clsCadastro aborta o submit antes de gravar.
     *
     * A chave é a tabela que o seeder popula, e ela é conferida antes: alguns
     * desses seeders inserem IDs explícitos e algumas dessas tabelas já têm
     * linhas vindas de migrations de fora de data/ (cadastro.estado_civil já
     * traz "Não informado"), então rodar sem conferir estoura chave duplicada.
     *
     * @var array<string, class-string<Seeder>>
     */
    private const REFERENCE = [
        'cadastro.estado_civil' => Seeders\DefaultCadastroEstadoCivilTableSeeder::class,
        'cadastro.escolaridade' => Seeders\DefaultCadastroEscolaridadeTableSeeder::class,
        'pmieducar.religions' => Seeders\DefaultPmieducarReligionTableSeeder::class,
        'pmieducar.turma_turno' => Seeders\DefaultPmieducarTurmaTurnoTableSeeder::class,
        'portal.funcionario_vinculo' => Seeders\DefaultPortalFuncionarioVinculoTableSeeder::class,
        'manager_roles' => Seeders\DefaultManagerRolesTableSeeder::class,
        'manager_link_types' => Seeders\DefaultManagerLinkTypesTableSeeder::class,
        'manager_access_criterias' => Seeders\DefaultManagerAccessCriteriasTableSeeder::class,
    ];

    /**
     * Único seeder de referência que grava quem cadastrou (`idpes_cad`) e por
     * isso só roda depois do administrador existir.
     *
     * @var array<string, class-string<Seeder>>
     */
    private const REFERENCE_AFTER_USER = [
        'cadastro.raca' => Seeders\DefaultCadastroRacaTableSeeder::class,
    ];

    private function __construct(
        public readonly State $state,
        public readonly LegacyInstitution $institution,
        public readonly LegacyUser $admin,
        public readonly LegacyEvaluationRule $evaluationRule,
        public readonly string $adminLogin,
        public readonly string $adminName,
    ) {}

    /**
     * Instala o sistema: dados de referência, endereçamento, instituição, um
     * administrador com credenciais conhecidas, a regra de avaliação padrão e a
     * árvore de menus.
     *
     * A ordem não é livre: o endereçamento e o estado civil vêm antes porque o
     * LegacyIndividualFactory precisa de um município; a instituição vem antes
     * do usuário porque o LegacyUserFactory resolve a instituição com
     * `current()` e, sem uma, criaria outra com valores do faker; e vários
     * seeders gravam quem cadastrou, então rodam depois do administrador.
     *
     * O login do administrador é parâmetro porque `test:browser:parallel` roda
     * os quatro processos no mesmo banco `testing` — cada arquivo usa o seu. Ele
     * vira a matrícula do funcionário, que tem no máximo 12 caracteres.
     */
    public static function install(
        string $adminLogin,
        string $adminName = 'Ana Diretora',
        string $institutionName = 'Prefeitura Municipal de Modelópolis',
    ): self {
        self::seed(self::REFERENCE);

        $state = self::addressing();
        $institution = self::institution($institutionName, $state);
        $admin = self::admin($adminLogin, $adminName);

        self::seed(self::REFERENCE_AFTER_USER);
        self::catalog();
        self::enable($institution);

        Menus::build();

        return new self(
            state: $state,
            institution: $institution,
            admin: $admin,
            // A regra de avaliação é pré-requisito do cadastro de série e tem
            // chave composta com a instituição (formula_media e
            // tabela_arredondamento são referenciadas junto com instituicao_id),
            // o que o seeder de dados não atende: ele fixa instituicao_id = 1.
            evaluationRule: LegacyEvaluationRuleFactory::new()->current(),
            adminLogin: $adminLogin,
            adminName: $adminName,
        );
    }

    /**
     * Roda seeders de referência ainda não aplicados, na transação do teste.
     *
     * @param  array<string, class-string<Seeder>>  $seeders
     */
    private static function seed(array $seeders): void
    {
        foreach ($seeders as $table => $seeder) {
            if (DB::table($table)->exists()) {
                continue;
            }

            app($seeder)->setContainer(app())->run();
        }
    }

    /**
     * Catálogo de tipos exigido pelas telas de gestão.
     *
     * Aqui não dá para usar os seeders de dados: eles fixam
     * `ref_cod_instituicao = 1`, e a instituição do cenário tem o id que a
     * sequência do banco `testing` der. Além de estourar a chave estrangeira,
     * seria errado — o formulário de curso filtra nível e tipo de ensino pela
     * instituição escolhida. As factories resolvem a instituição e o usuário com
     * `current()`, que é a que o install() acabou de criar.
     */
    private static function catalog(): void
    {
        LegacyEducationLevelFactory::new()->create(['nm_nivel' => self::EDUCATION_LEVEL]);
        LegacyEducationTypeFactory::new()->create(['nm_tipo' => self::EDUCATION_TYPE]);
        LegacyRegimeTypeFactory::new()->create(['nm_tipo' => self::REGIME_TYPE]);
        LegacySchoolClassTypeFactory::new()->create(['nm_tipo' => self::SCHOOL_CLASS_TYPE]);
        LegacyRoleFactory::new()->create(['nm_funcao' => self::ROLE, 'professor' => 1]);

        // Quatro etapas: é o tipo de etapa que o ano letivo da escola usa para
        // montar as datas de início e fim de cada bimestre.
        LegacyStageTypeFactory::new()->create([
            'nm_tipo' => self::STAGE_TYPE,
            'num_etapas' => 4,
            'num_meses' => 2,
            'num_semanas' => 10,
        ]);
    }

    /**
     * Libera o acesso ao sistema.
     *
     * Sem a configuração geral gravada, o middleware Suspended redireciona
     * qualquer requisição para /intranet/suspenso.php.
     */
    public static function enable(LegacyInstitution $institution): void
    {
        LegacyGeneralConfigurationFactory::new()->create([
            'ref_cod_instituicao' => $institution->getKey(),
            'active_on_ieducar' => true,
        ]);
    }

    /**
     * Cria a cadeia de endereçamento: país, estado, cidade e distrito.
     *
     * O formulário da instituição monta o <select> de UF a partir de
     * State::getListKeyAbbreviation(). Sem nenhum estado cadastrado a lista fica
     * só com "Selecione" e, por ser campo obrigatório, a validação do clsCadastro
     * aborta o submit: o clique em "Salvar" acontece, mas a página não navega.
     *
     * A sigla tem duas letras porque pmieducar.instituicao.ref_sigla_uf é
     * character(2), enquanto o StateFactory gera algo como "M C".
     */
    public static function addressing(): State
    {
        $state = StateFactory::new()->create([
            'country_id' => CountryFactory::new()->create(['name' => 'Brasil']),
            'name' => 'Santa Catarina',
            'abbreviation' => 'SC',
        ]);

        $city = CityFactory::new()->create([
            'state_id' => $state,
            'name' => 'Modelópolis',
        ]);

        DistrictFactory::new()->create([
            'city_id' => $city,
            'name' => 'Centro',
        ]);

        return $state;
    }

    /**
     * Cria a instituição com o endereço completo.
     *
     * A UF vem do estado cadastrado, para que o <select> do formulário
     * pré-selecione a opção certa, e o número é preenchido porque o factory o
     * deixa nulo. `obrigar_campos_censo` fica desligado de propósito: ligado, o
     * cadastro de escola passa a exigir dezenas de campos do Educacenso.
     */
    public static function institution(string $name, State $state): LegacyInstitution
    {
        return LegacyInstitutionFactory::new()->create([
            'nm_instituicao' => $name,
            'ref_sigla_uf' => $state->abbreviation,
            'ref_idtlog' => 'RUA',
            'cep' => 88820000,
            'logradouro' => 'Rua João Paulo Segundo',
            'numero' => 100,
            'bairro' => 'Centro',
            'cidade' => 'Modelópolis',
            'nm_responsavel' => 'Secretaria de Educação e Cultura',
            'obrigar_campos_censo' => false,
        ]);
    }

    /**
     * Cria um administrador com credenciais e nome conhecidos.
     *
     * O login é a matrícula do funcionário e a senha fica em portal.funcionario,
     * com o hash do Laravel. Administrador porque o Gate::before libera todos os
     * processos da ACL legada nesse nível.
     *
     * O nome é fixado para que os testes possam afirmar quem está logado: é ele
     * que aparece no topo da intranet.
     */
    public static function admin(string $login, string $name): LegacyUser
    {
        return LegacyUserFactory::new()->create([
            'cod_usuario' => self::person($name, $login)->idpes,
            // O tipo é criado aqui, e não pelo estado admin() do factory, para o
            // nome ser o mesmo da instalação real ("Administrador") em vez de um
            // nome do faker — ele aparece na listagem de tipos de usuário.
            'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create([
                'nm_tipo' => self::ADMIN_USER_TYPE,
                'descricao' => self::ADMIN_USER_TYPE,
                'nivel' => App_Model_NivelTipoUsuario::POLI_INSTITUCIONAL,
            ]),
        ]);
    }

    /**
     * Cria apenas a pessoa física (cadastro.pessoa + cadastro.fisica).
     *
     * É o pré-requisito das telas que buscam uma pessoa num popup com o input
     * `disabled`: o caminho automatizável é chegar nelas com a pessoa já
     * escolhida (`?ref_pessoa=`, `?cod_servidor=`, `?person=`), que é o que a
     * própria interface faz depois da busca. Sem o funcionário, porque é a tela
     * de usuário que o cria — se já existisse, o insert dela quebraria a chave
     * primária de portal.funcionario.
     */
    public static function individual(string $name): LegacyIndividual
    {
        return LegacyIndividualFactory::new()->create([
            'idpes' => LegacyPersonFactory::new()->create([
                'nome' => $name,
                'tipo' => 'F',
            ]),
        ]);
    }

    /**
     * Cria a pessoa física e o funcionário correspondente, que é o que dá login.
     */
    public static function person(string $name, ?string $login = null): LegacyIndividual
    {
        $individual = self::individual($name);

        LegacyEmployeeFactory::new()->create([
            'ref_cod_pessoa_fj' => $individual->idpes,
            'matricula' => $login ?? 'p' . $individual->idpes,
            'senha' => Hash::make(self::PASSWORD),
        ]);

        return $individual;
    }

    /**
     * Cria um curso ligado ao catálogo do cenário.
     *
     * Atalho por factory para os testes de outras telas; a tela de curso é
     * coberta por CourseTest. As factories resolvem nível de ensino, tipo de
     * ensino e instituição com `current()`, que devolve os do install().
     */
    public static function course(string $name = self::COURSE): LegacyCourse
    {
        return LegacyCourseFactory::new()->create([
            'nm_curso' => $name,
            // A descrição entra no rótulo do <select> de curso ("Nome (descrição)"),
            // então o factory deixaria uma frase do faker no meio da opção.
            'descricao' => '',
            'sgl_curso' => 'EF',
            'qtd_etapas' => 4,
            'padrao_ano_escolar' => 1,
        ]);
    }

    /**
     * Cria uma série do curso, já com a regra de avaliação do ano corrente.
     *
     * Sem a regra em modules.regra_avaliacao_serie_ano a série existe mas não
     * serve para matrícula, que é onde este roteiro vai parar.
     */
    public static function grade(?LegacyCourse $course = null, string $name = self::GRADE): LegacyGrade
    {
        $grade = LegacyGradeFactory::new()->create([
            'nm_serie' => $name,
            'ref_cod_curso' => $course ?? self::course(),
            'etapa_curso' => 1,
            'concluinte' => 1,
            'dias_letivos' => 200,
        ]);

        LegacyEvaluationRuleGradeYearFactory::new()->create([
            'serie_id' => $grade->getKey(),
            'ano_letivo' => now()->year,
            // Sem isto o factory criaria outra regra, com outra fórmula e outra
            // tabela de arredondamento, em vez de usar a padrão do cenário.
            'regra_avaliacao_id' => LegacyEvaluationRuleFactory::new()->current(),
            'regra_avaliacao_diferenciada_id' => null,
        ]);

        return $grade;
    }

    /**
     * Cria a pessoa jurídica que a escola representa.
     *
     * O cadastro de escola tem uma etapa prévia: escolher a pessoa jurídica num
     * campo de busca com autocomplete. O tipo da pessoa é fixado em "J" porque é
     * por ele que a busca da tela filtra.
     */
    public static function organization(string $name = self::SCHOOL): LegacyOrganization
    {
        return LegacyOrganizationFactory::new()->create([
            'idpes' => LegacyPersonFactory::new()->create([
                'nome' => $name,
                'tipo' => 'J',
            ]),
            'fantasia' => $name,
        ]);
    }

    /**
     * Cria uma escola ligada à instituição do cenário.
     *
     * Atalho por factory para os testes das telas que vêm depois da escola —
     * ano letivo, série da escola, turma. A tela em si é coberta por SchoolTest.
     */
    public static function school(?LegacyCourse $course = null, string $name = self::SCHOOL): LegacySchool
    {
        $factory = LegacySchoolFactory::new()->withName($name);

        // Com o curso, o factory grava também escola_curso e escola_serie — o
        // vínculo que as telas de série da escola e de turma exigem.
        if ($course instanceof LegacyCourse) {
            $factory = $factory->withCourse($course);
        }

        return $factory->create();
    }

    /**
     * Cria um tipo de usuário com permissão sobre os processos informados.
     *
     * Atalho por factory para os testes que exercitam outra tela e não querem
     * passar pela interface de permissões — essa é coberta por UserTypeTest.
     *
     * @param  array<int, int>  $processes
     * @param  int  $permission  1 visualiza, 2 cadastra, 3 exclui
     */
    public static function userType(
        string $name,
        int $level = App_Model_NivelTipoUsuario::INSTITUCIONAL,
        array $processes = [],
        int $permission = LegacyUserType::CAN_REMOVE,
    ): LegacyUserType {
        $type = LegacyUserTypeFactory::new()->create([
            'nm_tipo' => $name,
            'descricao' => $name,
            'nivel' => $level,
        ]);

        $rows = array_map(fn (int $process) => [
            'ref_cod_tipo_usuario' => $type->getKey(),
            'menu_id' => Menus::id($process),
            'visualiza' => (int) ($permission >= LegacyUserType::CAN_VIEW),
            'cadastra' => (int) ($permission >= LegacyUserType::CAN_MODIFY),
            'exclui' => (int) ($permission >= LegacyUserType::CAN_REMOVE),
        ], $processes);

        if ($rows !== []) {
            DB::table('pmieducar.menu_tipo_usuario')->insert($rows);
        }

        app(MenuCacheService::class)->flushAll();

        return $type;
    }

    /**
     * Cria um usuário com login próprio, vinculado a um tipo de usuário.
     */
    public static function user(
        string $login,
        string $name,
        LegacyUserType $type,
        LegacyInstitution $institution,
    ): LegacyUser {
        return LegacyUserFactory::new()->create([
            'cod_usuario' => self::person($name, $login)->idpes,
            'ref_cod_tipo_usuario' => $type->getKey(),
            'ref_cod_instituicao' => $institution->getKey(),
        ]);
    }

    /**
     * Autentica pela interface e devolve a página resultante.
     *
     * O login é feito pela UI, e não com actingAs(), porque o actingAs não
     * atravessa para a requisição servida ao browser: o Pest monta uma nova
     * requisição a partir dos cookies, então a sessão precisa ser criada de
     * verdade pelo formulário.
     */
    public static function login(string $login, string $password = self::PASSWORD)
    {
        return visit('/login')
            ->fill('login', $login)
            ->fill('password', $password)
            ->click('Entrar');
    }

    /**
     * Autentica com o administrador criado pelo install().
     */
    public function loginAsAdmin()
    {
        return self::login($this->adminLogin);
    }
}
