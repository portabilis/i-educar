<?php

use App\Models\LegacyIndividual;
use App\Models\LegacyPerson;
use Database\Factories\DocumentAbsenceReasonFactory;
use Database\Factories\LegacyIndividualFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\DiarioApiRequestTestTrait;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, DiarioApiRequestTestTrait::class);

beforeEach(function () {
    $this->parentDetails = function (LegacyIndividual $individual) {
        return $this->getResource('/module/Api/Pessoa', [
            'oper' => 'get',
            'resource' => 'pessoa-parent',
            'id' => $individual->getKey(),
        ]);
    };

    $this->parentRequest = function (string $name, array $state): array {
        return array_merge([
            'oper' => 'post',
            'resource' => 'pessoa',
            'nome' => $name,
            'sexo' => 'M',
            'cpf' => '',
            'document_absence_reason_id' => '',
            'document_absence_observation' => '',
        ], $state);
    };
});

test('new parent without cpf saves the reason and registers timeline', function () {
    $reason = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => false]);

    $response = $this->postResource('/module/Api/Pessoa', ($this->parentRequest)('Pai Sem Cpf', [
        'possui_cpf' => 0,
        'document_absence_reason_id' => $reason->getKey(),
    ]));

    $response->assertOk();

    $individual = LegacyIndividual::findOrFail($response->json('pessoa_id'));

    $this->assertNull($individual->getRawOriginal('cpf'));
    $this->assertSame($reason->getKey(), $individual->document_absence_reason_id);
    $this->assertCount(1, $individual->timelines);
    $this->assertSame(['CPF' => null, 'Motivo da ausência' => $reason->name], $individual->timelines->first()->payload['after']);
});

test('parent with cpf informed clears the reason', function () {
    $reason = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => false]);
    $individual = LegacyIndividualFactory::new()->create(['cpf' => 0]);
    LegacyIndividual::query()->whereKey($individual->getKey())->update(['document_absence_reason_id' => $reason->getKey()]);

    $response = $this->postResource('/module/Api/Pessoa', ($this->parentRequest)('Pai Com Cpf', [
        'pessoa_id' => $individual->getKey(),
        'possui_cpf' => 1,
        'cpf' => '123.456.789-09',
    ]));

    $response->assertOk()->assertJsonPath('pessoa_id', (string) $individual->getKey());

    $individual->refresh();

    $this->assertSame('123.456.789-09', $individual->cpf);
    $this->assertNull($individual->document_absence_reason_id);
});

test('refuses parent with cpf state without number', function () {
    $response = $this->postResource('/module/Api/Pessoa', ($this->parentRequest)('Pai Recusado Sem Numero', [
        'possui_cpf' => 1,
    ]));

    $response->assertOk()->assertJsonPath('msgs.0.msg', 'É necessário o preenchimento do CPF.');
    $this->assertFalse(LegacyPerson::query()->where('nome', 'Pai Recusado Sem Numero')->exists());
});

test('refuses parent without cpf and without reason', function () {
    $response = $this->postResource('/module/Api/Pessoa', ($this->parentRequest)('Pai Recusado Sem Motivo', [
        'possui_cpf' => 0,
    ]));

    $response->assertOk()->assertJsonPath('msgs.0.msg', 'É necessário informar o motivo da ausência do CPF.');
    $this->assertFalse(LegacyPerson::query()->where('nome', 'Pai Recusado Sem Motivo')->exists());
});

test('refuses system reason for new parent', function () {
    $systemReason = DocumentAbsenceReasonFactory::new()->create(['from_system' => true, 'requires_observation' => false]);

    $response = $this->postResource('/module/Api/Pessoa', ($this->parentRequest)('Pai Recusado Motivo Sistema', [
        'possui_cpf' => 0,
        'document_absence_reason_id' => $systemReason->getKey(),
    ]));

    $response->assertOk()->assertJsonPath('msgs.0.msg', 'O motivo da ausência do CPF informado não existe.');
    $this->assertFalse(LegacyPerson::query()->where('nome', 'Pai Recusado Motivo Sistema')->exists());
});

test('parent details treat zero cpf as without cpf', function () {
    $withCpf = LegacyIndividualFactory::new()->create(['cpf' => 12345678909]);
    $zeroCpf = LegacyIndividualFactory::new()->create(['cpf' => 0]);
    $nullCpf = LegacyIndividualFactory::new()->create(['cpf' => null]);

    $this->assertSame(1, ($this->parentDetails)($withCpf)->json('possui_cpf'));
    $this->assertSame(0, ($this->parentDetails)($zeroCpf)->json('possui_cpf'));
    $this->assertSame(0, ($this->parentDetails)($nullCpf)->json('possui_cpf'));
});
