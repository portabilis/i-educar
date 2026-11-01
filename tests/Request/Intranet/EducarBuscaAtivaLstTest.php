<?php

use App\Process;
use Database\Factories\LegacyActiveLookingFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use iEducar\Modules\School\Model\ActiveLooking;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->registration = $this->scenario->enrolled('Ana Alves', $this->scenario->schoolClass())->registration;

    $this->looking = fn (array $attributes = []) => LegacyActiveLookingFactory::new()->create($attributes + [
        'registration_id' => $this->registration,
        'start' => '2030-03-02',
        'end' => '2030-03-20',
        'result' => ActiveLooking::ACTIVE_LOOKING_WITH_ABSENCE_RESULT,
    ]);

    $this->url = '/intranet/educar_busca_ativa_lst.php?ref_cod_matricula=' . $this->registration->getKey();
    $this->new = 'educar_busca_ativa_cad.php?ref_cod_matricula=' . $this->registration->getKey();
});

test('lista as buscas ativas da matrícula, da mais recente para a mais antiga, com as datas e o resultado', function () {
    $closed = ($this->looking)();
    $open = ($this->looking)(['start' => '2030-04-05', 'end' => null, 'result' => ActiveLooking::ACTIVE_LOOKING_IN_PROGRESS_RESULT]);
    $other = ($this->looking)(['registration_id' => $this->scenario->enrolled('Bia Borges', $this->scenario->schoolClass())->registration]);

    $this->get($this->url)
        ->assertOk()
        ->assertSeeInOrder(['05/04/2030', 'Em andamento', '02/03/2030', '20/03/2030', 'Retorno com ausência justificada'])
        ->assertSee("educar_busca_ativa_cad.php?id={$open->getKey()}&ref_cod_matricula={$this->registration->getKey()}", false)
        ->assertSee("educar_busca_ativa_cad.php?id={$closed->getKey()}&ref_cod_matricula={$this->registration->getKey()}", false)
        ->assertDontSee("educar_busca_ativa_cad.php?id={$other->getKey()}&", false)
        ->assertSee($this->new, false)
        ->assertSee('educar_matricula_det.php?cod_matricula=' . $this->registration->getKey(), false);
});

test('não oferece o botão Novo quando a matrícula não está em andamento', function () {
    $this->registration->update(['aprovado' => App_Model_MatriculaSituacao::ABANDONO]);

    $this->get($this->url)
        ->assertOk()
        ->assertDontSee($this->new, false)
        ->assertSee('educar_matricula_det.php?cod_matricula=' . $this->registration->getKey(), false);
});

test('não oferece o botão Novo a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(Process::ACTIVE_LOOKING, modify: false, remove: false)
            ->create(['ref_cod_instituicao' => $this->scenario->institution])
    );

    $this->get($this->url)->assertOk()->assertDontSee($this->new, false);
});

test('defeito: a listagem só alcança as 20 buscas mais recentes', function () {
    // O total entregue à paginação é a quantidade de linhas da própria
    // página, então a tela nunca oferece a segunda página.
    foreach (range(1, 21) as $day) {
        ($this->looking)(['start' => sprintf('2030-03-%02d', $day), 'end' => sprintf('2030-03-%02d', $day)]);
    }

    $this->get($this->url)
        ->assertOk()
        ->assertSee('21/03/2030')
        ->assertSee('02/03/2030')
        ->assertDontSee('01/03/2030')
        ->assertDontSee('pagina_formulario=2', false);

    $this->get($this->url . '&pagina_formulario=2')->assertOk()->assertSee('01/03/2030')->assertDontSee('02/03/2030');
});

test('volta para a listagem de matrículas sem o código da matrícula', function () {
    $this->get('/intranet/educar_busca_ativa_lst.php')->assertRedirect('educar_matricula_lst.php');
});

test('defeito: com uma matrícula que não existe a tela estoura', function () {
    $this->get('/intranet/educar_busca_ativa_lst.php?ref_cod_matricula=999999999')->assertStatus(500);
});

test('manda para o início quem não pode ver a tela ou é de biblioteca', function (Closure $user) {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);

    $this->actingAs($user->call($this));

    $this->get($this->url)->assertRedirect('/intranet/index.php');
})->with([
    'sem acesso ao processo' => [fn () => LegacyUserFactory::new()->institutional()->withAccess(578)->create(['ref_cod_instituicao' => $this->scenario->institution])],
    'nível biblioteca' => [fn () => LegacyUserFactory::new()->withAccess(Process::ACTIVE_LOOKING)->create([
        'ref_cod_instituicao' => $this->scenario->institution,
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
    ])],
]);
