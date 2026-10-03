<?php

namespace Tests\Feature\Security;

use App\Models\LegacyUserType;
use App\Process;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class ApiStudentRegistrationsAuthorizationTest extends TestCase
{
    use DatabaseTransactions, WithoutMiddleware;

    private function schoolLevelUserType()
    {
        return LegacyUserTypeFactory::new()->create(['nivel' => LegacyUserType::LEVEL_SCHOOLING]);
    }

    private function getRegistrations(array $params, $user = null)
    {
        $data = array_merge([
            'oper' => 'get',
            'resource' => 'matriculas',
        ], $params);

        $_GET = $data;

        $request = $user ? $this->actingAs($user) : $this;

        return $request->get('/module/Api/Aluno?' . http_build_query($data));
    }

    private function accessKeys(): array
    {
        return [
            'access_key' => config('legacy.apis.access_key'),
            'secret_key' => config('legacy.apis.secret_key'),
        ];
    }

    public function test_api_aluno_matriculas_bloqueia_usuario_sem_permissao()
    {
        $registration = LegacyRegistrationFactory::new()->create();
        $user = LegacyUserFactory::new()->create(['ref_cod_tipo_usuario' => $this->schoolLevelUserType()->getKey()]);

        $response = $this->getRegistrations(array_merge($this->accessKeys(), [
            'aluno_id' => $registration->student_id,
        ]), $user);

        $response->assertSuccessful();
        $response->assertJsonMissingPath('matriculas');
        $response->assertJsonFragment(['msg' => "Usuário sem permissão para visualizar 'matriculas'."]);
    }

    public function test_api_aluno_matriculas_permite_usuario_com_permissao()
    {
        $registration = LegacyRegistrationFactory::new()->create();
        $user = LegacyUserFactory::new()->withAccess(Process::REGISTRATIONS)->create(['ref_cod_tipo_usuario' => $this->schoolLevelUserType()->getKey()]);

        $response = $this->getRegistrations(array_merge($this->accessKeys(), [
            'aluno_id' => $registration->student_id,
        ]), $user);

        $response->assertSuccessful();
        $response->assertJsonPath('matriculas.0.id', $registration->getKey());
    }

    public function test_api_aluno_matriculas_permite_integracao_com_access_key()
    {
        $registration = LegacyRegistrationFactory::new()->create();

        $response = $this->getRegistrations(array_merge($this->accessKeys(), [
            'aluno_id' => $registration->student_id,
        ]));

        $response->assertSuccessful();
        $response->assertJsonPath('matriculas.0.id', $registration->getKey());
    }

    public function test_api_aluno_matriculas_bloqueia_sem_login_e_sem_access_key()
    {
        $registration = LegacyRegistrationFactory::new()->create();

        $response = $this->getRegistrations([
            'aluno_id' => $registration->student_id,
        ]);

        $response->assertSuccessful();
        $response->assertJsonMissingPath('matriculas');
        $response->assertJsonFragment(['msg' => 'Usuário deve estar logado ou a chave de acesso deve ser enviada!']);
    }

    public function test_api_aluno_matriculas_bloqueia_access_key_invalida()
    {
        $registration = LegacyRegistrationFactory::new()->create();

        $response = $this->getRegistrations([
            'access_key' => 'chave-invalida',
            'aluno_id' => $registration->student_id,
        ]);

        $response->assertSuccessful();
        $response->assertJsonMissingPath('matriculas');
        $response->assertJsonFragment(['msg' => 'Chave de acesso inválida!']);
    }
}
