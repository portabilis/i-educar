<?php

namespace Tests\Api;

use App\Events\EmployeeCreated;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class EmployeeWorkloadValidationTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

        public function test_employee_cannot_be_created_with_zero_workload()
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        Event::fake();

        $individual = LegacyIndividualFactory::new()->create();

        $data = [
            'tipoacao' => 'Novo',
            'carga_horaria' => '00:00',
            'cod_servidor' => $individual->getKey(),
            'ref_cod_instituicao' => $user->ref_cod_instituicao,
        ];


        $this->post('/intranet/educar_servidor_cad.php', $data)
            ->assertSessionHas(
                'error',
                'A carga horária deve ser maior que 00:00.<br>'
            );


        $this->assertDatabaseMissing('pmieducar.servidor', [
            'cod_servidor' => $individual->getKey(),
            'ref_cod_instituicao' => $user->ref_cod_instituicao,
        ]);

        Event::assertNotDispatched(EmployeeCreated::class);
    }


    public function test_employee_workload_cannot_be_changed_to_zero()
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $individual = LegacyIndividualFactory::new()->create();

        // Cria um servidor com carga horária válida.
        EmployeeFactory::new()->create([
            'id' => $individual->getKey(),
            'institution_id' => $user->ref_cod_instituicao,
            'workload' => 20,
        ]);

        // Tenta alterar a carga horária para zero.
        $data = [
            'tipoacao' => 'Editar',
            'carga_horaria' => '00:00',
            'cod_servidor' => $individual->getKey(),
            'ref_cod_instituicao' => $user->ref_cod_instituicao,
            'ref_cod_instituicao_original' => $user->ref_cod_instituicao,
        ];


        $bufferLevel = ob_get_level();

        $response = $this->post('/intranet/educar_servidor_cad.php', $data);

        // Limpa buffers eventualmente deixados pela página legada.
        while (ob_get_level() > $bufferLevel) {
            ob_end_clean();
        }

        $response->assertSessionHas(
            'error',
            'A carga horária deve ser maior que 00:00.<br>'
        );


        // Confirma que a carga horária original foi preservada.
        $this->assertDatabaseHas('pmieducar.servidor', [
            'cod_servidor' => $individual->getKey(),
            'ref_cod_instituicao' => $user->ref_cod_instituicao,
            'carga_horaria' => 20,
        ]);
    }

    public function test_employee_cannot_be_created_with_alternative_zero_workload()
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $individual = LegacyIndividualFactory::new()->create();

        $data = [
            'tipoacao' => 'Novo',
            'carga_horaria' => '0:00',
            'cod_servidor' => $individual->getKey(),
            'ref_cod_instituicao' => $user->ref_cod_instituicao,
        ];

        $this->cleanGlobals();

        $response = $this->post('/intranet/educar_servidor_cad.php', $data);

        $response->assertSessionHas(
            'error',
            'A carga horária deve ser maior que 00:00.<br>'
        );

        $this->assertDatabaseMissing('pmieducar.servidor', [
            'cod_servidor' => $individual->getKey(),
            'ref_cod_instituicao' => $user->ref_cod_instituicao,
        ]);
    }

    public function test_employee_can_be_created_with_thirty_minutes()
    {
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);

        $individual = LegacyIndividualFactory::new()->create();

        $data = [
            'tipoacao' => 'Novo',
            'carga_horaria' => '00:30',
            'cod_servidor' => $individual->getKey(),
            'ref_cod_instituicao' => $user->ref_cod_instituicao,
        ];

        $this->post('/intranet/educar_servidor_cad.php', $data)
            ->assertRedirectContains('educar_servidor_det.php');

        $this->assertDatabaseHas('pmieducar.servidor', [
            'cod_servidor' => $individual->getKey(),
            'ref_cod_instituicao' => $user->ref_cod_instituicao,
            'carga_horaria' => 0.5,
        ]);
    }
}
