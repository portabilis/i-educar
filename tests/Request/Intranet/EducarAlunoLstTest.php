<?php

use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyEmployeeFactory;
use Database\Factories\LegacyEmployeeRoleFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyRoleFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassTeacherFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\StudentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->configuration = fn (array $attributes = []) => LegacyGeneralConfigurationFactory::new()->create($attributes + [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'active_on_ieducar' => true,
    ]);

    $this->detail = fn (StudentScenario $scenario) => 'educar_aluno_det.php?cod_aluno=' . $scenario->student->getKey();
    $this->list = fn (array $query = []) => $this->get('/intranet/educar_aluno_lst.php?' . http_build_query($query))->assertOk();
});

test('lista o código, o nome, a mãe, o responsável e o CPF do responsável', function () {
    $ana = StudentScenario::named('Ana Alves')->guardianType('m');
    $ana->mother('Marta Alves', ['cpf' => '12345678909']);

    $bia = StudentScenario::named('Bia Borges')->guardianType('p');
    $bia->father('Paulo Borges');

    ($this->list)(['nome_aluno' => 'Alves'])
        ->assertSeeInOrder([(string) $ana->student->getKey(), 'Ana Alves', 'MARTA ALVES', 'MARTA ALVES', '123.456.789-09'])
        ->assertSee(($this->detail)($ana), false)
        ->assertDontSee(($this->detail)($bia), false)
        ->assertSee('go("/module/Cadastro/aluno")', false);

    // Sem mãe a coluna mostra um traço; sem CPF, o aviso.
    ($this->list)(['nome_aluno' => 'Borges'])
        ->assertSeeInOrder([(string) $bia->student->getKey(), 'Bia Borges', '-', 'PAULO BORGES', 'Não informado']);
});

test('mostra o nome social antes do nome de registro', function () {
    $ana = StudentScenario::named('Ana Alves', individual: ['nome_social' => 'Nina Alves']);

    ($this->list)(['cod_aluno' => $ana->student->getKey()])
        ->assertSee('Nina Alves<br> <i>Nome de registro: </i>Ana Alves', false);
});

test('filtra por cada campo da pessoa', function (Closure $query) {
    $ana = StudentScenario::named('Ana Alves', ['aluno_estado_id' => '111.222.333-4'], ['data_nasc' => '2015-04-20', 'cpf' => '12345678909']);
    $ana->mother('Marta Alves');
    $ana->father('Pedro Alves');
    $ana->guardian('Rita Ramos');
    $ana->document(['rg' => '7654321']);
    $ana->inep('123456789012');

    $bia = StudentScenario::named('Bia Borges', ['aluno_estado_id' => '555.666.777-8'], ['data_nasc' => '2014-01-05', 'cpf' => '98765432100']);
    $bia->mother('Olga Borges');
    $bia->father('Paulo Borges');
    $bia->guardian('Sara Souza');
    $bia->document(['rg' => '1112223']);
    $bia->inep('999999999999');

    ($this->list)($query->call($this, $ana))
        ->assertSee(($this->detail)($ana), false)
        ->assertDontSee(($this->detail)($bia), false);
})->with([
    'código' => [fn (StudentScenario $ana) => ['cod_aluno' => $ana->student->getKey()]],
    'nome, sem acento e por trecho' => [fn () => ['nome_aluno' => 'ána alv']],
    'nome da mãe' => [fn () => ['nome_mae' => 'marta']],
    'nome do pai' => [fn () => ['nome_pai' => 'pedro']],
    'nome do responsável' => [fn () => ['nome_responsavel' => 'Rita']],
    'data de nascimento' => [fn () => ['data_nascimento' => '20/04/2015']],
    'CPF com máscara' => [fn () => ['cpf_aluno' => '123.456.789-09']],
    'RG com pontuação' => [fn () => ['rg_aluno' => '7.654.321']],
    'código da rede estadual' => [fn () => ['aluno_estado_id' => '111.222.333-4']],
    'código INEP' => [fn () => ['cod_inep' => '123456789012']],
]);

test('filtra pelas matrículas ativas do aluno', function (Closure $query) {
    $scenario = EnrollmentScenario::for($this->user, 2030);
    $ana = $scenario->enrolled('Ana Alves', $scenario->schoolClass())->registration->student;

    $other = $scenario->grade('Segundo Ano');
    $bia = $scenario->student('Bia Borges');
    $scenario->registration(['ref_cod_aluno' => $bia, 'ano' => 2029, 'ref_ref_cod_serie' => $other]);

    ($this->list)($query->call($this, $scenario))
        ->assertSee('educar_aluno_det.php?cod_aluno=' . $ana->getKey(), false)
        ->assertDontSee('educar_aluno_det.php?cod_aluno=' . $bia->getKey(), false);
})->with([
    'ano' => [fn (EnrollmentScenario $scenario) => ['ano' => 2030, 'ref_cod_escola' => $scenario->school->getKey()]],
    'série' => [fn (EnrollmentScenario $scenario) => ['ref_cod_serie' => $scenario->grade->getKey()]],
    'curso e ano' => [fn (EnrollmentScenario $scenario) => ['ref_cod_curso' => $scenario->course->getKey(), 'ano' => 2030]],
]);

test('com a similaridade ligada acha grafias parecidas do nome', function () {
    $ana = StudentScenario::named('Thiago Souza');
    $bia = StudentScenario::named('Bia Borges');

    ($this->list)(['nome_aluno' => 'Tiago Sousa'])->assertDontSee(($this->detail)($ana), false);

    ($this->list)(['nome_aluno' => 'Tiago Sousa', 'similaridade' => 'on'])
        ->assertSee(($this->detail)($ana), false)
        ->assertDontSee(($this->detail)($bia), false);
});

test('ignora uma data de nascimento inválida ou anterior a 1990', function (string $date) {
    $ana = StudentScenario::named('Ana Alves');

    ($this->list)(['nome_aluno' => 'Ana Alves', 'data_nascimento' => $date])->assertSee(($this->detail)($ana), false);
})->with(['31/02/2015', '20/04/1800', '2015-04-20']);

test('não lista aluno inativo', function () {
    $ana = StudentScenario::named('Ana Alves', ['ativo' => 0]);

    ($this->list)(['nome_aluno' => 'Ana Alves'])->assertDontSee(($this->detail)($ana), false);
});

test('pagina de 20 em 20, do cadastro mais recente para o mais antigo', function () {
    foreach (range(1, 21) as $number) {
        StudentScenario::named(sprintf('Registro Aluno %02d', $number), ['data_cadastro' => now()->subDays($number)]);
    }

    ($this->list)(['nome_aluno' => 'Registro Aluno'])
        ->assertSeeInOrder(['Registro Aluno 01', 'Registro Aluno 20'])
        ->assertDontSee('Registro Aluno 21');

    ($this->list)(['nome_aluno' => 'Registro Aluno', 'pagina_formulario' => 2])
        ->assertSee('Registro Aluno 21')
        ->assertDontSee('Registro Aluno 20');
});

test('mostra o filtro e a coluna do código INEP quando a instituição pede', function () {
    $ana = StudentScenario::named('Ana Alves')->inep('123456789012');
    $bia = StudentScenario::named('Bia Alves');

    ($this->list)(['nome_aluno' => 'Alves'])
        ->assertDontSee('name="cod_inep"', false)
        ->assertDontSee('123456789012');

    ($this->configuration)(['mostrar_codigo_inep_aluno' => 1]);

    ($this->list)(['nome_aluno' => 'Alves'])
        ->assertSee('name="cod_inep"', false)
        ->assertSee('Código INEP')
        ->assertSee('<a href="' . ($this->detail)($ana) . '">123456789012</a>', false)
        ->assertSee('<a href="' . ($this->detail)($bia) . '">-</a>', false);
});

test('não oferece o botão Novo quando a instituição bloqueia o cadastro de alunos', function () {
    ($this->configuration)(['bloquear_cadastro_aluno' => 1]);

    ($this->list)(['cod_aluno' => 1])->assertDontSee('go("/module/Cadastro/aluno")', false);
});

test('não oferece o botão Novo a quem só pode visualizar', function () {
    ($this->configuration)();

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(578, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao])
    );

    ($this->list)(['cod_aluno' => 1])->assertDontSee('go("/module/Cadastro/aluno")', false);
});

test('lista as séries do vínculo com turma do professor ao filtrar por escola e curso', function () {
    $institution = LegacyInstitutionFactory::new()->current();

    DB::table('pmieducar.instituicao')
        ->where('cod_instituicao', $institution->getKey())
        ->update(['exigir_vinculo_turma_professor' => 1]);

    $schoolClass = LegacySchoolClassFactory::new()->create();
    $otherGrade = LegacySchoolGradeFactory::new()->create([
        'ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
        'ref_cod_serie' => LegacyGradeFactory::new()->create(['ref_cod_curso' => $schoolClass->ref_cod_curso]),
    ])->grade;
    $employee = EmployeeFactory::new()->create();

    LegacyEmployeeFactory::new()->create(['ref_cod_pessoa_fj' => $employee->getKey()]);
    LegacyEmployeeRoleFactory::new()->create([
        'ref_cod_servidor' => $employee->getKey(),
        'ref_cod_funcao' => LegacyRoleFactory::new()->create(['professor' => 1]),
    ]);
    LegacySchoolClassTeacherFactory::new()->create([
        'instituicao_id' => $institution->getKey(),
        'turma_id' => $schoolClass->getKey(),
        'servidor_id' => $employee->getKey(),
        'ano' => $schoolClass->ano,
    ]);

    $this->actingAs(LegacyUserFactory::new()->admin()->create(['cod_usuario' => $employee->getKey()]));

    $this->get('/intranet/educar_aluno_lst.php?' . http_build_query([
        'ref_cod_instituicao' => $institution->getKey(),
        'ref_cod_escola' => $schoolClass->ref_ref_cod_escola,
        'ref_cod_curso' => $schoolClass->ref_cod_curso,
        'ano' => $schoolClass->ano,
    ]))
        ->assertOk()
        ->assertSee($schoolClass->grade->name)
        ->assertDontSee($otherGrade->name)
        ->assertDontSee(mb_strtoupper($otherGrade->name));
});
