<?php

use App\Models\LegacyIndividual;
use Database\Factories\DocumentAbsenceReasonFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\DiarioApiRequestTestTrait;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, DiarioApiRequestTestTrait::class);

beforeEach(function () {
    $this->cpfRequest = function (int $personId, array $state): array {
        return array_merge([
            'oper' => 'post',
            'resource' => 'pessoa-cpf',
            'pessoa_id' => $personId,
            'cpf' => '',
            'document_absence_reason_id' => '',
            'document_absence_observation' => '',
        ], $state);
    };
});

test('informing cpf clears reason and keeps other data', function () {
    $reason = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => false]);
    $individual = LegacyIndividualFactory::new()->create(['cpf' => null, 'sexo' => 'F', 'data_nasc' => '2010-05-10']);
    LegacyIndividual::query()->whereKey($individual->getKey())->update(['document_absence_reason_id' => $reason->getKey()]);

    $response = $this->postResource('/module/Api/Pessoa', ($this->cpfRequest)($individual->getKey(), [
        'possui_cpf' => 1,
        'cpf' => '123.456.789-09',
    ]));

    $response->assertOk()->assertJsonPath('pessoa_id', $individual->getKey());

    $individual->refresh();

    $this->assertSame('123.456.789-09', $individual->cpf);
    $this->assertNull($individual->document_absence_reason_id);
    $this->assertSame('F', $individual->sexo);
    $this->assertSame('2010-05-10', $individual->data_nasc->format('Y-m-d'));
    $this->assertCount(1, $individual->timelines);
    $this->assertSame(['CPF' => null, 'Motivo da ausência' => $reason->name], $individual->timelines->first()->payload['before']);
    $this->assertSame(['CPF' => '123.456.789-09'], $individual->timelines->first()->payload['after']);
});

test('zero cpf marked without cpf clears the cpf and saves the reason', function () {
    $reason = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => false]);
    $individual = LegacyIndividualFactory::new()->create(['cpf' => 0]);

    $response = $this->postResource('/module/Api/Pessoa', ($this->cpfRequest)($individual->getKey(), [
        'possui_cpf' => 0,
        'document_absence_reason_id' => $reason->getKey(),
    ]));

    $response->assertOk()->assertJsonPath('pessoa_id', $individual->getKey());

    $individual->refresh();

    $this->assertNull($individual->getRawOriginal('cpf'));
    $this->assertSame($reason->getKey(), $individual->document_absence_reason_id);
});

test('refuses system reason not saved for the person', function () {
    $systemReason = DocumentAbsenceReasonFactory::new()->create(['from_system' => true, 'requires_observation' => false]);
    $individual = LegacyIndividualFactory::new()->create(['cpf' => null]);

    $response = $this->postResource('/module/Api/Pessoa', ($this->cpfRequest)($individual->getKey(), [
        'possui_cpf' => 0,
        'document_absence_reason_id' => $systemReason->getKey(),
    ]));

    $response->assertOk()->assertJsonPath('msgs.0.msg', 'O motivo da ausência do CPF informado não existe.');
    $this->assertNull($individual->refresh()->document_absence_reason_id);
});

test('refuses cpf already used by another person', function () {
    $other = LegacyIndividualFactory::new()->create(['cpf' => 12345678909]);
    $individual = LegacyIndividualFactory::new()->create(['cpf' => null]);

    $response = $this->postResource('/module/Api/Pessoa', ($this->cpfRequest)($individual->getKey(), [
        'possui_cpf' => 1,
        'cpf' => '123.456.789-09',
    ]));

    $response->assertOk()->assertJsonPath('msgs.0.msg', "CPF já utilizado pela pessoa {$other->getKey()}.");
    $this->assertNull($individual->refresh()->getRawOriginal('cpf'));
});

test('accepts inactive reason already saved for the person', function () {
    $reason = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => false]);
    $individual = LegacyIndividualFactory::new()->create(['cpf' => null]);
    LegacyIndividual::query()->whereKey($individual->getKey())->update(['document_absence_reason_id' => $reason->getKey()]);
    $reason->delete();

    $response = $this->postResource('/module/Api/Pessoa', ($this->cpfRequest)($individual->getKey(), [
        'possui_cpf' => 0,
        'document_absence_reason_id' => $reason->getKey(),
    ]));

    $response->assertOk()->assertJsonPath('pessoa_id', $individual->getKey());
    $this->assertSame($reason->getKey(), $individual->refresh()->document_absence_reason_id);
});

test('saving without change does not register timeline', function () {
    $reason = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => false]);
    $individual = LegacyIndividualFactory::new()->create(['cpf' => null]);
    LegacyIndividual::query()->whereKey($individual->getKey())->update(['document_absence_reason_id' => $reason->getKey()]);

    $response = $this->postResource('/module/Api/Pessoa', ($this->cpfRequest)($individual->getKey(), [
        'possui_cpf' => 0,
        'document_absence_reason_id' => $reason->getKey(),
    ]));

    $response->assertOk()->assertJsonPath('pessoa_id', $individual->getKey());
    $this->assertCount(0, $individual->refresh()->timelines);
});

test('refuses unknown person', function () {
    $response = $this->postResource('/module/Api/Pessoa', ($this->cpfRequest)(0, [
        'possui_cpf' => 1,
        'cpf' => '123.456.789-09',
    ]));

    $response->assertOk()->assertJsonPath('msgs.0.msg', 'Pessoa não encontrada.');
});

test('refuses user without permission', function () {
    $individual = LegacyIndividualFactory::new()->create(['cpf' => null]);
    $data = array_merge([
        'access_key' => config('legacy.apis.access_key'),
        'secret_key' => config('legacy.apis.secret_key'),
    ], ($this->cpfRequest)($individual->getKey(), [
        'possui_cpf' => 1,
        'cpf' => '123.456.789-09',
    ]));
    $_POST = $data;

    $response = $this->actingAs(LegacyUserFactory::new()->institutional()->create())
        ->post('/module/Api/Pessoa?' . http_build_query($data));

    $response->assertOk()->assertJsonPath('msgs.0.msg', "Usuário sem permissão para cadastrar 'pessoa-cpf'.");
    $this->assertNull($individual->refresh()->getRawOriginal('cpf'));
});
