<?php

use App\Services\IndividualCpfStateService;
use Database\Factories\DocumentAbsenceReasonFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->service = new IndividualCpfStateService;
});

test('normalize with cpf clears the absence reason and observation', function () {
    $state = $this->service->normalize([
        'possui_cpf' => true,
        'cpf' => '123.456.789-09',
        'document_absence_reason_id' => 1,
        'document_absence_observation' => 'Observação',
    ]);

    $this->assertSame('123.456.789-09', $state['cpf']);
    $this->assertNull($state['document_absence_reason_id']);
    $this->assertNull($state['document_absence_observation']);
});

test('normalize without cpf clears the cpf and keeps the reason', function () {
    $state = $this->service->normalize([
        'possui_cpf' => false,
        'cpf' => '123.456.789-09',
        'document_absence_reason_id' => 1,
        'document_absence_observation' => 'Observação',
    ]);

    $this->assertNull($state['cpf']);
    $this->assertSame(1, $state['document_absence_reason_id']);
    $this->assertSame('Observação', $state['document_absence_observation']);
});

test('validate requires the cpf number when the person has cpf', function () {
    $error = $this->service->validate([
        'possui_cpf' => true,
        'cpf' => null,
        'document_absence_reason_id' => null,
        'document_absence_observation' => null,
    ]);

    $this->assertSame(['id_federal' => 'É necessário o preenchimento do CPF.'], $error);
});

test('validate accepts the cpf number when the person has cpf', function () {
    $error = $this->service->validate([
        'possui_cpf' => true,
        'cpf' => '123.456.789-09',
        'document_absence_reason_id' => null,
        'document_absence_observation' => null,
    ]);

    $this->assertNull($error);
});

test('validate requires the reason when the person has no cpf', function () {
    $error = $this->service->validate([
        'possui_cpf' => false,
        'cpf' => null,
        'document_absence_reason_id' => null,
        'document_absence_observation' => null,
    ]);

    $this->assertSame(['document_absence_reason_id' => 'É necessário informar o motivo da ausência do CPF.'], $error);
});

test('validate refuses an inactive reason', function () {
    $reason = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => false]);
    $reason->delete();

    $error = $this->service->validate([
        'possui_cpf' => false,
        'cpf' => null,
        'document_absence_reason_id' => $reason->getKey(),
        'document_absence_observation' => null,
    ]);

    $this->assertSame(['document_absence_reason_id' => 'O motivo da ausência do CPF informado não existe.'], $error);
});

test('validate requires the observation when the reason asks for it', function () {
    $reason = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => true]);

    $state = [
        'possui_cpf' => false,
        'cpf' => null,
        'document_absence_reason_id' => $reason->getKey(),
        'document_absence_observation' => '  ',
    ];

    $this->assertSame(
        ['document_absence_observation' => 'Este motivo exige o preenchimento das observações.'],
        $this->service->validate($state)
    );

    $state['document_absence_observation'] = 'Documento em emissão';

    $this->assertNull($this->service->validate($state));
});

test('reason options start with the placeholder and list only the active reasons', function () {
    $active = DocumentAbsenceReasonFactory::new()->create();
    $inactive = DocumentAbsenceReasonFactory::new()->create();
    $inactive->delete();

    $options = $this->service->reasonOptions();

    $this->assertSame('Selecione', $options['']);
    $this->assertSame('', array_key_first($options));
    $this->assertSame($active->name, $options[$active->getKey()]);
    $this->assertArrayNotHasKey($inactive->getKey(), $options);
});

test('reasons requiring observation list only the active ones', function () {
    $requires = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => true]);
    $requiresInactive = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => true]);
    $requiresInactive->delete();
    $doesNotRequire = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => false]);

    $ids = $this->service->reasonsRequiringObservation();

    $this->assertContains($requires->getKey(), $ids);
    $this->assertNotContains($requiresInactive->getKey(), $ids);
    $this->assertNotContains($doesNotRequire->getKey(), $ids);
});
