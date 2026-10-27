<?php

use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = ['ref_cod_instituicao' => $this->user->ref_cod_instituicao];

    // Uma série vinculada a uma escola, as duas na instituição do usuário.
    $this->link = function (array $grade = [], array $course = []) {
        $course = LegacyCourseFactory::new()->create($course + $this->institution);
        $grade = LegacyGradeFactory::new()->create($grade + ['ref_cod_curso' => $course]);
        $school = LegacySchoolFactory::new()->withName('Escola ' . $grade->getKey())->create($this->institution);

        return LegacySchoolGradeFactory::new()->create(['ref_cod_escola' => $school, 'ref_cod_serie' => $grade]);
    };

    $this->detail = fn ($link) => "educar_escola_serie_det.php?ref_cod_escola={$link->ref_cod_escola}&ref_cod_serie={$link->ref_cod_serie}";

    $this->list = fn (array $query = []) => $this->get('/intranet/educar_escola_serie_lst.php?' . http_build_query($query + $this->institution))->assertOk();
});

test('lista as séries das escolas com série, curso, escola e instituição', function () {
    $link = ($this->link)(
        ['nm_serie' => 'Primeiro Ano', 'descricao' => 'Ciclo de alfabetização'],
        ['nm_curso' => 'Fundamental', 'descricao' => 'Nove anos']
    );

    ($this->list)()
        ->assertSee(($this->detail)($link), false)
        ->assertSee('Primeiro Ano (Ciclo de alfabetização)')
        ->assertSee('Fundamental (Nove anos)')
        ->assertSee('Escola ' . $link->ref_cod_serie)
        ->assertSee($this->user->institution->nm_instituicao)
        ->assertSee('go("educar_escola_serie_cad.php")', false);
});

test('mostra série e curso sem parênteses quando não têm descrição', function () {
    ($this->link)(['nm_serie' => 'Segundo Ano', 'descricao' => null], ['nm_curso' => 'Fundamental', 'descricao' => null]);

    ($this->list)()
        ->assertSee('>Segundo Ano</a>', false)
        ->assertSee('>Fundamental</a>', false);
});

test('não lista o vínculo inativo', function () {
    $link = ($this->link)();
    $link->update(['ativo' => 0]);

    ($this->list)()->assertDontSee(($this->detail)($link), false);
});

test('filtra pela escola, pelo curso e pela série', function (Closure $query) {
    $wanted = ($this->link)();
    $other = ($this->link)();

    ($this->list)($query->call($this, $wanted))
        ->assertSee(($this->detail)($wanted), false)
        ->assertDontSee(($this->detail)($other), false);
})->with([
    'escola' => [fn ($link) => ['ref_cod_escola' => $link->ref_cod_escola]],
    'curso' => [fn ($link) => ['ref_cod_curso' => $link->grade->ref_cod_curso]],
    'série' => [fn ($link) => ['ref_cod_curso' => $link->grade->ref_cod_curso, 'ref_cod_serie' => $link->ref_cod_serie]],
    // Os campos ocultos da tela de edição têm precedência sobre os filtros.
    'escola e série vindas dos campos ocultos' => [fn ($link) => ['ref_cod_escola_' => $link->ref_cod_escola, 'ref_cod_serie_' => $link->ref_cod_serie]],
]);

test('com o curso escolhido, oferece as séries ativas dele no filtro', function () {
    $link = ($this->link)(['nm_serie' => 'Terceiro Ano']);
    LegacyGradeFactory::new()->create(['ref_cod_curso' => $link->grade->ref_cod_curso, 'nm_serie' => 'Série Extinta', 'ativo' => 0]);

    $html = ($this->list)(['ref_cod_curso' => $link->grade->ref_cod_curso])->getContent();

    expect($html)->toContain("value=\"{$link->ref_cod_serie}\">Terceiro Ano")
        ->and($html)->not->toContain('Série Extinta');
});

test('usuário de nível escola só vê as séries das escolas a que está vinculado', function () {
    LegacyGeneralConfigurationFactory::new()->create($this->institution + ['active_on_ieducar' => true]);

    $mine = ($this->link)();
    $other = ($this->link)();

    $this->actingAs(
        LegacyUserFactory::new()->school()->withSchool($mine->school)->withAccess(585, modify: false, remove: false)->create($this->institution)
    );

    $this->get('/intranet/educar_escola_serie_lst.php')
        ->assertOk()
        ->assertSee(($this->detail)($mine), false)
        ->assertDontSee(($this->detail)($other), false)
        // Sem permissão de cadastro não há botão Novo.
        ->assertDontSee('go("educar_escola_serie_cad.php")', false);
});
