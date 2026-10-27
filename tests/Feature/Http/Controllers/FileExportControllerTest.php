<?php

use App\Jobs\FileExporterJob;
use App\Listeners\LoginLegacySession;
use App\Models\FileExport;
use App\User;
use Database\Factories\FileExportFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Bus;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    Bus::fake();

    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    app(LoginLegacySession::class)->handle(new Login('web', User::query()->findOrFail($this->user->getKey()), false));

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma A']);

    $this->form = fn (array $overrides = []) => $overrides + [
        'ano' => 2030,
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_escola' => $this->scenario->school->getKey(),
        'ref_cod_curso' => $this->scenario->course->getKey(),
        'ref_cod_serie' => $this->scenario->grade->getKey(),
        'ref_cod_turma' => $this->schoolClass->getKey(),
    ];
});

test('lista só as exportações de documentos do usuário', function () {
    $mine = FileExportFactory::new()->create(['user_id' => $this->user->getKey()]);
    FileExportFactory::new()->create(['user_id' => LegacyUserFactory::new()->create()->getKey()]);

    $this->get(route('file.export.index'))
        ->assertOk()
        ->assertViewHas('exports', fn ($exports) => $exports->pluck('id')->all() === [$mine->getKey()]);
});

test('o formulário de nova exportação abre', function () {
    $this->get(route('file.export.create'))
        ->assertOk()
        ->assertViewIs('file_export.create');
});

test('a exportação exige ano, instituição, escola, curso, série e turma', function () {
    $this->post(route('file.export.store'), ['ano' => 'abc'])
        ->assertSessionHasErrors([
            'ano' => 'Você precisa informar um ano válido.',
            'ref_cod_instituicao' => 'Você precisa selecionar a instituição.',
            'ref_cod_escola' => 'Você precisa selecionar uma escola.',
            'ref_cod_curso' => 'Você precisa selecionar um curso.',
            'ref_cod_serie' => 'Você precisa selecionar uma série.',
            'ref_cod_turma' => 'Você precisa selecionar uma turma.',
        ]);

    $this->post(route('file.export.store'), [])
        ->assertSessionHasErrors(['ano' => 'Você precisa informar o ano']);
});

test('a exportação da turma leva o nome da escola e da turma e despacha o job', function () {
    $this->post(route('file.export.store'), ($this->form)())
        ->assertRedirect(route('file.export.index'));

    $export = FileExport::query()->latest('id')->first();

    expect($export->user_id)->toBe($this->user->getKey())
        ->and($export->filename)->toBe('ESCOLA AURORA - TURMA A (2030)');

    Bus::assertDispatched(FileExporterJob::class);
});

test('a exportação de uma matrícula leva o nome do aluno', function () {
    $registration = $this->scenario->enrolled('Bia Borges-Souza', $this->schoolClass)->registration;

    $this->post(route('file.export.store'), ($this->form)(['matricula' => $registration->getKey()]))
        ->assertRedirect(route('file.export.index'));

    expect(FileExport::query()->latest('id')->value('filename'))->toBe("BIA BORGESSOUZA ({$registration->ref_cod_aluno})");
});
