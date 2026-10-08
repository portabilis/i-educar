<?php

use App\Jobs\EnrollmentsPromotionJob;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Bus;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    Bus::fake();
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->rule = $this->scenario->evaluationRule();
    $this->schoolClass = $this->scenario->schoolClass();
    $this->registration = $this->scenario->enrolled('Bia Borges', $this->schoolClass)->registration;
});

test('envia para a fila a promoção das matrículas que casam com os filtros', function () {
    $this->postJson(route('enrollments.promotion'), [
        'ano' => 2030,
        'escola' => $this->scenario->school->getKey(),
        'curso' => $this->scenario->course->getKey(),
        'serie' => $this->scenario->grade->getKey(),
        'turma' => $this->schoolClass->getKey(),
        'matricula' => $this->registration->getKey(),
        'situacaoMatricula' => 9,
        'regras_avaliacao_id' => $this->rule->getKey(),
        'atualizar_notas' => 1,
    ])
        ->assertOk()
        ->assertJson(['status' => 'success']);

    Bus::assertBatched(fn ($batch) => $batch->jobs->count() === 1 && $batch->jobs->first() instanceof EnrollmentsPromotionJob);
});

test('avisa quando nenhuma matrícula casa com os filtros', function () {
    $this->postJson(route('enrollments.promotion'), ['ano' => 2029])
        ->assertOk()
        ->assertExactJson(['message' => 'Não foi possível encontrar matrículas com os parâmetros enviados', 'status' => 'notice']);

    Bus::assertNothingBatched();
});
