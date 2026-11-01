<?php

use App\Models\LegacyIndividual;
use App\Models\LegacyStudent;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyMaritalStatusFactory;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\Api\DiarioApiRequestTestTrait;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, DiarioApiRequestTestTrait::class);

beforeEach(function () {
    $this->maritalStatus = null;

    $this->createIndividual = function (): LegacyIndividual {
        $this->maritalStatus ??= LegacyMaritalStatusFactory::new()->create([
            'ideciv' => 1,
            'descricao' => 'Solteiro',
        ]);

        return LegacyIndividualFactory::new()->create([
            'ideciv' => $this->maritalStatus,
        ]);
    };

    $this->putData = function (LegacyStudent $student, array $params): array {
        return array_merge([
            'oper' => 'put',
            'resource' => 'aluno',
            'id' => $student->getKey(),
            'pessoa_id' => $student->ref_idpes,
            'tipo_responsavel' => 'pai_mae',
            'alfabetizado' => 'checked',
            'material' => 'A',
            'tipo_transporte' => 'nenhum',
            'deficiencias' => [],
            'transtornos' => [],
        ], $params);
    };
});

test('put com pessoa id vazia nao grava pai e mae', function () {
    Event::fake();

    $mae = ($this->createIndividual)();
    $student = LegacyStudentFactory::new()->create();

    LegacyIndividual::query()->whereKey($student->ref_idpes)->update([
        'idpes_mae' => $mae->getKey(),
    ]);

    $response = $this->getResource('/module/Api/Aluno', ($this->putData)($student, [
        'pessoa_id' => '',
        'mae_id' => '',
        'pai_id' => '',
    ]));

    $response->assertStatus(200);
    $this->assertTrue($response->json('any_error_msg'));

    $this->assertEquals(
        $mae->getKey(),
        LegacyIndividual::query()->whereKey($student->ref_idpes)->value('idpes_mae')
    );
});

test('put reprovado nao grava pai e mae', function () {
    Event::fake();

    $maeAtual = ($this->createIndividual)();
    $maeNova = ($this->createIndividual)();
    $student = LegacyStudentFactory::new()->create();

    LegacyIndividual::query()->whereKey($student->ref_idpes)->update([
        'idpes_mae' => $maeAtual->getKey(),
    ]);

    $response = $this->getResource('/module/Api/Aluno', ($this->putData)($student, [
        'mae_id' => $maeNova->getKey(),
        'tipo_transporte' => 'invalido',
    ]));

    $response->assertStatus(200);
    $this->assertTrue($response->json('any_error_msg'));

    $this->assertEquals(
        $maeAtual->getKey(),
        LegacyIndividual::query()->whereKey($student->ref_idpes)->value('idpes_mae')
    );
});

test('put com pai e mae vazios limpa os vinculos', function () {
    Event::fake();

    $mae = ($this->createIndividual)();
    $student = LegacyStudentFactory::new()->create();

    LegacyIndividual::query()->whereKey($student->ref_idpes)->update([
        'idpes_mae' => $mae->getKey(),
    ]);

    $response = $this->getResource('/module/Api/Aluno', ($this->putData)($student, [
        'mae_id' => '',
        'pai_id' => '',
    ]));

    $response->assertStatus(200);
    $this->assertFalse($response->json('any_error_msg'));

    $this->assertNull(
        LegacyIndividual::query()->whereKey($student->ref_idpes)->value('idpes_mae')
    );
});

test('post reprovado nao devolve identificador nem cria aluno', function () {
    Event::fake();

    $individual = ($this->createIndividual)();
    $responsavel = ($this->createIndividual)();

    $response = $this->getResource('/module/Api/Aluno', [
        'oper' => 'post',
        'resource' => 'aluno',
        'pessoa_id' => $individual->getKey(),
        'mae_id' => $responsavel->getKey(),
        'pai_id' => $responsavel->getKey(),
        'tipo_responsavel' => 'mae',
        'alfabetizado' => 'checked',
        'material' => 'A',
        'tipo_transporte' => 'nenhum',
        'deficiencias' => [],
        'transtornos' => [],
    ]);

    $response->assertStatus(200);
    $this->assertTrue($response->json('any_error_msg'));
    $this->assertNull($response->json('id'));

    $this->assertFalse(
        LegacyStudent::query()->where('ref_idpes', $individual->getKey())->exists()
    );
});
