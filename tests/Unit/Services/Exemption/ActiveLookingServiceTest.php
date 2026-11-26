<?php

use App\Events\ActiveLookingChanged;
use App\Models\LegacyActiveLooking;
use App\Services\Exemption\ActiveLookingService;
use Database\Factories\LegacyActiveLookingFactory;
use Database\Factories\LegacyRegistrationFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    Event::fake([ActiveLookingChanged::class]);

    $this->service = new ActiveLookingService;
    $this->registration = LegacyRegistrationFactory::new()->create(['data_matricula' => '2026-03-01']);
    $this->activeLooking = fn (string $start, ?string $end = null) => LegacyActiveLookingFactory::new()->make([
        'registration_id' => $this->registration->getKey(),
        'start' => $start,
        'end' => $end,
    ]);
});

test('grava a busca ativa e avisa que ela foi criada', function () {
    $activeLooking = $this->service->store(($this->activeLooking)('2026-03-10', '2026-03-20'), $this->registration);

    expect($activeLooking->exists)->toBeTrue()
        ->and($activeLooking->getStartDate())->toBe('2026-03-10')
        ->and($activeLooking->getEndDate())->toBe('2026-03-20');

    Event::assertDispatched(ActiveLookingChanged::class, fn ($event) => $event->activeLooking->is($activeLooking)
        && $event->action === ActiveLookingChanged::ACTION_CREATED);
});

test('atualiza a busca ativa existente e avisa que ela foi alterada', function () {
    $activeLooking = $this->service->store(($this->activeLooking)('2026-03-10'), $this->registration);
    $activeLooking = LegacyActiveLooking::query()->findOrFail($activeLooking->getKey());
    $activeLooking->data_fim = '2026-03-25';

    $this->service->store($activeLooking, $this->registration);

    expect($activeLooking->fresh()->getEndDate())->toBe('2026-03-25');

    Event::assertDispatched(ActiveLookingChanged::class, fn ($event) => $event->action === ActiveLookingChanged::ACTION_UPDATED);
});

test('recusa o início antes da data da matrícula', function () {
    expect(fn () => $this->service->store(($this->activeLooking)('2026-02-28'), $this->registration))
        ->toThrow(ValidationException::class, 'Data de início não pode ser menor que a data matrícula.');

    expect(LegacyActiveLooking::query()->where('ref_cod_matricula', $this->registration->getKey())->exists())->toBeFalse();

    Event::assertNotDispatched(ActiveLookingChanged::class);
});

test('recusa o retorno antes do início', function () {
    expect(fn () => $this->service->store(($this->activeLooking)('2026-03-10', '2026-03-09'), $this->registration))
        ->toThrow(ValidationException::class, 'Data de retorno não pode ser menor que a data de início');

    Event::assertNotDispatched(ActiveLookingChanged::class);
});

test('recusa uma busca ativa no período de outra da mesma matrícula', function () {
    $this->service->store(($this->activeLooking)('2026-03-10', '2026-03-20'), $this->registration);

    expect(fn () => $this->service->store(($this->activeLooking)('2026-03-15'), $this->registration))
        ->toThrow(ValidationException::class, 'Já existe uma busca ativa lançada para o período selecionado.');
});

test('aceita o início no mesmo dia da matrícula e sem data de retorno', function () {
    $activeLooking = $this->service->store(($this->activeLooking)('2026-03-01'), $this->registration);

    expect($activeLooking->exists)->toBeTrue()
        ->and($activeLooking->getStartDate())->toBe('2026-03-01')
        ->and($activeLooking->getEndDate())->toBeNull();
});

test('exclui a busca ativa sem apagar o registro', function () {
    $activeLooking = $this->service->store(($this->activeLooking)('2026-03-10'), $this->registration);

    $this->service->delete($activeLooking);

    expect(LegacyActiveLooking::query()->find($activeLooking->getKey()))->toBeNull()
        ->and(LegacyActiveLooking::withTrashed()->find($activeLooking->getKey()))->not->toBeNull();
});
