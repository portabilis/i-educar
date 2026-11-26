<?php

use App\Rules\StateRegistrationUniqueRule;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->student = LegacyStudentFactory::new()->create(['aluno_estado_id' => '123.456.789-0']);
});

test('aceita um número de inscrição que nenhum aluno usa', function () {
    expect((new StateRegistrationUniqueRule)->passes('aluno_estado_id', '987.654.321-0'))->toBeTrue();
});

test('recusa um número de inscrição que outro aluno já usa', function () {
    expect((new StateRegistrationUniqueRule)->passes('aluno_estado_id', '123.456.789-0'))->toBeFalse();
});

test('explica que o número de inscrição já está em uso', function () {
    expect((new StateRegistrationUniqueRule)->message())->toBe('Já existe uma aluno com este número de inscrição.');
});

test('aceita o número de inscrição do próprio aluno que está sendo editado', function () {
    expect((new StateRegistrationUniqueRule($this->student))->passes('aluno_estado_id', '123.456.789-0'))->toBeTrue();
});

test('recusa o número de inscrição de outro aluno mesmo ignorando o aluno editado', function () {
    $other = LegacyStudentFactory::new()->create(['aluno_estado_id' => '111.222.333-4']);

    expect((new StateRegistrationUniqueRule($this->student))->passes('aluno_estado_id', '111.222.333-4'))->toBeFalse()
        ->and((new StateRegistrationUniqueRule($other))->passes('aluno_estado_id', '123.456.789-0'))->toBeFalse();
});
