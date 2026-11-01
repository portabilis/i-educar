<?php

use App\Jobs\DatabaseToCsvExporter;
use App\Listeners\LoginLegacySession;
use App\Models\Exporter\Employee;
use App\Models\Exporter\Enrollment;
use App\Models\Exporter\Export;
use App\Models\Exporter\SocialAssistance;
use App\Models\Exporter\Stage;
use App\Models\Exporter\Student;
use App\Models\Exporter\Teacher;
use App\Process;
use App\User;
use Database\Factories\Exporter\ExportFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    Bus::fake();

    $this->login = function ($user) {
        $this->user = $user;
        $this->actingAs($user);
        app(LoginLegacySession::class)->handle(new Login('web', User::query()->findOrFail($user->getKey()), false));
    };
    ($this->login)(LegacyUserFactory::new()->admin()->create());

    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $this->export = fn (array $data) => $this->post(route('export.export'), $data + ['agree' => 1, 'fields' => ['registration_id']]);
    $this->filters = fn () => Export::query()->latest('id')->first()->filters;
});

test('lista só as exportações do usuário, da mais recente para a mais antiga', function () {
    $mine = ExportFactory::new()->create(['user_id' => $this->user->getKey(), 'filename' => 'minha.csv']);
    ExportFactory::new()->create(['filename' => 'de_outro.csv', 'user_id' => LegacyUserFactory::new()->create()->getKey()]);

    $this->get(route('export.index'))
        ->assertOk()
        ->assertViewHas('exports', fn ($exports) => $exports->pluck('id')->all() === [$mine->getKey()]);
});

test('o formulário abre com o tipo escolhido, ou com matrículas por padrão', function () {
    $this->get(route('export.form'))
        ->assertOk()
        ->assertViewHas('exportation', fn ($exportation) => $exportation instanceof Enrollment);

    $this->get(route('export.form', ['type' => 3]))
        ->assertOk()
        ->assertViewHas('exportation', fn ($exportation) => $exportation instanceof Teacher);

    $this->get(route('export.form', ['type' => 99]))
        ->assertOk()
        ->assertViewHas('exportation', fn ($exportation) => $exportation instanceof Student);
});

test('com uma exportação pendente, não deixa começar outra', function () {
    config(['export.limit' => 1]);
    ExportFactory::new()->create(['user_id' => $this->user->getKey(), 'url' => null]);

    $this->get(route('export.form'))
        ->assertRedirect(route('export.index'))
        ->assertSessionHasErrors(['Error' => 'Você já possui 1 exportação(ões) pendente(s) e não é possível iniciar uma nova exportação. Por favor, aguarde a conclusão de uma delas antes de iniciar uma nova.']);
});

test('exportar exige aceitar o termo e escolher ao menos uma informação', function () {
    $this->post(route('export.export'), ['fields' => ['registration_id']])
        ->assertRedirect(route('export.form'));

    $this->post(route('export.export'), ['agree' => 1])
        ->assertRedirect('/exportacoes/novo')
        ->assertSessionHasErrors(['Error' => 'Selecione ao menos uma informação que deseja exportar para continuar.']);

    expect(Export::query()->count())->toBe(0);
    Bus::assertNotDispatched(DatabaseToCsvExporter::class);
});

test('exportar grava a exportação com os filtros e despacha o job', function (string $model, string $table, string $filename, array $extra, array $expected) {
    ($this->export)(['model' => $model, 'ano' => '2030', 'ref_cod_escola' => '7'] + $extra)
        ->assertRedirect(route('export.index'));

    $export = Export::query()->latest('id')->first();

    expect($export->model)->toBe($model)
        ->and($export->user_id)->toBe($this->user->getKey())
        ->and($export->fields)->toBe(['registration_id'])
        ->and($export->filename)->toStartWith($filename)
        ->and($export->filters)->toBe($expected);

    Bus::assertDispatched(DatabaseToCsvExporter::class);
})->with([
    'alunos' => [Student::class, 'exporter_student_grouped_registration', 'alunos_', ['situacao_matricula' => '3'], [
        ['column' => 'exporter_student_grouped_registration.status', 'operator' => '=', 'value' => '3'],
        ['column' => 'exporter_student_grouped_registration.year', 'operator' => '=', 'value' => 2030],
        ['column' => 'exporter_student_grouped_registration.school_id', 'operator' => 'in', 'value' => ['7']],
    ]],
    'matrículas de alunos transferidos' => [Enrollment::class, 'exporter_student', 'matriculas_', ['status' => '2'], [
        ['column' => 'exporter_student.year', 'operator' => '=', 'value' => 2030],
        ['column' => 'exporter_student.school_filter_id', 'operator' => '@>', 'value' => '7'],
    ]],
    'professores' => [Teacher::class, 'exporter_teacher', 'professores.csv', [], [
        ['column' => 'exporter_teacher.year', 'operator' => '=', 'value' => 2030],
        ['column' => 'exporter_teacher.school_id', 'operator' => 'in', 'value' => ['7']],
    ]],
    'assistência social' => [SocialAssistance::class, 'exporter_social_assistance', 'assistencia_social_', [], [
        ['column' => 'exporter_social_assistance.year', 'operator' => '=', 'value' => 2030],
        ['column' => 'exporter_social_assistance.school_id', 'operator' => 'in', 'value' => ['7']],
    ]],
    'calendário' => [Stage::class, 'exporter_stages', 'calendario.csv', [], [
        ['column' => 'exporter_stages.year', 'operator' => '=', 'value' => 2030],
        ['column' => 'exporter_stages.school_id', 'operator' => 'in', 'value' => ['7']],
    ]],
    'servidores' => [Employee::class, 'exporter_employee', 'servidores.csv', [], [
        ['column' => 'exporter_employee.year_id', 'operator' => '@>', 'value' => 2030],
        ['column' => 'exporter_employee.school_id', 'operator' => '@>', 'value' => '7'],
    ]],
]);

test('o usuário de escola sem escolher escola exporta só as escolas dele', function () {
    $school = LegacySchoolFactory::new()->create();
    ($this->login)(LegacyUserFactory::new()->school()->withSchool($school)->withAccess(Process::DATA_EXPORT)->create());
    $id = $school->getKey();

    ($this->export)(['model' => Student::class, 'status' => '2']);
    expect(($this->filters)())->toBe([['column' => 'exporter_student_grouped_registration.school_filter_id', 'operator' => '@>', 'value' => [$id]]]);

    ($this->export)(['model' => Enrollment::class]);
    expect(($this->filters)())->toBe([['column' => 'exporter_student.school_id', 'operator' => 'in', 'value' => [$id]]]);

    ($this->export)(['model' => Teacher::class]);
    expect(($this->filters)())->toBe([['column' => 'exporter_teacher.school_id', 'operator' => 'in', 'value' => [$id]]]);

    ($this->export)(['model' => Stage::class]);
    expect(($this->filters)())->toBe([['column' => 'exporter_stages.school_id', 'operator' => 'in', 'value' => [$id]]]);

    ($this->export)(['model' => Employee::class]);
    expect(($this->filters)())->toBe([['column' => 'exporter_employee.school_id', 'operator' => '@>', 'value' => [$id]]]);
});

test('o administrador sem filtros exporta tudo', function () {
    ($this->export)(['model' => Teacher::class]);

    expect(($this->filters)())->toBeNull();
});
