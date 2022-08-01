<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\LegacyStudent;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StudentControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testUpdateStateRegistration()
    {
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
    }

    public function testUpdateStateRegistrationWithDigit()
    {
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
    }

    public function testUpdateStateRegistrationDuplicated()
    {
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
    }

    public function testUpdateStateRegistrationInvalid()
    {
        /** @var LegacyStudent $student */
        $student = LegacyStudentFactory::new()->create();

        $stateRegistration = 1234;

        $response = $this->put("/api/students/{$student->getKey()}/update-state-registration", [
            'state_registration_id' => $stateRegistration,
        ], $this->getAuthorizationHeader());

        $response->assertUnprocessable();
        $response->assertJson(['message' => 'O número de inscrição é inválido.']);
        $response->assertJsonValidationErrors(['state_registration_id' => ['O número de inscrição é inválido.']]);
    }
}
