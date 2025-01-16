<?php

namespace Tests\Api;

use App\Events\EmployeeCreated;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class EmployeeCreatedEventTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function test_employee_created_event()
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        Event::fake();

        $employeeIndividual = LegacyIndividualFactory::new()->create();

        $data = [
            'tipoacao' => 'Novo',
            'carga_horaria' => '20:00',
            'cod_servidor' => $employeeIndividual->getKey(),
            'ref_cod_instituicao' => $user->ref_cod_instituicao,
        ];

        $this->post('/intranet/educar_servidor_cad.php', $data)
            ->assertRedirectContains('educar_servidor_det.php');

        Event::assertDispatched(EmployeeCreated::class);
    }
}
