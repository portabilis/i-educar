<?php

use App\Models\LegacySchool;
use App\Models\LegacyUser;
use App\Process;
use Database\Factories\LegacyMenuUserTypeFactory;
use Database\Factories\LegacyRegistrationFactory;
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
    $this->createSchoolUser = function (LegacySchool $school): LegacyUser {
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
    };
});

test('school user is redirected from registration of another school', function () {
    $user = ($this->createSchoolUser)(LegacySchoolFactory::new()->create());
    $registration = LegacyRegistrationFactory::new()->create();

    $this->actingAs($user)
        ->get('/intranet/educar_matricula_det.php' . '?cod_matricula=' . $registration->getKey())
        ->assertRedirectContains('educar_aluno_det.php?cod_aluno=' . $registration->ref_cod_aluno);
});

test('school user sees registration of own school', function () {
    $school = LegacySchoolFactory::new()->create();
    $user = ($this->createSchoolUser)($school);
    $registration = LegacyRegistrationFactory::new()->create(['ref_ref_cod_escola' => $school]);

    $this->actingAs($user)
        ->get('/intranet/educar_matricula_det.php' . '?cod_matricula=' . $registration->getKey())
        ->assertOk();
});
