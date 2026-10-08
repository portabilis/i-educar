<?php

use App\Listeners\LoginLegacySession;
use App\Models\Student;
use App\Process;
use App\User;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LogUnificationFactory;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()
        ->withAccess(Process::STUDENT_UNIFICATION)
        ->withAccess(Process::UNDO_STUDENT_UNIFICATION)
        ->create();
    $this->actingAs($this->user);

    // O layout lê da sessão o que o listener de login grava.
    app(LoginLegacySession::class)->handle(new Login('web', User::query()->findOrFail($this->user->getKey()), false));

    // O filtro por nome usa o nome da pessoa; a tela mostra o nome real.
    $this->student = fn (string $name) => LegacyStudentFactory::new()->create([
        'ref_idpes' => LegacyIndividualFactory::new()->create([
            'idpes' => LegacyPersonFactory::new()->create(['nome' => $name, 'tipo' => 'F']),
            'nome_social' => null,
        ]),
    ]);

    $this->unification = fn (array $attributes = []) => LogUnificationFactory::new()->create($attributes + [
        'type' => Student::class,
        'main_id' => ($this->student)('Ana Alves'),
        'duplicates_id' => '[]',
        'active' => true,
        'created_by' => $this->user->getKey(),
    ]);
});

test('lista as unificações de aluno, filtrando por escola e por nome', function () {
    $ana = ($this->student)('Ana Alves');
    $bia = ($this->student)('Bia Borges');
    $registration = LegacyRegistrationFactory::new()->create(['ref_cod_aluno' => $ana]);
    ($this->unification)(['main_id' => $ana]);
    ($this->unification)(['main_id' => $bia]);

    $this->get(route('student-log-unification.index'))
        ->assertOk()
        ->assertSee('Ana Alves')
        ->assertSee('Bia Borges');

    $this->get(route('student-log-unification.index', ['ref_cod_escola' => $registration->ref_ref_cod_escola]))
        ->assertOk()
        ->assertSee('Ana Alves')
        ->assertDontSee('Bia Borges');

    $this->get(route('student-log-unification.index', ['name' => 'borges']))
        ->assertOk()
        ->assertSee('Bia Borges')
        ->assertDontSee('Ana Alves');
});

test('mostra o detalhe da unificação, com quem fez e, se desfeita, quando e quem desfez', function () {
    $duplicate = ($this->student)('Bia Borges');
    $unification = ($this->unification)(['duplicates_id' => json_encode([$duplicate->getKey()])]);

    $this->get(route('student-log-unification.show', $unification))
        ->assertOk()
        ->assertSeeInOrder([
            'Aluno principal:', 'Ana Alves',
            'Aluno(s) unificado(s):', 'Bia Borges',
            'Data da unificação:', $unification->created_at->format('d/m/Y H:i:s'),
            'Feita por:',
        ])
        ->assertSee('Desfazer')
        ->assertDontSee('Desfeita em:');

    $undone = ($this->unification)(['active' => false, 'updated_by' => $this->user->getKey()]);

    $this->get(route('student-log-unification.show', $undone))
        ->assertOk()
        ->assertSeeInOrder(['Desfeita em:', $undone->updated_at->format('d/m/Y H:i:s'), 'Desfeita por:'])
        ->assertDontSee('Desfazer');
});

test('desfaz a unificação e volta para a lista', function () {
    $unification = ($this->unification)();

    $this->get(route('student-log-unification.undo', $unification))
        ->assertRedirect(route('student-log-unification.index'))
        ->assertSessionHas('success', 'Unificação desfeita com sucesso');

    expect($unification->refresh()->active)->toBeFalse();
});

test('quando não dá para desfazer, volta para o detalhe com o motivo', function () {
    $unification = ($this->unification)(['active' => false]);

    $this->get(route('student-log-unification.undo', $unification))
        ->assertRedirect(route('student-log-unification.show', $unification))
        ->assertSessionHasErrors();

    expect(session('errors')->first())->toBe('A unificação já foi desfeita anteriormente.');
});
