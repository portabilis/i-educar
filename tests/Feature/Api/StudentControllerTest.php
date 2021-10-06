<?php

namespace Tests\Feature\Api;

use App\Models\LegacyStudent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class StudentControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testUpdateStateRegistration()
    {
        /** @var LegacyStudent $student */
        $student = factory(LegacyStudent::class)->create();

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
        $student = factory(LegacyStudent::class)->create();

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
        $this->expectException(ValidationException::class);

        $stateRegistration = '000.000.000';

        /** @var LegacyStudent $student */
        $student = factory(LegacyStudent::class)->create();

        factory(LegacyStudent::class)->create([
            'aluno_estado_id' => $stateRegistration,
        ]);

        $response = $this->put("/api/students/{$student->getKey()}/update-state-registration", [
            'state_registration_id' => $stateRegistration,
        ], $this->getAuthorizationHeader());

        $response->assertJson([]);
    }

    public function testUpdateStateRegistrationInvalid()
    {
        $this->expectException(ValidationException::class);

        /** @var LegacyStudent $student */
        $student = factory(LegacyStudent::class)->create();

        $stateRegistration = 1234;

        $response = $this->put("/api/students/{$student->getKey()}/update-state-registration", [
            'state_registration_id' => $stateRegistration,
        ], $this->getAuthorizationHeader());

        $response->assertJson([]);
    }
}
