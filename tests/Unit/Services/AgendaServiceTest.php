<?php

use App\Models\LegacyAgendaCommitment;
use App\Models\LegacyPerson;
use App\Services\AgendaService;
use Database\Factories\LegacyEmployeeFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->service = new AgendaService;
    // A agenda é de um servidor; o nome dela vem da pessoa.
    $this->employee = function (string $name) {
        $person = LegacyEmployeeFactory::new()->create()->ref_cod_pessoa_fj;
        LegacyPerson::query()->whereKey($person)->update(['nome' => $name]);

        return $person;
    };
    $this->person = ($this->employee)('Ana Maria Alves');
    $this->agenda = $this->service->firstOrCreate($this->person);
    $this->other = $this->service->firstOrCreate(($this->employee)('Bia'));

    $this->service->createCommitment($this->agenda, $this->person, null, 'Reunião', null, '10/03/2030', '08:00');
    $this->commitment = LegacyAgendaCommitment::query()->whereAgenda($this->agenda->getKey())->value('cod_agenda_compromisso');
});

test('o nome da agenda usa o primeiro e o último nome da pessoa', function () {
    expect($this->agenda->nm_agenda)->toBe('Ana Alves')
        ->and($this->other->nm_agenda)->toBe('Bia');
});

test('sem agenda não há permissão e o editor zero sempre pode', function () {
    expect($this->service->hasPermission($this->person, null))->toBeFalse()
        ->and($this->service->hasPermission(0, $this->other))->toBeTrue();
});

test('não mexe em compromisso de outra agenda nem em compromisso inexistente', function () {
    expect($this->service->updateCommitment($this->other, 0, $this->commitment, 'Outro', null, '10/03/2030', '09:00'))->toBe('')
        ->and($this->service->saveNoteAsCommitment($this->other, 0, $this->commitment, '10:00'))->toBe('')
        ->and($this->service->restoreVersion($this->other, $this->commitment, 1))->toBe('')
        ->and($this->service->updateCommitment($this->agenda, 0, 0, 'Outro', null, '10/03/2030', '09:00'))->toBe('');
});

test('a edição devolve o erro de validação sem alterar o compromisso', function () {
    expect($this->service->updateCommitment($this->agenda, 0, $this->commitment, null, null, '10/03/2030', '09:00'))
        ->toBe('Você deve preencher o campo Título ou o campo Descrição<br>')
        ->and(LegacyAgendaCommitment::query()->whereCommitment($this->commitment)->count())->toBe(1);
});

test('data inválida não tem compromissos no dia', function () {
    expect($this->service->getDayCommitments($this->agenda, '31/02/2030'))->toBeEmpty();
});
