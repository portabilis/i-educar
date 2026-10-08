<?php

use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LegacyUserTypeFactory;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->school = EnrollmentScenario::for($this->user, 2030);
    $this->institution = $this->school->institution;
    $this->ana = EmployeeScenario::named('Ana Alves', $this->institution);
    $this->id = $this->ana->employee->getKey();

    $this->keys = "ref_cod_servidor={$this->id}&ref_cod_instituicao={$this->institution}";
    $this->detail = fn ($link) => 'educar_servidor_vinculo_turma_det.php?id=' . $link->getKey();
    // Sem ano na URL a tela filtra pelo ano atual; o cenário é de 2030.
    $this->list = fn (array $query = []) => $this->get("/intranet/educar_servidor_vinculo_turma_lst.php?{$this->keys}&" . http_build_query($query + ['ano' => 2030]))->assertOk();
});

test('sem ano na URL, lista só o ano atual', function () {
    $old = $this->ana->classLink($this->school->schoolClass(['nm_turma' => 'Turma Antiga']));
    $current = $this->ana->classLink($this->school->schoolClass(['nm_turma' => 'Turma Atual', 'ano' => now()->year]), ['ano' => now()->year]);

    $this->get("/intranet/educar_servidor_vinculo_turma_lst.php?{$this->keys}")
        ->assertOk()
        ->assertSee(($this->detail)($current), false)
        ->assertDontSee(($this->detail)($old), false);
});

test('lista os vínculos por escola, curso, série e turma, com a função, o tipo e os botões', function () {
    $beta = $this->ana->classLink($this->school->schoolClass(['nm_turma' => 'Turma Beta']), ['funcao_exercida' => 2, 'tipo_vinculo' => 1]);
    $alfa = $this->ana->classLink($this->school->schoolClass(['nm_turma' => 'Turma Alfa']));
    $other = EmployeeScenario::named('Bia Borges', $this->institution)->classLink($this->school->schoolClass());

    ($this->list)()
        ->assertSeeInOrder([
            '2030', 'Escola Aurora', 'Ensino Fundamental', 'Primeiro Ano', 'Turma Alfa', 'Docente', 'Contrato temporário',
            '2030', 'Turma Beta', 'Auxiliar/Assistente educacional', 'Concursado/efetivo/estável',
        ])
        ->assertSee(($this->detail)($alfa), false)
        ->assertSee(($this->detail)($beta), false)
        ->assertDontSee(($this->detail)($other), false)
        ->assertSee("educar_servidor_vinculo_turma_cad.php?{$this->keys}", false)
        ->assertSee("educar_servidor_det.php?cod_servidor={$this->id}&ref_cod_instituicao={$this->institution}", false);
});

test('filtra por cada campo', function (Closure $query) {
    $wanted = $this->ana->classLink($this->school->schoolClass(['nm_turma' => 'Turma Alfa']), ['funcao_exercida' => 2, 'tipo_vinculo' => 1]);

    $otherGrade = $this->school->grade('Segundo Ano');
    $other = $this->ana->classLink($this->school->schoolClass(['nm_turma' => 'Turma Beta', 'ref_ref_cod_serie' => $otherGrade, 'ano' => 2031]), ['ano' => 2031]);

    ($this->list)($query->call($this, $wanted))
        ->assertSee(($this->detail)($wanted), false)
        ->assertDontSee(($this->detail)($other), false);
})->with([
    'ano' => [fn () => ['ano' => 2030]],
    'série' => [fn () => ['ref_cod_serie' => $this->school->grade->getKey()]],
    'turma' => [fn ($wanted) => ['ref_cod_turma' => $wanted->turma_id]],
    'função exercida' => [fn () => ['funcao_exercida' => 2]],
    'tipo de vínculo' => [fn () => ['tipo_vinculo' => 1]],
    'escola e curso' => [fn () => ['ref_cod_escola' => $this->school->school->getKey(), 'ref_cod_curso' => $this->school->course->getKey(), 'ano' => 2030]],
]);

test('pagina de 20 em 20', function () {
    foreach (range(1, 21) as $number) {
        $this->ana->classLink($this->school->schoolClass(['nm_turma' => sprintf('Turma %02d', $number)]));
    }

    ($this->list)()->assertSee('Turma 20')->assertDontSee('Turma 21');
    ($this->list)(['pagina_formulario' => 2])->assertSee('Turma 21')->assertDontSee('Turma 20');
});

test('sem vínculos mostra a lista vazia', function () {
    ($this->list)()->assertDontSee('educar_servidor_vinculo_turma_det.php', false);
});

test('o usuário de biblioteca só vê os vínculos nas turmas das suas escolas', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $mine = $this->ana->classLink($this->school->schoolClass());
    $elsewhere = $this->ana->classLink(EnrollmentScenario::for($this->user, 2030)->schoolClass());

    $this->actingAs(LegacyUserFactory::new()->withSchool($this->school->school)->withAccess(635)->create([
        'ref_cod_instituicao' => $this->institution,
        'ref_cod_tipo_usuario' => LegacyUserTypeFactory::new()->create(['nivel' => App_Model_NivelTipoUsuario::BIBLIOTECA]),
    ]));

    ($this->list)()
        ->assertSee(($this->detail)($mine), false)
        ->assertDontSee(($this->detail)($elsewhere), false);
});

test('não oferece o botão Novo a quem só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->institution, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(635, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->institution])
    );

    ($this->list)()->assertDontSee('educar_servidor_vinculo_turma_cad.php', false);
});
