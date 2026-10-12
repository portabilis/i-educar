<?php

use App\Models\LegacyAcademicYearStage;
use Database\Factories\LegacyCalendarDayFactory;
use Database\Factories\LegacyCalendarDayNoteFactory;
use Database\Factories\LegacyCalendarDayReasonFactory;
use Database\Factories\LegacyCalendarYearFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->calendar = LegacyCalendarYearFactory::new()->create();
    $this->day = ['dia' => now()->day, 'mes' => now()->month, 'ano' => $this->calendar->ano, 'ref_cod_calendario_ano_letivo' => $this->calendar->getKey()];
});

test('mostra o calendário do ano letivo da escola', function () {
    LegacyCalendarDayFactory::new()->create(['ref_cod_calendario_ano_letivo' => $this->calendar]);

    $this->post('/intranet/educar_calendario_ano_letivo_lst.php', [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'ref_cod_escola' => $this->calendar->ref_cod_escola,
        'ano' => $this->calendar->ano,
        'mes' => now()->month,
        'cod_calendario_ano_letivo' => $this->calendar->getKey(),
    ])->assertOk();
});

test('abre a tela de calendário sem filtro', function () {
    $this->get('/intranet/educar_calendario_ano_letivo_lst.php')->assertOk();
});

// O calendário de 2030 da escola do cenário: etapas de 01/02 a 30/11.
function calendarioDaEscola($test): array
{
    $scenario = EnrollmentScenario::for($test->user, 2030);
    LegacyAcademicYearStage::query()->where('ref_ref_cod_escola', $scenario->school->getKey())->where('sequencial', 2)->update(['data_fim' => '2030-11-30']);
    $calendar = LegacyCalendarYearFactory::new()->create(['ref_cod_escola' => $scenario->school, 'ano' => 2030]);

    return [$scenario, $calendar];
}

function listaCalendario($test, $scenario, $calendar, int $month, array $extra = [])
{
    return $test->post('/intranet/educar_calendario_ano_letivo_lst.php', $extra + [
        'ref_cod_instituicao' => $scenario->institution,
        'ref_cod_escola' => $scenario->school->getKey(),
        'ano' => 2030,
        'mes' => $month,
        'cod_calendario_ano_letivo' => $calendar->getKey(),
    ])->assertOk();
}

test('mostra os dias com motivo, as turmas marcadas, as descrições e as anotações do mês', function () {
    [$scenario, $calendar] = calendarioDaEscola($this);
    $alfa = $scenario->schoolClass(['nm_turma' => 'Turma Alfa']);
    $extra = LegacyCalendarDayReasonFactory::new()->create(['ref_cod_escola' => $scenario->school, 'tipo' => 'e', 'nm_motivo' => 'Sábado letivo']);
    $off = LegacyCalendarDayReasonFactory::new()->create(['ref_cod_escola' => $scenario->school, 'tipo' => 'n', 'nm_motivo' => 'Feriado']);
    LegacyCalendarDayFactory::new()->create(['ref_cod_calendario_ano_letivo' => $calendar, 'mes' => 5, 'dia' => 4, 'ref_cod_calendario_dia_motivo' => $extra, 'descricao' => 'Reposição']);
    LegacyCalendarDayFactory::new()->create(['ref_cod_calendario_ano_letivo' => $calendar, 'mes' => 5, 'dia' => 1, 'ref_cod_calendario_dia_motivo' => $off, 'descricao' => 'Dia do trabalho']);
    LegacyCalendarDayFactory::new()->create(['ref_cod_calendario_ano_letivo' => $calendar, 'mes' => 5, 'dia' => 20, 'ref_cod_calendario_dia_motivo' => null, 'descricao' => 'Só uma observação']);
    DB::table('modules.calendario_turma')->insert(['calendario_ano_letivo_id' => $calendar->getKey(), 'ano' => 2030, 'mes' => 5, 'dia' => 4, 'turma_id' => $alfa->getKey()]);
    LegacyCalendarDayNoteFactory::new()->create(['ref_ref_cod_calendario_ano_letivo' => $calendar, 'ref_mes' => 5, 'ref_dia' => 4]);

    listaCalendario($this, $scenario, $calendar, 5)
        ->assertSee('Escola Aurora')
        ->assertSee('Dia Extra-Letivo')
        ->assertSee('Sábado letivo')
        ->assertSee('Turma Alfa')
        ->assertSee('Dia Não Letivo')
        ->assertSee('Feriado')
        ->assertSee('Só uma observação')
        ->assertSee('Dias Letivos');
});

test('marca o início e o término do ano letivo e os meses fora dele como não letivos', function (int $month, string $sees, ?string $doesntSee) {
    [$scenario, $calendar] = calendarioDaEscola($this);

    $response = listaCalendario($this, $scenario, $calendar, $month)->assertSee($sees);

    if ($doesntSee) {
        $response->assertDontSee($doesntSee);
    }
})->with([
    'mês de início' => [2, 'Início Ano Letivo', null],
    'antes do início' => [1, 'Não Letivo', 'Início Ano Letivo'],
    'mês de término' => [11, 'Término Ano Letivo', null],
    'depois do término' => [12, 'Não Letivo', 'Término Ano Letivo'],
]);

test('reabre o último calendário guardado na sessão e oferece voltar quando o ano não tem calendário', function () {
    [$scenario, $calendar] = calendarioDaEscola($this);
    $session = ['cod_calendario_ano_letivo' => $calendar->getKey(), 'ref_cod_instituicao' => $scenario->institution, 'ref_cod_escola' => $scenario->school->getKey(), 'ano' => 2030, 'mes' => 5];

    $this->withSession(['calendario' => $session])
        ->get('/intranet/educar_calendario_ano_letivo_lst.php')
        ->assertOk()
        ->assertSee('Escola Aurora');

    $this->withSession(['calendario' => $session + ['ultimo_valido' => 1]])
        ->post('/intranet/educar_calendario_ano_letivo_lst.php', ['ref_cod_instituicao' => $scenario->institution, 'ref_cod_escola' => $scenario->school->getKey(), 'ano' => 2031, 'mes' => 5])
        ->assertOk()
        ->assertSee('Sem Calendário para o ano selecionado')
        ->assertSee('value="Voltar"', false);
});

test('o usuário de escola sem calendário vê o aviso, e o de biblioteca não acessa', function () {
    [$scenario] = calendarioDaEscola($this);
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs(LegacyUserFactory::new()->school()->withSchool($scenario->school)->withAccess(620)->create());
    $this->post('/intranet/educar_calendario_ano_letivo_lst.php', ['ref_cod_instituicao' => $scenario->institution, 'ref_cod_escola' => $scenario->school->getKey(), 'ano' => 2031, 'mes' => 5])
        ->assertOk()
        ->assertSee('Sem Calendário Letivo');

    $library = LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]);
    $this->actingAs(LegacyUserFactory::new()->withAccess(620)->create(['ref_cod_tipo_usuario' => $library->getKey()]));
    $this->get('/intranet/educar_calendario_ano_letivo_lst.php')
        ->assertOk()
        ->assertSee('Usuário sem permissão para acessar esta página');
});
