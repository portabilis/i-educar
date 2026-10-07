<?php

use App\User;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    // O serviço de enturmação é resolvido com o usuário autenticado.
    $this->actingAs(User::query()->findOrFail(LegacyUserFactory::new()->admin()->create()->getKey()));
});

test('reordena os sequenciais das enturmações da matrícula', function () {
    $registration = LegacyRegistrationFactory::new()->create();
    $third = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'sequencial' => 3]);
    $fifth = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'sequencial' => 5]);

    $this->artisan('registration:reorder', ['registration' => $registration->getKey()])
        ->expectsOutput("Enrollments reordered to registration: {$registration->getKey()}")
        ->assertExitCode(0);

    expect($third->refresh()->sequencial)->toBe(1)
        ->and($fifth->refresh()->sequencial)->toBe(2);
});

test('avisa quando a matrícula não existe', function () {
    $this->artisan('registration:reorder', ['registration' => 999999999]);
})->throws(ModelNotFoundException::class);
