<?php

use App\Models\LegacyIndividual;
use App\Services\IndividualCpfStateService;
use Database\Factories\DocumentAbsenceReasonFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\TimelineFactory;
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

test('validate refuses an inactive reason that is not the saved one', function () {
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

test('validate accepts an inactive reason already saved for the person', function () {
    $reason = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => false]);
    $individual = LegacyIndividualFactory::new()->create([
        'document_absence_reason_id' => $reason->getKey(),
    ]);
    $reason->delete();

    $error = $this->service->validate([
        'possui_cpf' => false,
        'cpf' => null,
        'document_absence_reason_id' => $reason->getKey(),
        'document_absence_observation' => null,
    ], $individual->getKey());

    $this->assertNull($error);
});

test('validate refuses the system reason that is not the saved one', function () {
    $reason = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => false, 'from_system' => true]);

    $error = $this->service->validate([
        'possui_cpf' => false,
        'cpf' => null,
        'document_absence_reason_id' => $reason->getKey(),
        'document_absence_observation' => null,
    ]);

    $this->assertSame(['document_absence_reason_id' => 'O motivo da ausência do CPF informado não existe.'], $error);
});

test('validate accepts the system reason already saved for the person', function () {
    $reason = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => false, 'from_system' => true]);
    $individual = LegacyIndividualFactory::new()->create([
        'document_absence_reason_id' => $reason->getKey(),
    ]);

    $error = $this->service->validate([
        'possui_cpf' => false,
        'cpf' => null,
        'document_absence_reason_id' => $reason->getKey(),
        'document_absence_observation' => null,
    ], $individual->getKey());

    $this->assertNull($error);
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

test('reason options start with the placeholder and mark the saved inactive reason', function () {
    $active = DocumentAbsenceReasonFactory::new()->create(['name' => 'Zzz motivo ativo']);
    $inactive = DocumentAbsenceReasonFactory::new()->create(['name' => 'Aaa motivo inativo']);
    $inactive->delete();
    $otherInactive = DocumentAbsenceReasonFactory::new()->create();
    $otherInactive->delete();

    $options = $this->service->reasonOptions($inactive->getKey());
    $keys = array_keys($options);

    $this->assertSame('Selecione', $options['']);
    $this->assertSame('', array_key_first($options));
    $this->assertSame($active->name, $options[$active->getKey()]);
    $this->assertSame($inactive->name . ' (inativo)', $options[$inactive->getKey()]);
    $this->assertArrayNotHasKey($otherInactive->getKey(), $options);
    $this->assertLessThan(array_search($active->getKey(), $keys, true), array_search($inactive->getKey(), $keys, true));
});

test('reason options hide the system reason unless it is the saved one', function () {
    $system = DocumentAbsenceReasonFactory::new()->create(['from_system' => true]);

    $this->assertArrayNotHasKey($system->getKey(), $this->service->reasonOptions());
    $this->assertSame($system->name, $this->service->reasonOptions($system->getKey())[$system->getKey()]);
});

test('reasons requiring observation include inactive ones', function () {
    $requires = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => true]);
    $requiresInactive = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => true]);
    $requiresInactive->delete();
    $doesNotRequire = DocumentAbsenceReasonFactory::new()->create(['requires_observation' => false]);

    $ids = $this->service->reasonsRequiringObservation();

    $this->assertContains($requires->getKey(), $ids);
    $this->assertContains($requiresInactive->getKey(), $ids);
    $this->assertNotContains($doesNotRequire->getKey(), $ids);
});

test('situation with cpf returns the formatted number', function () {
    $individual = LegacyIndividualFactory::new()->create(['cpf' => 12345678909]);

    $this->assertSame(['type' => 'with_cpf', 'cpf' => '123.456.789-09'], $this->service->situation($individual));
});

test('situation without cpf and without reason is an unjustified absence', function () {
    $individual = LegacyIndividualFactory::new()->create(['cpf' => null, 'document_absence_reason_id' => null]);

    $this->assertSame(['type' => 'unjustified_absence'], $this->service->situation($individual));
});

test('situation with the system reason is an unjustified absence', function () {
    $reason = DocumentAbsenceReasonFactory::new()->create(['from_system' => true]);
    $individual = LegacyIndividualFactory::new()->create(['cpf' => null, 'document_absence_reason_id' => $reason->getKey()]);

    $this->assertSame(['type' => 'unjustified_absence'], $this->service->situation($individual));
});

test('situation with zero cpf and without reason is an unjustified absence', function () {
    $individual = LegacyIndividualFactory::new()->create(['cpf' => 0]);

    $this->assertSame(['type' => 'unjustified_absence'], $this->service->situation($individual));
});

test('situation with zero cpf and a reason is a justified absence', function () {
    $reason = DocumentAbsenceReasonFactory::new()->create();
    $individual = LegacyIndividualFactory::new()->create(['cpf' => 0]);
    LegacyIndividual::query()->whereKey($individual->getKey())->update(['document_absence_reason_id' => $reason->getKey()]);

    $this->assertSame([
        'type' => 'justified_absence',
        'reason' => $reason->name,
        'user' => null,
        'date' => null,
    ], $this->service->situation($individual->refresh()));
});

test('situation with a reason brings the author and date of the last change', function () {
    $reason = DocumentAbsenceReasonFactory::new()->create();
    $individual = LegacyIndividualFactory::new()->create(['cpf' => null, 'document_absence_reason_id' => $reason->getKey()]);
    TimelineFactory::new()->create(['model_id' => $individual->getKey(), 'payload' => ['user' => ['name' => 'Usuário antigo']]]);
    $last = TimelineFactory::new()->create(['model_id' => $individual->getKey(), 'payload' => ['user' => ['name' => 'Usuário recente']]]);

    $this->assertSame([
        'type' => 'justified_absence',
        'reason' => $reason->name,
        'user' => 'Usuário recente',
        'date' => $last->created_at->format('d/m/Y'),
    ], $this->service->situation($individual));
});

test('situation with a reason and no history has no author nor date', function () {
    $reason = DocumentAbsenceReasonFactory::new()->create();
    $individual = LegacyIndividualFactory::new()->create(['cpf' => null, 'document_absence_reason_id' => $reason->getKey()]);

    $this->assertSame([
        'type' => 'justified_absence',
        'reason' => $reason->name,
        'user' => null,
        'date' => null,
    ], $this->service->situation($individual));
});
