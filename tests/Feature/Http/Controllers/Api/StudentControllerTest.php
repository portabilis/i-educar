<?php

use App\Models\LegacyStudent;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('update state registration', function () {
    /** @var LegacyStudent $student */
    $student = LegacyStudentFactory::new()->create();

    $stateRegistration = '000.000.000';

    $response = $this->put("/api/students/{$student->getKey()}/update-state-registration", [
        'state_registration_id' => $stateRegistration,
    ], $this->getAuthorizationHeader());

    $response->assertSuccessful();
    $response->assertJson([
        'id' => $student->getKey(),
        'state_registration_id' => $stateRegistration,
    ]);

    $this->assertDatabaseHas($student->getTable(), [
        'cod_aluno' => $student->getKey(),
        'aluno_estado_id' => $stateRegistration,
    ]);
});

test('update state registration with digit', function () {
    /** @var LegacyStudent $student */
    $student = LegacyStudentFactory::new()->create();

    $stateRegistration = '000.000.000-1';

    $response = $this->put("/api/students/{$student->getKey()}/update-state-registration", [
        'state_registration_id' => $stateRegistration,
    ], $this->getAuthorizationHeader());

    $response->assertSuccessful();
    $response->assertJson([
        'id' => $student->getKey(),
        'state_registration_id' => $stateRegistration,
    ]);

    $this->assertDatabaseHas($student->getTable(), [
        'cod_aluno' => $student->getKey(),
        'aluno_estado_id' => $stateRegistration,
    ]);
});

test('update state registration duplicated', function () {
    $stateRegistration = '000.000.000';

    /** @var LegacyStudent $student */
    $student = LegacyStudentFactory::new()->create();

    LegacyStudentFactory::new()->create([
        'aluno_estado_id' => $stateRegistration,
    ]);

    $response = $this->put("/api/students/{$student->getKey()}/update-state-registration", [
        'state_registration_id' => $stateRegistration,
    ], $this->getAuthorizationHeader());

    $response->assertUnprocessable();
    $response->assertJson(['message' => 'Já existe uma aluno com este número de inscrição.']);
    $response->assertJsonValidationErrors(['state_registration_id' => ['Já existe uma aluno com este número de inscrição.']]);
});

test('update state registration invalid', function () {
    /** @var LegacyStudent $student */
    $student = LegacyStudentFactory::new()->create();

    $stateRegistration = 1234;

    $response = $this->put("/api/students/{$student->getKey()}/update-state-registration", [
        'state_registration_id' => $stateRegistration,
    ], $this->getAuthorizationHeader());

    $response->assertUnprocessable();
    $response->assertJson(['message' => 'O número de inscrição é inválido.']);
    $response->assertJsonValidationErrors(['state_registration_id' => ['O número de inscrição é inválido.']]);
});
