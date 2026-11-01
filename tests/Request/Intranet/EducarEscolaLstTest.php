<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = ['ref_cod_instituicao' => $this->user->ref_cod_instituicao];
    $this->alfa = LegacySchoolFactory::new()->withName('Escola Alfa')->create($this->institution);
    $this->beta = LegacySchoolFactory::new()->withName('Escola Beta')->create($this->institution);

    $this->link = fn ($school) => 'educar_escola_det.php?cod_escola=' . $school->getKey();

    // Fora do nível de administrador, a instituição precisa estar ativa no
    // i-Educar para o usuário não ser desviado para a tela de suspensão.
    $this->actingAsLevel = function (LegacyUserFactory $factory) {
        LegacyGeneralConfigurationFactory::new()->create($this->institution + ['active_on_ieducar' => true]);

        $this->actingAs($factory->create());
    };
});

test('o administrador vê as escolas com a coluna e o filtro de instituição', function () {
    $response = $this->get('/intranet/educar_escola_lst.php')
        ->assertOk()
        ->assertSee('Escola - Listagem')
        ->assertSee(($this->link)($this->alfa), false)
        ->assertSee(($this->link)($this->beta), false);

    $form = LegacyForm::from($response->getContent());

    expect($form->options('ref_cod_instituicao'))->toHaveKey((string) $this->user->ref_cod_instituicao)
        ->and($form->hidden())->not->toHaveKey('ref_cod_instituicao');
});

test('filtra pelo nome da escola', function () {
    $this->get('/intranet/educar_escola_lst.php?nm_escola=Escola Alfa')
        ->assertOk()
        ->assertSee(($this->link)($this->alfa), false)
        ->assertDontSee(($this->link)($this->beta), false);
});

test('filtra pela instituição', function () {
    $other = LegacySchoolFactory::new()->withName('Escola Gama')->create([
        'ref_cod_instituicao' => LegacyInstitutionFactory::new()->create(),
    ]);

    $this->get('/intranet/educar_escola_lst.php?ref_cod_instituicao=' . $this->user->ref_cod_instituicao)
        ->assertOk()
        ->assertSee(($this->link)($this->alfa), false)
        ->assertDontSee(($this->link)($other), false);
});

test('não lista escola inativa', function () {
    $this->beta->update(['ativo' => 0]);

    $this->get('/intranet/educar_escola_lst.php')
        ->assertOk()
        ->assertSee(($this->link)($this->alfa), false)
        ->assertDontSee(($this->link)($this->beta), false);
});

test('pagina de dez em dez, em ordem de nome', function () {
    foreach (range(1, 9) as $n) {
        LegacySchoolFactory::new()->withName("Escola Delta {$n}")->create($this->institution);
    }

    $last = LegacySchoolFactory::new()->withName('Escola Zeta')->create($this->institution);

    $this->get('/intranet/educar_escola_lst.php?nm_escola=Escola')
        ->assertOk()
        ->assertSee(($this->link)($this->alfa), false)
        ->assertDontSee(($this->link)($last), false);

    $this->get('/intranet/educar_escola_lst.php?nm_escola=Escola&pagina_formulario=2')
        ->assertOk()
        ->assertDontSee(($this->link)($this->alfa), false)
        ->assertSee(($this->link)($last), false);
});

test('o usuário institucional vê as escolas da própria instituição, sem o filtro', function () {
    ($this->actingAsLevel)(LegacyUserFactory::new()->institutional()->withAccess(561));

    $response = $this->get('/intranet/educar_escola_lst.php')
        ->assertOk()
        ->assertSee(($this->link)($this->alfa), false)
        ->assertSee(($this->link)($this->beta), false)
        ->assertSee('go("educar_escola_cad.php")', false);

    // A instituição vai num campo oculto, e não numa lista de opções.
    $form = LegacyForm::from($response->getContent());

    expect($form->options('ref_cod_instituicao'))->toBe([])
        ->and($form->hidden()['ref_cod_instituicao'])->toBe((string) $this->user->ref_cod_instituicao);
});

test('o usuário de nível escola só vê as escolas a que está vinculado', function () {
    ($this->actingAsLevel)(LegacyUserFactory::new()->school()->withSchool($this->alfa)->withAccess(561));

    $this->get('/intranet/educar_escola_lst.php')
        ->assertOk()
        ->assertSee(($this->link)($this->alfa), false)
        ->assertDontSee(($this->link)($this->beta), false);
});

test('o usuário de nível biblioteca só vê as escolas a que está vinculado', function () {
    $library = LegacyUserFactory::new()->withSchool($this->beta)->withAccess(561)->state([
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
    ]);

    ($this->actingAsLevel)($library);

    $this->get('/intranet/educar_escola_lst.php')
        ->assertOk()
        ->assertSee(($this->link)($this->beta), false)
        ->assertDontSee(($this->link)($this->alfa), false);
});

test('não oferece o botão de nova escola a quem só pode visualizar', function () {
    ($this->actingAsLevel)(LegacyUserFactory::new()->institutional()->withAccess(561, modify: false, remove: false));

    $this->get('/intranet/educar_escola_lst.php')
        ->assertOk()
        ->assertSee(($this->link)($this->alfa), false)
        ->assertDontSee('go("educar_escola_cad.php")', false);
});
