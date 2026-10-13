<?php

use App\Models\LegacyRegimeType;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = ['ref_cod_instituicao' => $this->user->ref_cod_instituicao];

    $this->course = LegacyCourseFactory::new()->standardAcademicYear()->create($this->institution + [
        'nm_curso' => 'Curso Alfa',
        'sgl_curso' => 'CALFA',
        'qtd_etapas' => 3,
        'hora_falta' => 0.75,
        'carga_horaria' => 1200.5,
        'ato_poder_publico' => 'Portaria 1 de 2020',
        'objetivo_curso' => 'Formar leitores',
        'publico_alvo' => 'Crianças de seis a dez anos',
    ]);

    $this->url = '/intranet/educar_curso_det.php?cod_curso=' . $this->course->getKey();
});

test('mostra todos os dados do curso ao administrador', function () {
    $this->get($this->url)
        ->assertOk()
        ->assertSeeInOrder(['Instituição', $this->course->institution->nm_instituicao], false)
        ->assertSeeInOrder(['Tipo Regime', LegacyRegimeType::query()->findOrFail($this->course->ref_cod_tipo_regime)->nm_tipo])
        ->assertSeeInOrder(['Nível Ensino', $this->course->educationLevel->nm_nivel])
        ->assertSeeInOrder(['Tipo Ensino', $this->course->educationType->nm_tipo])
        ->assertSeeInOrder(['Curso', 'Curso Alfa'])
        ->assertSeeInOrder(['Sigla Curso', 'CALFA'])
        ->assertSeeInOrder(['Quantidade Etapas', '3'])
        ->assertSeeInOrder(['Carga Horária', '1.200,50'])
        ->assertSeeInOrder(['Ato Poder', 'Portaria 1 de 2020'])
        ->assertSeeInOrder(['Padrão Ano Escolar', 'sim'])
        ->assertSeeInOrder(['Objetivo Curso', 'Formar leitores'])
        ->assertSee('Crianças de seis a dez anos')
        ->assertSee('educar_curso_cad.php?cod_curso=' . $this->course->getKey(), false);
});

test('omite as linhas dos campos vazios', function () {
    $course = LegacyCourseFactory::new()->create($this->institution + ['nm_curso' => 'Curso Beta', 'padrao_ano_escolar' => 0]);

    $this->get('/intranet/educar_curso_det.php?cod_curso=' . $course->getKey())
        ->assertOk()
        ->assertSee('Curso Beta')
        ->assertDontSee('Padrão Ano Escolar')
        ->assertDontSee('Objetivo Curso')
        ->assertDontSee('Ato Poder');
});

test('fora do nível de administrador não mostra a instituição nem os botões de quem só visualiza', function () {
    LegacyGeneralConfigurationFactory::new()->create($this->institution + ['active_on_ieducar' => true]);

    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(566, modify: false, remove: false)->create());

    $this->get($this->url)
        ->assertOk()
        ->assertSee('Curso Alfa')
        ->assertDontSee($this->course->institution->nm_instituicao)
        ->assertDontSee('educar_curso_cad.php?cod_curso=' . $this->course->getKey(), false);
});

test('volta para a listagem quando o curso não existe', function () {
    $this->get('/intranet/educar_curso_det.php?cod_curso=0')->assertRedirect('educar_curso_lst.php');
});
