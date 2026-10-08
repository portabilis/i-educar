<?php

use App\Listeners\LoginLegacySession;
use App\Models\LegacyIndividual;
use App\User;
use Database\Factories\LegacyUserFactory;
use Database\Factories\SchoolInepFactory;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\StudentScenario;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    app(LoginLegacySession::class)->handle(new Login('web', User::query()->findOrFail($this->user->getKey()), false));

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    SchoolInepFactory::new()->create(['cod_escola' => $this->scenario->school, 'cod_escola_inep' => 12345678]);
    $this->schoolClass = $this->scenario->schoolClass(['etapa_educacenso' => 14]);
});

test('a tela de exportação para o SEB abre', function () {
    $this->get(route('seb-export.index'))
        ->assertOk()
        ->assertViewIs('seb-export.index');
});

test('a exportação exige o ano e a instituição', function () {
    $this->post(route('seb-export.export'), ['ano' => 'abc'])
        ->assertSessionHasErrors([
            'ano' => 'O campo ano deve ser um ano válido.',
            'ref_cod_instituicao' => 'A instituição é obrigatória.',
        ]);

    $this->post(route('seb-export.export'), [])
        ->assertSessionHasErrors(['ano' => 'O ano é obrigatório.']);
});

test('exporta as matrículas em andamento do ano com a etapa convertida e os CPFs dos responsáveis', function () {
    $mother = StudentScenario::individual('Mãe da Bia', ['cpf' => '11111111111']);
    $father = StudentScenario::individual('Pai da Bia', ['cpf' => '44444444444']);
    $guardian = StudentScenario::individual('Avó da Bia', ['cpf' => '33333333333']);
    $registration = $this->scenario->enrolled('Bia Borges', $this->schoolClass)->registration;
    LegacyIndividual::query()->whereKey($registration->student->ref_idpes)->update([
        'cpf' => '22222222222',
        'nome_social' => 'Bia',
        'idpes_mae' => $mother->getKey(),
        'idpes_pai' => $father->getKey(),
        'idpes_responsavel' => $guardian->getKey(),
    ]);
    $this->scenario->enrolled('Caio Costa', $this->scenario->schoolClass(['etapa_educacenso' => null]));

    $response = $this->post(route('seb-export.export'), ['ano' => 2030, 'ref_cod_instituicao' => $this->scenario->institution, 'ref_cod_escola' => $this->scenario->school->getKey()])
        ->assertOk()
        ->assertHeader('Content-Disposition', 'attachment; filename="exportacao_seb.txt"');

    expect($response->getContent())->toBe("2030;12345678;22222222222;Bia;{$registration->getKey()};4;0;11111111111;1;44444444444;2;33333333333;3\n");
});
