<?php

namespace Tests\Feature\Api;

use App\Models\LegacyStudent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StudentControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testUpdateStateRegistration()
    {
        /** @var LegacyStudent $student */
        $student = factory(LegacyStudent::class)->create();

        $this->put("/api/students/{$student->getKey()}/update-state-registration", [
            'state_registration_id' => 12345,
        ]);

        $this->assertDatabaseHas($student->getTable(), [
            'aluno_estado_id' => 12345,
        ]);
    }
}
