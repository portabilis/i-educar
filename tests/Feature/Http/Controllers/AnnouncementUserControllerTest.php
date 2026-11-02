<?php

use App\Listeners\LoginLegacySession;
use App\Models\Announcement;
use App\User;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->login = function ($user) {
        $this->user = $user;
        $this->actingAs($user);
        app(LoginLegacySession::class)->handle(new Login('web', User::query()->findOrFail($user->getKey()), false));
    };
    ($this->login)(LegacyUserFactory::new()->admin()->create());
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->user->ref_cod_instituicao, 'active_on_ieducar' => true]);

    // A data de criação não é preenchível; entra depois, para ordenar.
    $this->announcement = fn (array $attributes = []) => tap(Announcement::query()->create($attributes + ['name' => 'Aviso', 'description' => 'x', 'created_by_user_id' => $this->user->getKey()]), fn ($announcement) => $announcement->forceFill(['created_at' => $attributes['created_at'] ?? now()])->saveQuietly());
    // A relação não carrega as datas do pivot; elas são lidas da tabela.
    $this->pivot = fn ($announcement) => DB::table('announcement_users')->where('announcement_id', $announcement->getKey())->where('user_id', $this->user->getKey())->first();
});

test('sem aviso, a tela e a confirmação voltam ao início', function () {
    $this->get(route('announcement.user.show'))->assertRedirect('/');
    $this->post(route('announcement.user.confirm'))->assertRedirect('/');
});

test('mostra o último aviso, marca a leitura e registra a confirmação', function () {
    ($this->announcement)(['name' => 'Antigo', 'created_at' => now()->subMinute()]);
    $announcement = ($this->announcement)(['name' => 'Atual']);

    $this->get(route('announcement.user.show'))
        ->assertOk()
        ->assertViewHas('announcement', fn ($given) => $given->is($announcement))
        ->assertViewHas('schools', []);

    expect(($this->pivot)($announcement)->read_at)->not->toBeNull();

    $this->post(route('announcement.user.confirm'))->assertRedirect('/');

    expect(($this->pivot)($announcement)->confirmed_at)->not->toBeNull();
});

test('com a opção de vagas, mostra as turmas com vaga das escolas do usuário', function () {
    $scenario = EnrollmentScenario::for($this->user, now()->year);
    $schoolClass = $scenario->schoolClass(['nm_turma' => 'Turma A', 'max_aluno' => 10]);
    $scenario->enrolled('Bia Borges', $schoolClass);
    $announcement = ($this->announcement)(['show_vacancy' => true]);

    $this->get(route('announcement.user.show'))
        ->assertOk()
        ->assertViewHas('schools', fn ($schools) => $schools->has('ESCOLA AURORA'));

    ($this->login)(LegacyUserFactory::new()->school()->withSchool($scenario->school)->create());

    $this->get(route('announcement.user.show'))
        ->assertOk()
        ->assertViewHas('schools', fn ($schools) => $schools->has('ESCOLA AURORA'));
});
