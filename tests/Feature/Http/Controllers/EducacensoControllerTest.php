<?php

use App\Listeners\LoginLegacySession;
use App\Models\LegacyInstitution;
use App\User;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\SchoolInepFactory;
use Database\Factories\SchoolManagerFactory;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    app(LoginLegacySession::class)->handle(new Login('web', User::query()->findOrFail($this->user->getKey()), false));

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);
    SchoolInepFactory::new()->create(['cod_escola' => $this->scenario->school, 'cod_escola_inep' => 42000001]);

    // Só entram na consulta as enturmações anteriores à data de referência do Censo.
    LegacyInstitution::query()->whereKey($this->scenario->institution)->update(['data_educacenso' => '2030-05-29']);

    $schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma Censo']);
    $this->scenario->enrolled('Ana Censo', $schoolClass, ['data_enturmacao' => '2030-02-10']);

    EmployeeScenario::named('Bia Docente', $this->scenario->institution)
        ->classLink($schoolClass, [], [$this->scenario->discipline('Matemática')]);

    $manager = EmployeeScenario::named('Caio Gestor', $this->scenario->institution);
    SchoolManagerFactory::new()->create(['school_id' => $this->scenario->school, 'employee_id' => $manager->employee]);

    $this->consult = fn (array $query = []) => $this->get(route('educacenso.consult', $query))->assertOk();
});

test('sem registro, escola e ano mostra só os filtros', function () {
    ($this->consult)()
        ->assertViewHas('paginate', null)
        ->assertViewHas('record20', null);
});

test('consulta as turmas, os gestores, os docentes e os alunos da escola no Censo', function (string $record, string $variable, string $name) {
    ($this->consult)(['record' => $record, 'ref_cod_escola' => $this->scenario->school->getKey(), 'year' => 2030])
        ->assertViewHas($variable, fn ($items) => collect($items)->isNotEmpty())
        ->assertSee($name);
})->with([
    'turmas' => ['20', 'record20', 'Turma Censo'],
    'gestores' => ['40', 'record40', 'Caio Gestor'],
    'docentes' => ['50', 'record50', 'Bia Docente'],
    'alunos' => ['60', 'record60', 'Ana Censo'],
]);
