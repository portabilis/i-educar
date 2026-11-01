<?php

use App\Models\LegacyGrade;
use Tests\Browser\Support\Form;
use Tests\Browser\Support\Scenario;

/**
 * Etapa 6 do roteiro: cadastrar a série (ano escolar) do curso.
 *
 * A série é o primeiro cadastro que exige a regra de avaliação, e é ela que
 * amarra curso, carga horária, dias letivos e ano letivo. O select de curso é
 * preenchido por AJAX depois da instituição, como no cadastro de curso.
 */
beforeEach(function () {
    $this->scenario = Scenario::install(adminLogin: 'adm-serie');
    $this->course = Scenario::course();
});

it('lista as séries', function () {
    Scenario::grade($this->course);

    $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_serie_lst.php')
        ->assertSee('Série')
        ->assertSee(Scenario::GRADE);
});

it('cadastra uma série', function () {
    $page = $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_serie_cad.php');

    $page = Form::cascade($page, [
        'ref_cod_instituicao' => $this->scenario->institution->getKey(),
        'ref_cod_curso' => $this->course->getKey(),
    ]);

    $page = Form::fill($page, [
        'nm_serie' => Scenario::GRADE,
        'carga_horaria' => '800',
        'dias_letivos' => '200',
        // Nomes com colchetes precisam vir como seletor de atributo: o plugin do
        // Pest tentaria interpretá-los como seletor CSS.
        '[name="anos_letivos[0]"]' => (string) now()->year,
    ]);

    $page = $page->select('etapa_curso', '1')
        ->select('concluinte', 'não')
        ->select('[name="regras_avaliacao_id[0]"]', Scenario::EVALUATION_RULE);

    Form::save($page)->assertPathIs('/intranet/educar_serie_lst.php');

    $grade = LegacyGrade::query()->where('nm_serie', Scenario::GRADE)->sole();

    expect($grade->ref_cod_curso)->toBe($this->course->getKey())
        ->and((int) $grade->dias_letivos)->toBe(200);
});

it('edita o nome da série', function () {
    $grade = Scenario::grade($this->course);

    $page = $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_serie_cad.php?cod_serie=' . $grade->getKey())
        ->fill('nm_serie', '1º Ano do Ensino Fundamental');

    Form::save($page)->assertPathIs('/intranet/educar_serie_lst.php');

    expect($grade->fresh()->nm_serie)->toBe('1º Ano do Ensino Fundamental');
});

it('recusa gravar sem o nome da série', function () {
    $page = $this->scenario->loginAsAdmin()
        ->navigate('/intranet/educar_serie_cad.php');

    $page = Form::cascade($page, [
        'ref_cod_instituicao' => $this->scenario->institution->getKey(),
        'ref_cod_curso' => $this->course->getKey(),
    ]);

    Form::save($page)->assertPathIs('/intranet/educar_serie_cad.php');

    expect(LegacyGrade::query()->count())->toBe(0);
});
