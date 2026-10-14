<?php

use App\Models\LegacyCalendarYear;
use App\Models\LegacySchoolAcademicYear;
use Database\Factories\LegacyCalendarYearFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->calendar = LegacyCalendarYearFactory::new()->create();
    $this->day = ['dia' => now()->day, 'mes' => now()->month, 'ano' => $this->calendar->ano, 'ref_cod_calendario_ano_letivo' => $this->calendar->getKey()];
});

test('abre o formulário de um novo calendário', function () {
    $this->get('/intranet/educar_calendario_ano_letivo_cad.php')->assertOk();
});

test('abre o calendário existente e exclui', function () {
    $url = '/intranet/educar_calendario_ano_letivo_cad.php?cod_calendario_ano_letivo=' . $this->calendar->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post($url, $payload)->assertRedirect();

    expect($this->calendar->refresh()->ativo)->toBe(0);
});

test('oferece os anos letivos já encerrados da escola escolhida', function () {
    $school = LegacySchoolFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao]);
    LegacySchoolAcademicYearFactory::new()->create(['ref_cod_escola' => $school, 'ano' => 2029, 'andamento' => LegacySchoolAcademicYear::FINALIZED]);

    $this->get('/intranet/educar_calendario_ano_letivo_cad.php?ref_cod_escola=' . $school->getKey())
        ->assertOk()
        ->assertSee('value="2029"', false);
});

test('cria o calendário do ano letivo que tem etapas, edita o que já existe e recusa o ano sem etapas', function () {
    $scenario = EnrollmentScenario::for($this->user, 2030);
    $url = '/intranet/educar_calendario_ano_letivo_cad.php';
    $form = fn (array $overrides) => LegacyFields::from($this->get($url)->assertOk()->getContent(), $overrides + [
        'ref_cod_instituicao' => $scenario->institution,
        'ref_cod_escola' => $scenario->school->getKey(),
        'ano' => 2030,
    ]);

    $this->post($url, $form([]))
        ->assertRedirect("educar_calendario_ano_letivo_lst.php?ref_cod_escola={$scenario->school->getKey()}&ref_cod_instituicao={$scenario->institution}&ano=2030");

    $calendar = LegacyCalendarYear::query()->where('ref_cod_escola', $scenario->school->getKey())->where('ano', 2030)->first();
    expect($calendar)->not->toBeNull();

    $this->post($url, $form([]))->assertRedirect('educar_calendario_ano_letivo_lst.php');

    expect($calendar->refresh()->ref_usuario_exc)->toBe($this->user->getKey());

    $edit = '/intranet/educar_calendario_ano_letivo_cad.php?cod_calendario_ano_letivo=' . $calendar->getKey();
    $this->post($edit, LegacyFields::from($this->get($edit)->assertOk()->getContent(), ['ref_cod_escola' => $scenario->school->getKey(), 'ano' => 2030]))
        ->assertRedirect('educar_calendario_ano_letivo_lst.php');

    $this->post($url, $form(['ano' => 2031]))
        ->assertOk()
        ->assertSee('Não foi possível definir as datas de início e fim do ano letivo.');

    $this->post($edit, LegacyFields::from($this->get($edit)->assertOk()->getContent(), ['ref_cod_escola' => $scenario->school->getKey(), 'ano' => 2031]))
        ->assertOk()
        ->assertSee('Não foi possível definir as datas de início e fim do ano letivo.');
});
