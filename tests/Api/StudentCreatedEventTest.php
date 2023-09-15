<?php

namespace Tests\Api;

use App\Events\StudentCreated;
use App\Events\StudentUpdated;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class StudentCreatedEventTest extends TestCase
{
    use DatabaseTransactions;
    use DiarioApiRequestTestTrait;

    public function testStudentCreatedEvent()
    {
        Event::fake();

        $studentPerson  = LegacyIndividualFactory::new()->create();
        $guardianPerson  = LegacyIndividualFactory::new()->create();

        $data = [
            'oper' => 'post',
            'resource' => 'aluno',
            'pessoa_id' => $studentPerson->idpes,
            'mae_id' => $guardianPerson->idpes,
            'id_federal' => '000.000.000-00',
            'tipo_responsavel'=> 'mae',
            'alfabetizado'=> 'checked',
            'material'=> 'A',
            'tipo_transporte'=> 'nenhum',
            'deficiencias'=> [],
            'transtornos' => [],
        ];

        $response = $this->getResource('/module/Api/Aluno', $data);

        $studentId = $response->json('id');

        Event::assertDispatched(StudentCreated::class, function ($e) use ($studentId) {
            return $e->student->id === $studentId;
        });
    }

    public function testStudentUpdatedEvent()
    {
        Event::fake();

        $individual = LegacyIndividualFactory::new()->father()->mother()->guardian()->create();
        $student = LegacyStudentFactory::new()->create([
            'ref_idpes' => $individual,
        ]);

        $data = [
            'oper' => 'put',
            'resource' => 'aluno',
            'id' => $student->id,
            'pessoa_id' => $student->ref_idpes,
            'mae_id' => $student->individual->idpes_mae,
            'id_federal' => '000.000.000-00',
            'tipo_responsavel'=> 'mae',
            'alfabetizado'=> 'checked',
            'material'=> 'A',
            'tipo_transporte'=> 'nenhum',
            'deficiencias'=> [],
            'transtornos' => [],
        ];

        $response = $this->getResource('/module/Api/Aluno', $data);

        $studentId = $response->json('id');

        Event::assertDispatched(StudentUpdated::class, function ($e) use ($studentId) {
            return $e->student->id === $studentId;
        });
    }
}
