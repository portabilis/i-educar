<?php

namespace Tests\Api;

use App\Models\LegacySchool;
use App\Models\LegacyUser;
use App\Process;
use App_Model_NivelTipoUsuario;
use Database\Factories\LegacyMenuUserTypeFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserSchoolFactory;
use Database\Factories\LegacyUserTypeFactory;
use Database\Factories\MenuFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Portabilis_Utils_User;
use Tests\TestCase;

class LegacyRegistrationDetTest extends TestCase
{
    use DatabaseTransactions, WithoutMiddleware;

    public const ROUTE = '/intranet/educar_matricula_det.php';

    protected function setUp(): void
    {
        parent::setUp();

        // O usuário e o nível de acesso ficam em cache estático entre as requisições do mesmo processo.
        Portabilis_Utils_User::$_currentUserId = null;
        Portabilis_Utils_User::$_nivelAcesso = null;
        Portabilis_Utils_User::$_permissoes = null;
    }

    public function test_school_user_is_redirected_from_registration_of_another_school(): void
    {
        $user = $this->createSchoolUser(LegacySchoolFactory::new()->create());
        $registration = LegacyRegistrationFactory::new()->create();

        $this->actingAs($user)
            ->get(self::ROUTE . '?cod_matricula=' . $registration->getKey())
            ->assertRedirectContains('educar_aluno_det.php?cod_aluno=' . $registration->ref_cod_aluno);
    }

    public function test_school_user_sees_registration_of_own_school(): void
    {
        $school = LegacySchoolFactory::new()->create();
        $user = $this->createSchoolUser($school);
        $registration = LegacyRegistrationFactory::new()->create(['ref_ref_cod_escola' => $school]);

        $this->actingAs($user)
            ->get(self::ROUTE . '?cod_matricula=' . $registration->getKey())
            ->assertOk();
    }

    private function createSchoolUser(LegacySchool $school): LegacyUser
    {
        $userType = LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::ESCOLA]);

        LegacyMenuUserTypeFactory::new()->create([
            'ref_cod_tipo_usuario' => $userType,
            'menu_id' => MenuFactory::new()->create([
                'parent_id' => MenuFactory::new()->create(),
                'process' => Process::REGISTRATIONS,
            ]),
        ]);

        $user = LegacyUserFactory::new()->create(['ref_cod_tipo_usuario' => $userType]);

        LegacyUserSchoolFactory::new()->create([
            'ref_cod_usuario' => $user,
            'ref_cod_escola' => $school,
        ]);

        return $user;
    }
}
