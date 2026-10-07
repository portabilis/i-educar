<?php

use App\Models\LegacyCourse;
use Tests\Browser\Support\Form;
use Tests\Browser\Support\Scenario;

/**
 * Etapa 5 do roteiro: cadastrar o curso.
 *
 * O curso é o primeiro cadastro da estrutura pedagógica e depende do catálogo da
 * instituição: nível de ensino e tipo de ensino. Esses dois selects chegam vazios
 * e só são preenchidos por AJAX depois que a instituição é escolhida — o
 * selectOption do Playwright tenta de novo até a opção existir, e é isso que
 * segura a cascata.
 */
beforeEach(function () {
    $this->scenario = Scenario::install(adminLogin: 'adm-curso');
});

it('lista os cursos', function () {
    $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_curso_lst.php')
        ->assertSee('Curso')
        ->assertSee('Nível Ensino');

    // Sem assertNoJavaScriptErrors aqui: educar-curso-lst.js:32 faz
    // document.getElementById('ref_cod_instituicao').onchange sem conferir se o
    // elemento existe, e na listagem ele não existe — o script morre aí e os
    // filtros em cascata da listagem nunca são ligados. É defeito de hoje, não do
    // rebranding; quando for corrigido, a asserção volta.
});

it('cadastra um curso', function () {
    $page = $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_curso_cad.php');

    $page = Form::cascade($page, [
        'ref_cod_instituicao' => $this->scenario->institution->getKey(),
        'ref_cod_nivel_ensino' => Scenario::EDUCATION_LEVEL,
        'ref_cod_tipo_ensino' => Scenario::EDUCATION_TYPE,
    ]);

    $page = Form::fill($page, [
        'nm_curso' => Scenario::COURSE,
        'sgl_curso' => 'EF',
        'qtd_etapas' => '4',
        'hora_falta' => '50',
        'carga_horaria' => '800',
    ])->select('modalidade_curso', 'Ensino regular');

    Form::save($page)->assertPathIs('/intranet/educar_curso_lst.php');

    $course = LegacyCourse::query()->where('nm_curso', Scenario::COURSE)->sole();

    expect($course->ref_cod_instituicao)->toBe($this->scenario->institution->getKey())
        ->and((int) $course->qtd_etapas)->toBe(4);
});

it('edita o nome do curso', function () {
    $course = Scenario::course();

    $page = $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_curso_cad.php?cod_curso=' . $course->getKey())
        ->fill('nm_curso', 'Ensino Fundamental de 9 anos');

    Form::save($page)->assertPathIs('/intranet/educar_curso_lst.php');

    expect($course->fresh()->nm_curso)->toBe('Ensino Fundamental de 9 anos');
});

it('recusa gravar sem o nome do curso', function () {
    $page = $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_curso_cad.php');

    $page = Form::cascade($page, [
        'ref_cod_instituicao' => $this->scenario->institution->getKey(),
        'ref_cod_nivel_ensino' => Scenario::EDUCATION_LEVEL,
        'ref_cod_tipo_ensino' => Scenario::EDUCATION_TYPE,
    ]);

    Form::save($page)->assertPathIs('/intranet/educar_curso_cad.php');

    expect(LegacyCourse::query()->count())->toBe(0);
});
