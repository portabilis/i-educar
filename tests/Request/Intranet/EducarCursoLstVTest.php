<?php

use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyRegimeTypeFactory;
use Database\Factories\LegacyUserFactory;

/**
 * Esta listagem não é referenciada por nenhum menu, rota ou tela; só é
 * alcançada pelo endereço. Os testes registram o que ela faz hoje.
 */
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = ['ref_cod_instituicao' => $this->user->ref_cod_instituicao];
    $this->course = LegacyCourseFactory::new()->create($this->institution + ['nm_curso' => 'Curso Alfa']);
});

test('lista os cursos com nível, tipo de ensino e instituição', function () {
    $this->get('/intranet/educar_curso_lst_v.php')
        ->assertOk()
        ->assertSee('Curso Alfa')
        ->assertSee($this->course->educationLevel->nm_nivel)
        ->assertSee($this->course->educationType->nm_tipo)
        ->assertSee($this->course->institution->nm_instituicao);
});

// Defeito conhecido: o campo "Curso" aparece na tela, mas o valor dele não é
// repassado à consulta.
test('defeito: o filtro por nome não filtra', function () {
    $this->get('/intranet/educar_curso_lst_v.php?nm_curso=nome-que-nao-existe')
        ->assertOk()
        ->assertSee('Curso Alfa');
});

// Defeito conhecido: o nível de ensino escolhido é repassado à consulta como
// tipo de regime (e o tipo de ensino, como nível).
test('defeito: o filtro de nível de ensino filtra pelo tipo de regime', function () {
    $regime = LegacyRegimeTypeFactory::new()->create($this->institution);

    LegacyCourseFactory::new()->create($this->institution + ['nm_curso' => 'Curso do Regime', 'ref_cod_tipo_regime' => $regime]);

    // O código passado como nível é o de um tipo de regime, e quem vem é o
    // curso desse regime.
    $this->get('/intranet/educar_curso_lst_v.php?ref_cod_nivel_ensino=' . $regime->getKey())
        ->assertOk()
        ->assertSee('Curso do Regime')
        ->assertDontSee('Curso Alfa');
});
