<?php

namespace Tests\Feature\Security;

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

class ApiLegacyAuthorizationTest extends TestCase
{
    use DatabaseTransactions, WithoutMiddleware;

    private const PROCESSO_PESSOA_FISICA = 43;

    private const PROCESSO_DEIXOU_DE_FREQUENTAR = 685;

    private const PROCESSO_TURMA = 586;

    private const PROCESSO_DIARIO = 642;

    private const PROCESSO_HISTORICO = 999613;

    private const PROCESSO_DOCUMENTACAO = 578;

    /**
     * O menu do processo é criado como filho de outro menu, como em produção.
     * Um menu raiz liberaria a visualização a qualquer usuário pela regra de
     * ProcessPolicy::view, e os testes de acesso permitido não provariam nada.
     */
    private function schoolUser(?int $process = null, ?LegacySchool $school = null, bool $modify = true): User
    {
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
    }

    private function callApi(string $method, string $path, array $params, ?User $user = null)
    {
        $_GET = $params;

        $request = $user ? $this->actingAs($user) : $this;

        return $request->call($method, $path . '?' . http_build_query($params));
    }

    private function accessKeys(): array
    {
        return [
            'access_key' => config('legacy.apis.access_key'),
            'secret_key' => config('legacy.apis.secret_key'),
        ];
    }

    private function getPessoa(array $params, ?User $user = null)
    {
        return $this->callApi('GET', '/module/Api/Pessoa', array_merge([
            'oper' => 'get',
            'resource' => 'pessoa',
        ], $params), $user);
    }

    private function abandonedRegistration(?LegacySchool $school = null): LegacyRegistration
    {
        return LegacyRegistrationFactory::new()->create(array_filter([
            'ref_ref_cod_escola' => $school?->getKey(),
            'aprovado' => \App_Model_MatriculaSituacao::ABANDONO,
        ]));
    }

    private function deleteAbandono(LegacyRegistration $registration, User $user)
    {
        return $this->callApi('DELETE', '/module/Api/Matricula', [
            'oper' => 'delete',
            'resource' => 'abandono',
            'id' => $registration->getKey(),
        ], $user);
    }

    private function getProcessamento(LegacyRegistration $registration, User $user)
    {
        return $this->callApi('GET', '/module/HistoricoEscolar/processamentoApi', [
            'oper' => 'get',
            'att' => 'matriculas',
            'instituicao_id' => $registration->school->ref_cod_instituicao,
            'escola_id' => $registration->ref_ref_cod_escola,
            'ano' => $registration->ano,
        ], $user);
    }

    private function getDiarioMatriculas($schoolClass, User $user)
    {
        return $this->callApi('GET', '/module/Avaliacao/diarioApi', [
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
    }

    // CVE-2025-8790

    public function test_api_pessoa_bloqueia_usuario_sem_permissao()
    {
        $individual = LegacyIndividualFactory::new()->create();

        $response = $this->getPessoa(['id' => $individual->getKey()], $this->schoolUser());

        $response->assertSuccessful();
        $response->assertJsonMissingPath('cpf');
        $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'pessoa'."]);
    }

    public function test_api_pessoa_bloqueia_busca_por_cpf_sem_permissao()
    {
        $individual = LegacyIndividualFactory::new()->create(['cpf' => '11144477735']);

        $response = $this->getPessoa(['cpf' => '111.444.777-35'], $this->schoolUser());

        $response->assertSuccessful();
        $response->assertJsonMissingPath('id');
        $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'pessoa'."]);
        $this->assertNotNull($individual);
    }

    public function test_api_pessoa_bloqueia_usuario_com_permissao_em_outro_processo()
    {
        $individual = LegacyIndividualFactory::new()->create();

        $response = $this->getPessoa(['id' => $individual->getKey()], $this->schoolUser(self::PROCESSO_TURMA));

        $response->assertJsonMissingPath('cpf');
        $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'pessoa'."]);
    }

    public function test_api_pessoa_permite_usuario_do_cadastro_de_pessoa_fisica()
    {
        $individual = LegacyIndividualFactory::new()->create();

        $response = $this->getPessoa(['id' => $individual->getKey()], $this->schoolUser(self::PROCESSO_PESSOA_FISICA));

        $response->assertSuccessful();
        $response->assertJsonPath('id', (string) $individual->getKey());
    }

    public function test_api_pessoa_permite_usuario_do_cadastro_de_aluno()
    {
        $individual = LegacyIndividualFactory::new()->create();

        $response = $this->getPessoa(['id' => $individual->getKey()], $this->schoolUser(Process::REGISTRATIONS));

        $response->assertSuccessful();
        $response->assertJsonPath('id', (string) $individual->getKey());
    }

    public function test_api_pessoa_permite_integracao_com_access_key()
    {
        $individual = LegacyIndividualFactory::new()->create();

        $response = $this->getPessoa(array_merge($this->accessKeys(), ['id' => $individual->getKey()]));

        $response->assertSuccessful();
        $response->assertJsonPath('id', (string) $individual->getKey());
    }

    public function test_api_pessoa_parent_bloqueia_usuario_sem_permissao()
    {
        $individual = LegacyIndividualFactory::new()->create();

        $response = $this->callApi('GET', '/module/Api/Pessoa', [
            'oper' => 'get',
            'resource' => 'pessoa-parent',
            'id' => $individual->getKey(),
        ], $this->schoolUser());

        $response->assertSuccessful();
        $response->assertJsonMissingPath('cpf');
        $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'pessoa-parent'."]);
    }

    // CVE-2025-9760

    public function test_api_abandono_bloqueia_usuario_sem_permissao()
    {
        $registration = $this->abandonedRegistration();

        $response = $this->deleteAbandono($registration, $this->schoolUser(school: $registration->school));

        $response->assertSuccessful();
        $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'abandono'."]);
        $this->assertSame(\App_Model_MatriculaSituacao::ABANDONO, $registration->refresh()->aprovado);
    }

    public function test_api_abandono_bloqueia_matricula_de_outra_escola()
    {
        $registration = $this->abandonedRegistration();

        $response = $this->deleteAbandono($registration, $this->schoolUser(self::PROCESSO_DEIXOU_DE_FREQUENTAR));

        $response->assertSuccessful();
        $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'abandono' desta escola."]);
        $this->assertSame(\App_Model_MatriculaSituacao::ABANDONO, $registration->refresh()->aprovado);
    }

    public function test_api_abandono_bloqueia_usuario_com_permissao_em_outro_processo()
    {
        $registration = $this->abandonedRegistration();

        $response = $this->deleteAbandono($registration, $this->schoolUser(Process::REGISTRATIONS, $registration->school));

        $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'abandono'."]);
        $this->assertSame(\App_Model_MatriculaSituacao::ABANDONO, $registration->refresh()->aprovado);
    }

    public function test_api_abandono_permite_usuario_com_permissao_na_propria_escola()
    {
        $registration = $this->abandonedRegistration();
        $user = $this->schoolUser(self::PROCESSO_DEIXOU_DE_FREQUENTAR, $registration->school);

        $response = $this->deleteAbandono($registration, $user);

        $response->assertSuccessful();
        $response->assertJsonMissing(['msg' => "Usuário sem permissão para acessar 'abandono'."]);
        $response->assertJsonMissing(['msg' => "Usuário sem permissão para acessar 'abandono' desta escola."]);
        $this->assertSame(\App_Model_MatriculaSituacao::EM_ANDAMENTO, $registration->refresh()->aprovado);
    }

    // CVE-2025-10073

    public function test_api_turma_bloqueia_turma_de_outra_escola()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();

        $response = $this->callApi('GET', '/module/Api/Turma', [
            'oper' => 'get',
            'resource' => 'turma',
            'id' => $schoolClass->getKey(),
        ], $this->schoolUser(self::PROCESSO_TURMA));

        $response->assertSuccessful();
        $response->assertJsonMissingPath('cod_turma');
        $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'turma' desta escola."]);
    }

    public function test_api_turma_permite_turma_da_propria_escola()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();

        $response = $this->callApi('GET', '/module/Api/Turma', [
            'oper' => 'get',
            'resource' => 'turma',
            'id' => $schoolClass->getKey(),
        ], $this->schoolUser(self::PROCESSO_TURMA, $schoolClass->school));

        $response->assertSuccessful();
        $response->assertJsonPath('cod_turma', $schoolClass->getKey());
    }

    // CVE-2025-9687

    public function test_processamento_api_bloqueia_usuario_sem_permissao()
    {
        $registration = LegacyRegistrationFactory::new()->create();

        $response = $this->getProcessamento($registration, $this->schoolUser(school: $registration->school));

        $response->assertSuccessful();
        $response->assertJsonMissingPath('matriculas');
        $response->assertJsonFragment(['msg' => 'Usuário sem permissão para processar o histórico escolar.']);
    }

    public function test_processamento_api_bloqueia_usuario_so_com_visualizacao()
    {
        $registration = LegacyRegistrationFactory::new()->create();
        $user = $this->schoolUser(self::PROCESSO_HISTORICO, $registration->school, modify: false);

        $response = $this->getProcessamento($registration, $user);

        $response->assertJsonMissingPath('matriculas');
        $response->assertJsonFragment(['msg' => 'Usuário sem permissão para processar o histórico escolar.']);
    }

    public function test_processamento_api_bloqueia_escola_de_outro_usuario()
    {
        $registration = LegacyRegistrationFactory::new()->create();

        $response = $this->getProcessamento($registration, $this->schoolUser(self::PROCESSO_HISTORICO));

        $response->assertJsonMissingPath('matriculas');
        $response->assertJsonFragment(['msg' => 'Usuário sem permissão para acessar matrículas desta escola.']);
    }

    public function test_processamento_api_bloqueia_escola_nao_numerica()
    {
        $registration = LegacyRegistrationFactory::new()->create();
        $user = $this->schoolUser(self::PROCESSO_HISTORICO, $registration->school);

        $response = $this->callApi('GET', '/module/HistoricoEscolar/processamentoApi', [
            'oper' => 'get',
            'att' => 'matriculas',
            'instituicao_id' => $registration->school->ref_cod_instituicao,
            'escola_id' => 'x',
            'ano' => $registration->ano,
        ], $user);

        $response->assertJsonMissingPath('matriculas');
        $response->assertJsonFragment(['msg' => 'Usuário sem permissão para acessar matrículas desta escola.']);
    }

    public function test_processamento_api_permite_usuario_com_permissao_na_propria_escola()
    {
        $registration = LegacyRegistrationFactory::new()->create();
        $user = $this->schoolUser(self::PROCESSO_HISTORICO, $registration->school);

        $response = $this->getProcessamento($registration, $user);

        $response->assertSuccessful();
        $response->assertJsonPath('matriculas', []);
    }

    // CVE-2025-10607

    public function test_diario_api_bloqueia_usuario_sem_permissao()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();

        $response = $this->getDiarioMatriculas($schoolClass, $this->schoolUser(school: $schoolClass->school));

        $response->assertSuccessful();
        $response->assertJsonPath('matriculas', []);
        $response->assertJsonFragment(['msg' => "Usuário sem permissão para visualizar 'matriculas'."]);
    }

    public function test_diario_api_bloqueia_usuario_com_permissao_em_outro_processo()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();

        $response = $this->getDiarioMatriculas($schoolClass, $this->schoolUser(self::PROCESSO_TURMA, $schoolClass->school));

        $response->assertJsonPath('matriculas', []);
        $response->assertJsonFragment(['msg' => "Usuário sem permissão para visualizar 'matriculas'."]);
    }

    public function test_diario_api_bloqueia_turma_de_outra_escola()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();

        $response = $this->getDiarioMatriculas($schoolClass, $this->schoolUser(self::PROCESSO_DIARIO));

        $response->assertSuccessful();
        $response->assertJsonMissingPath('matriculas');
        $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'matriculas' desta escola."]);
    }

    public function test_diario_api_bloqueia_matricula_de_outra_escola()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();
        $registration = LegacyRegistrationFactory::new()->create();
        $user = $this->schoolUser(self::PROCESSO_DIARIO, $schoolClass->school);

        $response = $this->callApi('POST', '/module/Avaliacao/diarioApi', [
            'oper' => 'post',
            'resource' => 'falta',
            'turma_id' => $schoolClass->getKey(),
            'matricula_id' => $registration->getKey(),
            'etapa' => 1,
            'att_value' => 1,
        ], $user);

        $response->assertSuccessful();
        $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'falta' desta escola."]);
    }

    public function test_diario_api_bloqueia_exclusao_sem_permissao_de_cadastro()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();
        $user = $this->schoolUser(self::PROCESSO_DIARIO, $schoolClass->school, modify: false);

        $response = $this->callApi('DELETE', '/module/Avaliacao/diarioApi', [
            'oper' => 'delete',
            'resource' => 'falta',
            'turma_id' => $schoolClass->getKey(),
            'etapa' => 1,
        ], $user);

        $response->assertSuccessful();
        $response->assertJsonFragment(['msg' => "Usuário sem permissão para alterar 'falta'."]);
    }

    // Documentação padrão da instituição

    private function document(): LegacyInstitutionDocument
    {
        return LegacyInstitutionDocument::query()->create([
            'instituicao_id' => LegacyInstitutionFactory::new()->current()->getKey(),
            'titulo_documento' => 'Documento',
            'url_documento' => 'https://example.com/documento.pdf',
        ]);
    }

    private function callDocuments(string $resource, array $params, User $user)
    {
        return $this->callApi('GET', '/module/Api/InstituicaoDocumentacao', array_merge([
            'oper' => 'get',
            'resource' => $resource,
        ], $params), $user);
    }

    public function test_documentacao_bloqueia_inclusao_sem_permissao()
    {
        $institutionId = LegacyInstitutionFactory::new()->current()->getKey();

        $response = $this->callDocuments('insertDocuments', [
            'instituicao_id' => $institutionId,
            'titulo_documento' => 'Intruso',
            'url_documento' => 'https://example.com/intruso.pdf',
        ], $this->schoolUser(self::PROCESSO_DOCUMENTACAO, modify: false));

        $response->assertSuccessful();
        $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'insertDocuments'."]);
        $this->assertDatabaseMissing('pmieducar.instituicao_documentacao', ['titulo_documento' => 'Intruso']);
    }

    public function test_documentacao_bloqueia_exclusao_sem_permissao()
    {
        $document = $this->document();

        $response = $this->callDocuments('deleteDocuments', ['id' => $document->getKey()], $this->schoolUser());

        $response->assertSuccessful();
        $response->assertJsonFragment(['msg' => "Usuário sem permissão para acessar 'deleteDocuments'."]);
        $this->assertModelExists($document);
    }

    public function test_documentacao_permite_inclusao_com_permissao_e_grava_o_usuario_logado()
    {
        $user = $this->schoolUser(self::PROCESSO_DOCUMENTACAO);
        $institutionId = LegacyInstitutionFactory::new()->current()->getKey();

        $this->callDocuments('insertDocuments', [
            'instituicao_id' => $institutionId,
            'titulo_documento' => 'Permitido',
            'url_documento' => 'https://example.com/permitido.pdf',
            'ref_usuario_cad' => 1,
        ], $user)->assertSuccessful();

        $this->assertDatabaseHas('pmieducar.instituicao_documentacao', [
            'titulo_documento' => 'Permitido',
            'ref_usuario_cad' => $user->getKey(),
        ]);
    }

    public function test_documentacao_permite_exclusao_com_permissao()
    {
        $document = $this->document();

        $this->callDocuments('deleteDocuments', ['id' => $document->getKey()], $this->schoolUser(self::PROCESSO_DOCUMENTACAO))
            ->assertSuccessful();

        $this->assertModelMissing($document);
    }
}
