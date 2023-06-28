<?php

namespace Tests\Api;

use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\LoginFirstUser;
use Tests\TestCase;

class LegacyIndividualEndpointTestDelete extends TestCase
{
    use DatabaseTransactions;
    use LoginFirstUser;

    public function testDeleteEndpoint(): void
    {
        $individual = LegacyIndividualFactory::new()->create();

        $request = [
            'tipoacao' => 'Excluir',
            'cod_pessoa_fj' => (string) $individual->getKey(),
        ];

        $data = [];

        $payload = array_merge($request, $data);

        $this->post('/intranet/atendidos_cad.php', $payload)->assertRedirect('atendidos_lst.php');
        $this->assertDatabaseHas($individual->getTable(), [
            'idpes' => $individual->person->getKey(),
            'ativo' => 0,
        ]);
    }

    public function testDeleteWhenHasStudent(): void
    {
        $student = LegacyStudentFactory::new()->create();

        $request = [
            'tipoacao' => 'Excluir',
            'cod_pessoa_fj' => (string) $student->ref_idpes,
        ];

        $data = [];

        $payload = array_merge($request, $data);

        $this->post('/intranet/atendidos_cad.php', $payload)
            ->assertSuccessful()
            ->assertSee('Não foi possível excluir. Esta pessoa possuí vínculo com aluno.');
        $this->assertDatabaseHas($student->getTable(), [
            'ref_idpes' => $student->ref_idpes,
            'ativo' => 1,
        ]);
    }

    public function testDeleteWhenHasResponsible(): void
    {
        $student = LegacyStudentFactory::new()->create();

        $request = [
            'tipoacao' => 'Excluir',
            'cod_pessoa_fj' => (string) $student->individual->idpes_mae,
        ];

        $data = [];

        $payload = array_merge($request, $data);

        $this->post('/intranet/atendidos_cad.php', $payload)
            ->assertSuccessful()
            ->assertSee('Não foi possível excluir. A pessoa possuí vínculo(s) com aluno(s) como mãe, pai ou outro responsável.');
    }

    public function testDeleteWhenHasEmployee(): void
    {
        $employee = EmployeeFactory::new()->create();

        $request = [
            'tipoacao' => 'Excluir',
            'cod_pessoa_fj' => (string) $employee->ref_cod_pessoa_fj,
        ];

        $data = [];

        $payload = array_merge($request, $data);

        $this->post('/intranet/atendidos_cad.php', $payload)
            ->assertSuccessful()
            ->assertSee('Não foi possível excluir. Esta pessoa possuí vínculo com usuário do sistema.');
        $this->assertDatabaseHas($employee->getTable(), [
            'cod_servidor' => $employee->getKey(),
            'ativo' => 1,
        ]);
    }
}
