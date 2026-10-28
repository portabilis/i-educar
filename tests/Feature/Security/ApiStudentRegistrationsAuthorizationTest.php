<?php

use App\Models\LegacyUserType;
use App\Process;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, WithoutMiddleware::class);

beforeEach(function () {
    $this->schoolLevelUserType = function () {
        return LegacyUserTypeFactory::new()->create(['nivel' => LegacyUserType::LEVEL_SCHOOLING]);
    };

    $this->getRegistrations = function (array $params, $user = null) {
        $data = array_merge([
            'oper' => 'get',
            'resource' => 'matriculas',
        ], $params);

        $_GET = $data;

        $request = $user ? $this->actingAs($user) : $this;

        return $request->get('/module/Api/Aluno?' . http_build_query($data));
    };

    $this->accessKeys = function (): array {
        return [
            'access_key' => config('legacy.apis.access_key'),
            'secret_key' => config('legacy.apis.secret_key'),
        ];
    };
});

test('api aluno matriculas bloqueia usuario sem permissao', function () {
    $registration = LegacyRegistrationFactory::new()->create();
    $user = LegacyUserFactory::new()->create(['ref_cod_tipo_usuario' => ($this->schoolLevelUserType)()->getKey()]);

    $response = ($this->getRegistrations)(array_merge(($this->accessKeys)(), [
        'aluno_id' => $registration->student_id,
    ]), $user);

    $response->assertSuccessful();
    $response->assertJsonMissingPath('matriculas');
    $response->assertJsonFragment(['msg' => "Usuário sem permissão para visualizar 'matriculas'."]);
});

test('api aluno matriculas permite usuario com permissao', function () {
    $registration = LegacyRegistrationFactory::new()->create();
    $user = LegacyUserFactory::new()->withAccess(Process::REGISTRATIONS)->create(['ref_cod_tipo_usuario' => ($this->schoolLevelUserType)()->getKey()]);

    $response = ($this->getRegistrations)(array_merge(($this->accessKeys)(), [
        'aluno_id' => $registration->student_id,
    ]), $user);

    $response->assertSuccessful();
    $response->assertJsonPath('matriculas.0.id', $registration->getKey());
});

test('api aluno matriculas permite integracao com access key', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $response = ($this->getRegistrations)(array_merge(($this->accessKeys)(), [
        'aluno_id' => $registration->student_id,
    ]));

    $response->assertSuccessful();
    $response->assertJsonPath('matriculas.0.id', $registration->getKey());
});

test('api aluno matriculas bloqueia sem login e sem access key', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $response = ($this->getRegistrations)([
        'aluno_id' => $registration->student_id,
    ]);

    $response->assertSuccessful();
    $response->assertJsonMissingPath('matriculas');
    $response->assertJsonFragment(['msg' => 'Usuário deve estar logado ou a chave de acesso deve ser enviada!']);
});

test('api aluno matriculas bloqueia access key invalida', function () {
    $registration = LegacyRegistrationFactory::new()->create();

    $response = ($this->getRegistrations)([
        'access_key' => 'chave-invalida',
        'aluno_id' => $registration->student_id,
    ]);

    $response->assertSuccessful();
    $response->assertJsonMissingPath('matriculas');
    $response->assertJsonFragment(['msg' => 'Chave de acesso inválida!']);
});
