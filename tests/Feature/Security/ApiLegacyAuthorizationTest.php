<?php

use App\Models\LegacyInstitutionDocument;
use App\Models\LegacyRegistration;
use App\Models\LegacySchool;
use App\Models\LegacyUserType;
use App\Process;
use App\User;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyMenuUserTypeFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserSchoolFactory;
use Database\Factories\LegacyUserTypeFactory;
use Database\Factories\MenuFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, WithoutMiddleware::class);

beforeEach(function () {
    /**
     * O menu do processo é criado como filho de outro menu, como em produção.
     * Um menu raiz liberaria a visualização a qualquer usuário pela regra de
     * ProcessPolicy::view, e os testes de acesso permitido não provariam nada.
     */
    $this->schoolUser = function (?int $process = null, ?LegacySchool $school = null, bool $modify = true): User {
        $type = LegacyUserTypeFactory::new()->create(['nivel' => LegacyUserType::LEVEL_SCHOOLING]);
        $user = LegacyUserFactory::new()->create(['ref_cod_tipo_usuario' => $type->getKey()]);

        if ($process) {
            LegacyMenuUserTypeFactory::new()->create([
                'menu_id' => MenuFactory::new()->create([
                    'parent_id' => MenuFactory::new()->create()->getKey(),
                    'process' => $process,
                ])->getKey(),
                'ref_cod_tipo_usuario' => $type->getKey(),
                'visualiza' => 1,
                'cadastra' => (int) $modify,
                'exclui' => 0,
            ]);
        }

        LegacyUserSchoolFactory::new()->create([
            'ref_cod_usuario' => $user->getKey(),
            'ref_cod_escola' => $school ?? LegacySchoolFactory::new()->create(),
        ]);

        return User::query()->findOrFail($user->getKey());
    };

    $this->callApi = function (string $method, string $path, array $params, ?User $user = null) {
        $_GET = $params;

        $request = $user ? $this->actingAs($user) : $this;

        return $request->call($method, $path . '?' . http_build_query($params));
    };

    $this->accessKeys = function (): array {
        return [
            'access_key' => config('legacy.apis.access_key'),
            'secret_key' => config('legacy.apis.secret_key'),
        ];
    };

    $this->getPessoa = function (array $params, ?User $user = null) {
        return ($this->callApi)('GET', '/module/Api/Pessoa', array_merge([
            'oper' => 'get',
            'resource' => 'pessoa',
        ], $params), $user);
    };

    $this->abandonedRegistration = function (?LegacySchool $school = null): LegacyRegistration {
        return LegacyRegistrationFactory::new()->create(array_filter([
            'ref_ref_cod_escola' => $school?->getKey(),
            'aprovado' => App_Model_MatriculaSituacao::ABANDONO,
        ]));
    };

    $this->deleteAbandono = function (LegacyRegistration $registration, User $user) {
        return ($this->callApi)('DELETE', '/module/Api/Matricula', [
            'oper' => 'delete',
            'resource' => 'abandono',
            'id' => $registration->getKey(),
        ], $user);
    };

    $this->getProcessamento = function (LegacyRegistration $registration, User $user) {
        return ($this->callApi)('GET', '/module/HistoricoEscolar/processamentoApi', [
            'oper' => 'get',
            'att' => 'matriculas',
            'instituicao_id' => $registration->school->ref_cod_instituicao,
            'escola_id' => $registration->ref_ref_cod_escola,
            'ano' => $registration->ano,
        ], $user);
    };

    $this->getDiarioMatriculas = function ($schoolClass, User $user) {
        return ($this->callApi)('GET', '/module/Avaliacao/diarioApi', [
            'oper' => 'get',
            'resource' => 'matriculas',
            'instituicao_id' => $schoolClass->school->ref_cod_instituicao,
            'escola_id' => $schoolClass->ref_ref_cod_escola,
            'curso_id' => $schoolClass->ref_cod_curso,
            'serie_id' => $schoolClass->ref_ref_cod_serie,
            'turma_id' => $schoolClass->getKey(),
            'ano' => $schoolClass->ano,
            'etapa' => 1,
        ], $user);
    };

    $this->document = function (): LegacyInstitutionDocument {
        return LegacyInstitutionDocument::query()->create([
            'instituicao_id' => LegacyInstitutionFactory::new()->current()->getKey(),
            'titulo_documento' => 'Documento',
            'url_documento' => 'https://example.com/documento.pdf',
        ]);
    };

    $this->callDocuments = function (string $resource, array $params, User $user) {
        return ($this->callApi)('GET', '/module/Api/InstituicaoDocumentacao', array_merge([
            'oper' => 'get',
            'resource' => $resource,
        ], $params), $user);
    };
});

test('api pessoa bloqueia usuario sem permissao', function () {
    $individual = LegacyIndividualFactory::new()->create();

    $response = ($this->getPessoa)(['id' => $individual->getKey()], ($this->schoolUser)());

    $response->assertSuccessful();
    $response->assertJsonMissingPath('cpf');
    $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'pessoa'."]);
});

test('api pessoa bloqueia busca por cpf sem permissao', function () {
    $individual = LegacyIndividualFactory::new()->create(['cpf' => '11144477735']);

    $response = ($this->getPessoa)(['cpf' => '111.444.777-35'], ($this->schoolUser)());

    $response->assertSuccessful();
    $response->assertJsonMissingPath('id');
    $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'pessoa'."]);
    $this->assertNotNull($individual);
});

test('api pessoa bloqueia usuario com permissao em outro processo', function () {
    $individual = LegacyIndividualFactory::new()->create();

    $response = ($this->getPessoa)(['id' => $individual->getKey()], ($this->schoolUser)(586));

    $response->assertJsonMissingPath('cpf');
    $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'pessoa'."]);
});

test('api pessoa permite usuario do cadastro de pessoa fisica', function () {
    $individual = LegacyIndividualFactory::new()->create();

    $response = ($this->getPessoa)(['id' => $individual->getKey()], ($this->schoolUser)(43));

    $response->assertSuccessful();
    $response->assertJsonPath('id', (string) $individual->getKey());
});

test('api pessoa permite usuario do cadastro de aluno', function () {
    $individual = LegacyIndividualFactory::new()->create();

    $response = ($this->getPessoa)(['id' => $individual->getKey()], ($this->schoolUser)(Process::REGISTRATIONS));

    $response->assertSuccessful();
    $response->assertJsonPath('id', (string) $individual->getKey());
});

test('api pessoa permite integracao com access key', function () {
    $individual = LegacyIndividualFactory::new()->create();

    $response = ($this->getPessoa)(array_merge(($this->accessKeys)(), ['id' => $individual->getKey()]));

    $response->assertSuccessful();
    $response->assertJsonPath('id', (string) $individual->getKey());
});

test('api pessoa parent bloqueia usuario sem permissao', function () {
    $individual = LegacyIndividualFactory::new()->create();

    $response = ($this->callApi)('GET', '/module/Api/Pessoa', [
        'oper' => 'get',
        'resource' => 'pessoa-parent',
        'id' => $individual->getKey(),
    ], ($this->schoolUser)());

    $response->assertSuccessful();
    $response->assertJsonMissingPath('cpf');
    $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'pessoa-parent'."]);
});

test('api abandono bloqueia usuario sem permissao', function () {
    $registration = ($this->abandonedRegistration)();

    $response = ($this->deleteAbandono)($registration, ($this->schoolUser)(school: $registration->school));

    $response->assertSuccessful();
    $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'abandono'."]);
    $this->assertSame(App_Model_MatriculaSituacao::ABANDONO, $registration->refresh()->aprovado);
});

test('api abandono bloqueia matricula de outra escola', function () {
    $registration = ($this->abandonedRegistration)();

    $response = ($this->deleteAbandono)($registration, ($this->schoolUser)(685));

    $response->assertSuccessful();
    $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'abandono' desta escola."]);
    $this->assertSame(App_Model_MatriculaSituacao::ABANDONO, $registration->refresh()->aprovado);
});

test('api abandono bloqueia usuario com permissao em outro processo', function () {
    $registration = ($this->abandonedRegistration)();

    $response = ($this->deleteAbandono)($registration, ($this->schoolUser)(Process::REGISTRATIONS, $registration->school));

    $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'abandono'."]);
    $this->assertSame(App_Model_MatriculaSituacao::ABANDONO, $registration->refresh()->aprovado);
});

test('api abandono permite usuario com permissao na propria escola', function () {
    $registration = ($this->abandonedRegistration)();
    $user = ($this->schoolUser)(685, $registration->school);

    $response = ($this->deleteAbandono)($registration, $user);

    $response->assertSuccessful();
    $response->assertJsonMissing(['msg' => "Usuário sem permissão para acessar 'abandono'."]);
    $response->assertJsonMissing(['msg' => "Usuário sem permissão para acessar 'abandono' desta escola."]);
    $this->assertSame(App_Model_MatriculaSituacao::EM_ANDAMENTO, $registration->refresh()->aprovado);
});

test('api turma bloqueia turma de outra escola', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    $response = ($this->callApi)('GET', '/module/Api/Turma', [
        'oper' => 'get',
        'resource' => 'turma',
        'id' => $schoolClass->getKey(),
    ], ($this->schoolUser)(586));

    $response->assertSuccessful();
    $response->assertJsonMissingPath('cod_turma');
    $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'turma' desta escola."]);
});

test('api turma permite turma da propria escola', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    $response = ($this->callApi)('GET', '/module/Api/Turma', [
        'oper' => 'get',
        'resource' => 'turma',
        'id' => $schoolClass->getKey(),
    ], ($this->schoolUser)(586, $schoolClass->school));

    $response->assertSuccessful();
    $response->assertJsonPath('cod_turma', $schoolClass->getKey());
});

test('processamento api bloqueia usuario sem permissao', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $response = ($this->getProcessamento)($registration, ($this->schoolUser)(school: $registration->school));

    $response->assertSuccessful();
    $response->assertJsonMissingPath('matriculas');
    $response->assertJsonFragment(['msg' => 'Usuário sem permissão para processar o histórico escolar.']);
});

test('processamento api bloqueia usuario so com visualizacao', function () {
    $registration = LegacyRegistrationFactory::new()->create();
    $user = ($this->schoolUser)(999613, $registration->school, modify: false);

    $response = ($this->getProcessamento)($registration, $user);

    $response->assertJsonMissingPath('matriculas');
    $response->assertJsonFragment(['msg' => 'Usuário sem permissão para processar o histórico escolar.']);
});

test('processamento api bloqueia escola de outro usuario', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $response = ($this->getProcessamento)($registration, ($this->schoolUser)(999613));

    $response->assertJsonMissingPath('matriculas');
    $response->assertJsonFragment(['msg' => 'Usuário sem permissão para acessar matrículas desta escola.']);
});

test('processamento api bloqueia escola nao numerica', function () {
    $registration = LegacyRegistrationFactory::new()->create();
    $user = ($this->schoolUser)(999613, $registration->school);

    $response = ($this->callApi)('GET', '/module/HistoricoEscolar/processamentoApi', [
        'oper' => 'get',
        'att' => 'matriculas',
        'instituicao_id' => $registration->school->ref_cod_instituicao,
        'escola_id' => 'x',
        'ano' => $registration->ano,
    ], $user);

    $response->assertJsonMissingPath('matriculas');
    $response->assertJsonFragment(['msg' => 'Usuário sem permissão para acessar matrículas desta escola.']);
});

test('processamento api permite usuario com permissao na propria escola', function () {
    $registration = LegacyRegistrationFactory::new()->create();
    $user = ($this->schoolUser)(999613, $registration->school);

    $response = ($this->getProcessamento)($registration, $user);

    $response->assertSuccessful();
    $response->assertJsonPath('matriculas', []);
});

test('diario api bloqueia usuario sem permissao', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    $response = ($this->getDiarioMatriculas)($schoolClass, ($this->schoolUser)(school: $schoolClass->school));

    $response->assertSuccessful();
    $response->assertJsonPath('matriculas', []);
    $response->assertJsonFragment(['msg' => "Usuário sem permissão para visualizar 'matriculas'."]);
});

test('diario api bloqueia usuario com permissao em outro processo', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    $response = ($this->getDiarioMatriculas)($schoolClass, ($this->schoolUser)(586, $schoolClass->school));

    $response->assertJsonPath('matriculas', []);
    $response->assertJsonFragment(['msg' => "Usuário sem permissão para visualizar 'matriculas'."]);
});

test('diario api bloqueia turma de outra escola', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    $response = ($this->getDiarioMatriculas)($schoolClass, ($this->schoolUser)(642));

    $response->assertSuccessful();
    $response->assertJsonMissingPath('matriculas');
    $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'matriculas' desta escola."]);
});

test('diario api bloqueia matricula de outra escola', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();
    $registration = LegacyRegistrationFactory::new()->create();
    $user = ($this->schoolUser)(642, $schoolClass->school);

    $response = ($this->callApi)('POST', '/module/Avaliacao/diarioApi', [
        'oper' => 'post',
        'resource' => 'falta',
        'turma_id' => $schoolClass->getKey(),
        'matricula_id' => $registration->getKey(),
        'etapa' => 1,
        'att_value' => 1,
    ], $user);

    $response->assertSuccessful();
    $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'falta' desta escola."]);
});

test('diario api bloqueia exclusao sem permissao de cadastro', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();
    $user = ($this->schoolUser)(642, $schoolClass->school, modify: false);

    $response = ($this->callApi)('DELETE', '/module/Avaliacao/diarioApi', [
        'oper' => 'delete',
        'resource' => 'falta',
        'turma_id' => $schoolClass->getKey(),
        'etapa' => 1,
    ], $user);

    $response->assertSuccessful();
    $response->assertJsonFragment(['msg' => "Usuário sem permissão para alterar 'falta'."]);
});

test('documentacao bloqueia inclusao sem permissao', function () {
    $institutionId = LegacyInstitutionFactory::new()->current()->getKey();

    $response = ($this->callDocuments)('insertDocuments', [
        'instituicao_id' => $institutionId,
        'titulo_documento' => 'Intruso',
        'url_documento' => 'https://example.com/intruso.pdf',
    ], ($this->schoolUser)(578, modify: false));

    $response->assertSuccessful();
    $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'insertDocuments'."]);
    $this->assertDatabaseMissing('pmieducar.instituicao_documentacao', ['titulo_documento' => 'Intruso']);
});

test('documentacao bloqueia exclusao sem permissao', function () {
    $document = ($this->document)();

    $response = ($this->callDocuments)('deleteDocuments', ['id' => $document->getKey()], ($this->schoolUser)());

    $response->assertSuccessful();
    $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'deleteDocuments'."]);
    $this->assertModelExists($document);
});

test('documentacao permite inclusao com permissao e grava o usuario logado', function () {
    $user = ($this->schoolUser)(578);
    $institutionId = LegacyInstitutionFactory::new()->current()->getKey();

    ($this->callDocuments)('insertDocuments', [
        'instituicao_id' => $institutionId,
        'titulo_documento' => 'Permitido',
        'url_documento' => 'https://example.com/permitido.pdf',
        'ref_usuario_cad' => 1,
    ], $user)->assertSuccessful();

    $this->assertDatabaseHas('pmieducar.instituicao_documentacao', [
        'titulo_documento' => 'Permitido',
        'ref_usuario_cad' => $user->getKey(),
    ]);
});

test('documentacao permite exclusao com permissao', function () {
    $document = ($this->document)();

    ($this->callDocuments)('deleteDocuments', ['id' => $document->getKey()], ($this->schoolUser)(578))
        ->assertSuccessful();

    $this->assertModelMissing($document);
});
