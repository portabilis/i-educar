<?php

use App\Jobs\BatchExemptionJob;
use App\Listeners\LoginLegacySession;
use App\Process;
use App\User;
use Database\Factories\LegacyExemptionTypeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Bus;
use Tests\Request\Support\EnrollmentScenario;
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

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);
    $this->schoolClass = $this->scenario->schoolClass();
    $this->discipline = $this->scenario->discipline('Matemática');
    $this->enrollment = $this->scenario->enrolled('Bia Borges', $this->schoolClass);

    $this->exempt = fn (array $overrides = []) => $this->post(route('batch-exemption.exempt'), $overrides + [
        'ano' => 2030,
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_escola' => $this->scenario->school->getKey(),
        'ref_cod_curso' => $this->scenario->course->getKey(),
        'ref_cod_serie' => $this->scenario->grade->getKey(),
        'ref_cod_turma' => $this->schoolClass->getKey(),
        'situacao' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
        'ref_cod_componente_curricular' => [$this->discipline->getKey()],
        'exemption_type' => LegacyExemptionTypeFactory::new()->create()->getKey(),
        'stage' => [1],
        'stage_type' => DB::table('pmieducar.modulo')->where('ref_cod_instituicao', $this->scenario->institution)->value('cod_modulo'),
        'observacoes' => 'Dispensa em lote',
        'manter_frequencias' => 1,
    ]);
});

test('a tela de dispensa em lote abre para administradores e barra usuários de escola', function () {
    $this->get(route('batch-exemption.index'))
        ->assertOk()
        ->assertViewHas('stageTypes');

    ($this->login)(LegacyUserFactory::new()->school()->withAccess(Process::BATCH_EXEMPTION)->create());

    $this->from('/intranet/educar_configuracoes_index.php')
        ->get(route('batch-exemption.index'))
        ->assertRedirect('/intranet/educar_configuracoes_index.php')
        ->assertSessionHasErrors(['Error' => 'Você não tem permissão para acessar este recurso']);
});

test('a dispensa em lote valida os campos', function () {
    $this->post(route('batch-exemption.exempt'), ['ano' => 'abc'])
        ->assertSessionHasErrors([
            'ano' => 'Você precisa informar um ano válido.',
            'ref_cod_instituicao' => 'Você precisa selecionar a instituição.',
            'ref_cod_componente_curricular' => 'Você precisa selecionar pelo menos um componente curricular.',
            'exemption_type' => 'Você precisa selecionar o tipo de dispensa.',
            'stage' => 'Você precisa selecionar pelo menos uma etapa.',
            'ref_cod_escola' => 'Você precisa selecionar uma escola.',
            'stage_type' => 'Você precisa selecionar o tipo de etapa.',
        ]);

    $this->post(route('batch-exemption.exempt'), [])
        ->assertSessionHasErrors(['ano' => 'Você precisa informar o ano']);
});

test('avisa quando os filtros não encontram matrículas enturmadas', function () {
    ($this->exempt)(['ref_cod_turma' => $this->scenario->schoolClass()->getKey()])
        ->assertRedirect(route('batch-exemption.index'))
        ->assertSessionHas('error', 'Nenhuma matrícula encontrada com os filtros selecionados');

    Bus::assertNotDispatched(BatchExemptionJob::class);
});

test('despacha o job de dispensa para as matrículas filtradas', function () {
    ($this->exempt)()
        ->assertRedirect(route('batch-exemption.index'))
        ->assertSessionHas('success', 'Serão criadas dispensas para 1 matrículas. Você será notificado no final do processo');

    Bus::assertDispatched(BatchExemptionJob::class, fn (BatchExemptionJob $job) => $job->getUser()->is($this->user) && $job->tags() === [DB::getDefaultConnection(), 'batch-exemption']);
});
