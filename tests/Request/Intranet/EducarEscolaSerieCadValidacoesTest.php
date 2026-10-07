<?php

use App\Models\LegacySchoolGrade;
use App\Models\LegacySchoolGradeDiscipline;
use App\Services\iDiarioService;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyDisciplineScoreFactory;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolCourseFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyStudentScoreFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

/**
 * As recusas da edição da série da escola: antes de gravar, a tela compara os
 * componentes enviados com os que estavam vinculados (o campo oculto
 * `componentes_sombra`) e recusa o que não pode ser acrescentado ou retirado.
 * A recusa volta para o próprio formulário com as mensagens na sessão.
 */
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = ['ref_cod_instituicao' => $this->user->ref_cod_instituicao];
    $this->course = LegacyCourseFactory::new()->create($this->institution);
    $this->grade = LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->course]);
    $this->school = LegacySchoolFactory::new()->create($this->institution);

    LegacySchoolCourseFactory::new()->create([
        'ref_cod_escola' => $this->school,
        'ref_cod_curso' => $this->course,
        'anos_letivos' => '{2030,2031}',
    ]);

    $this->link = LegacySchoolGradeFactory::new()->create([
        'ref_cod_escola' => $this->school,
        'ref_cod_serie' => $this->grade,
        'anos_letivos' => '{2030,2031}',
    ]);

    // Um componente da série, válido nos anos informados, e vinculado ou não à escola.
    $this->component = function (string $name, string $gradeYears, ?string $schoolYears = null) {
        $discipline = LegacyDisciplineFactory::new()->create(['instituicao_id' => $this->user->ref_cod_instituicao, 'nome' => $name]);

        LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $this->grade,
            'anos_letivos' => $gradeYears,
        ]);

        if ($schoolYears) {
            LegacySchoolGradeDisciplineFactory::new()->create([
                'ref_ref_cod_escola' => $this->school,
                'ref_ref_cod_serie' => $this->grade,
                'ref_cod_disciplina' => $discipline,
                'anos_letivos' => $schoolYears,
            ]);
        }

        return $discipline->getKey();
    };

    // Artes está vinculada à escola nos dois anos; Química ainda não está.
    $this->arts = ($this->component)('Artes Visuais', '{2030,2031}', '{2030,2031}');
    $this->chemistry = ($this->component)('Química', '{2030}');

    // Uma turma ativa da série na escola.
    $this->schoolClass = fn (int $year = 2030) => LegacySchoolClassFactory::new()->create([
        'ref_ref_cod_escola' => $this->school,
        'ref_ref_cod_serie' => $this->grade,
        'ref_cod_curso' => $this->course,
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'ano' => $year,
    ]);

    // Uma nota lançada para o componente a um aluno enturmado na turma.
    $this->score = function ($schoolClass, int $component) {
        $registration = LegacyRegistrationFactory::new()->create([
            'ref_ref_cod_escola' => $this->school,
            'ref_ref_cod_serie' => $this->grade,
            'ref_cod_curso' => $this->course,
            'ano' => $schoolClass->ano,
        ]);

        LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'ref_cod_turma' => $schoolClass]);

        LegacyDisciplineScoreFactory::new()->create([
            'nota_aluno_id' => LegacyStudentScoreFactory::new()->create(['matricula_id' => $registration]),
            'componente_curricular_id' => $component,
        ]);
    };

    // Liga a integração com o iDiário e faz o serviço responder que há lançamentos.
    $this->idiario = function (bool $hasActivity) {
        LegacyGeneralConfigurationFactory::new()->create($this->institution + [
            'active_on_ieducar' => true,
            'url_novo_educacao' => 'https://idiario.example',
            'token_novo_educacao' => 'token',
        ]);

        $this->mock(iDiarioService::class)->shouldReceive('getClassroomsActivityByDiscipline')->andReturn($hasActivity);
    };

    $this->url = "/intranet/educar_escola_serie_cad.php?ref_cod_escola={$this->school->getKey()}&ref_cod_serie={$this->grade->getKey()}";

    // Envia a edição com os componentes e os anos letivos de cada um.
    $this->submit = function (array $components, array $overrides = []) {
        $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), $overrides);

        $payload['disciplinas'] = array_combine(array_keys($components), array_keys($components));
        $payload['componente_anos_letivos'] = $components;

        return $this->post($this->url, $payload);
    };

    $this->refused = function ($response, string ...$messages) {
        $response->assertRedirect($this->url)->assertSessionHas('legacy', $messages);
    };

    $this->years = fn () => LegacySchoolGradeDiscipline::query()
        ->where('ref_ref_cod_escola', $this->school->getKey())
        ->where('ref_ref_cod_serie', $this->grade->getKey())
        ->pluck('anos_letivos', 'ref_cod_disciplina')
        ->all();

    $this->unchanged = [$this->arts => '{2030,2031}'];

    // O vínculo some entre a abertura do formulário e o envio.
    $this->vanish = function () {
        LegacySchoolGradeDiscipline::query()->where('ref_ref_cod_escola', $this->school->getKey())->delete();
        LegacySchoolGrade::query()->where('ref_cod_escola', $this->school->getKey())->delete();
    };
});

test('aceita acrescentar um componente em um ano em que ele está cadastrado na série', function () {
    ($this->submit)([$this->arts => [2030, 2031], $this->chemistry => [2030]])
        ->assertRedirect('educar_escola_serie_lst.php');

    expect(($this->years)())->toBe([$this->arts => '{2030,2031}', $this->chemistry => '{2030}']);
});

test('recusa acrescentar um componente em um ano em que ele não está cadastrado na série', function () {
    ($this->refused)(
        ($this->submit)([$this->arts => [2030, 2031], $this->chemistry => [2030, 2031]]),
        'O ano 2031 de "Química" precisa estar devidamente cadastrado nesta série em Componentes da série (Escola > Cadastros > Tipos > Séries > Componentes da série).'
    );

    expect(($this->years)())->toBe($this->unchanged);
});

test('recusa acrescentar a um componente já vinculado um ano em que ele não está cadastrado na série', function () {
    $physics = ($this->component)('Física', '{2030}', '{2030}');

    ($this->refused)(
        ($this->submit)([$this->arts => [2030, 2031], $physics => [2030, 2031]]),
        'O ano 2031 de "Física" precisa estar devidamente cadastrado nesta série em Componentes da série (Escola > Cadastros > Tipos > Séries > Componentes da série).'
    );

    expect(($this->years)())->toBe([$this->arts => '{2030,2031}', $physics => '{2030}']);
});

test('aceita retirar um componente e um ano letivo sem lançamentos', function () {
    $physics = ($this->component)('Física', '{2030}', '{2030}');

    ($this->schoolClass)();

    ($this->submit)([$this->arts => [2030]])->assertRedirect('educar_escola_serie_lst.php');

    expect(($this->years)())->toBe([$this->arts => '{2030}'])
        ->and($physics)->not->toBeIn(array_keys(($this->years)()));
});

test('recusa retirar um componente usado por uma turma ativa', function () {
    $physics = ($this->component)('Física', '{2030}', '{2030}');

    LegacyDisciplineSchoolClassFactory::new()->create([
        'componente_curricular_id' => $physics,
        'ano_escolar_id' => $this->grade,
        'escola_id' => $this->school,
        'turma_id' => ($this->schoolClass)(),
    ]);

    ($this->refused)(
        ($this->submit)([$this->arts => [2030, 2031]]),
        'Não é possível desvincular "Física" pois existem turmas vinculadas a este componente.'
    );

    expect(($this->years)())->toHaveKey($physics);
});

test('recusa retirar um componente com nota lançada na série e escola', function () {
    $physics = ($this->component)('Física', '{2030}', '{2030}');

    ($this->score)(($this->schoolClass)(), $physics);

    ($this->refused)(
        ($this->submit)([$this->arts => [2030, 2031]]),
        'Não é possível desvincular "Física" pois já existem notas, faltas e/ou pareceres lançados para este componente nesta série e escola.'
    );

    expect(($this->years)())->toHaveKey($physics);
});

test('recusa retirar um componente com lançamentos no iDiário', function () {
    $physics = ($this->component)('Física', '{2030}', '{2030}');

    ($this->schoolClass)();
    ($this->idiario)(true);

    ($this->refused)(
        ($this->submit)([$this->arts => [2030, 2031]]),
        'Não é possível desvincular "Física" pois já existem notas, faltas e/ou pareceres lançados para este componente nesta série e escola no iDiário'
    );

    expect(($this->years)())->toHaveKey($physics);
});

test('recusa retirar de um componente o ano letivo em que ele tem nota lançada', function () {
    ($this->score)(($this->schoolClass)(2031), $this->arts);

    ($this->refused)(
        ($this->submit)([$this->arts => [2030]]),
        'Não é possível desvincular o ano 2031 de "Artes Visuais" pois já existem notas, faltas e/ou pareceres lançados para este componente nesta série, ano e escola.'
    );

    expect(($this->years)())->toBe($this->unchanged);
});

test('recusa retirar de um componente o ano letivo em que ele tem lançamentos no iDiário', function () {
    ($this->schoolClass)(2031);
    ($this->idiario)(true);

    ($this->refused)(
        ($this->submit)([$this->arts => [2030]]),
        'Não é possível desvincular o ano 2031 de "Artes Visuais" pois já existem notas, faltas e/ou pareceres lançados para este componente nesta série, ano e escola no iDiário'
    );

    expect(($this->years)())->toBe($this->unchanged);
});

test('com o iDiário ligado e sem lançamentos, a retirada é aceita', function () {
    ($this->schoolClass)(2031);
    ($this->idiario)(false);

    ($this->submit)([$this->arts => [2030]])->assertRedirect('educar_escola_serie_lst.php');

    expect(($this->years)())->toBe([$this->arts => '{2030}']);
});

test('junta todas as recusas em uma só resposta', function () {
    $physics = ($this->component)('Física', '{2030}', '{2030}');

    ($this->score)(($this->schoolClass)(), $physics);

    ($this->refused)(
        ($this->submit)([$this->arts => [2030, 2031], $this->chemistry => [2031]]),
        'O ano 2031 de "Química" precisa estar devidamente cadastrado nesta série em Componentes da série (Escola > Cadastros > Tipos > Séries > Componentes da série).',
        'Não é possível desvincular "Física" pois já existem notas, faltas e/ou pareceres lançados para este componente nesta série e escola.'
    );
});

// Defeito conhecido: na edição, um componente que não existe é recusado só
// depois de o vínculo e os outros componentes já terem sido regravados.
test('defeito: a edição recusada por componente inexistente já gravou o que veio antes', function () {
    ($this->submit)([$this->arts => [2030], 999999999 => []])
        ->assertOk()
        ->assertSee('Edição não realizada.');

    expect(($this->years)())->toBe([$this->arts => '{2030}']);
});

test('recusa a edição de um vínculo que não existe mais', function () {
    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent());

    ($this->vanish)();

    $this->post($this->url, $payload)->assertOk()->assertSee('Edição não realizada.');
});

test('recusa a exclusão de um vínculo que não existe mais', function () {
    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    ($this->vanish)();

    $this->post($this->url, $payload)->assertOk()->assertSee('Exclusão não realizada.');
});

// Depois de uma recusa, o cadastro é reenviado com a escola e a série nos
// campos ocultos, que têm precedência sobre as listas (desabilitadas na tela).
test('no reenvio do cadastro valem a escola e a série dos campos ocultos', function () {
    $grade = LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->course]);

    $payload = LegacyFields::from($this->get('/intranet/educar_escola_serie_cad.php')->assertOk()->getContent(), [
        'ref_cod_instituicao_' => $this->user->ref_cod_instituicao,
        'ref_cod_escola_' => $this->school->getKey(),
        'ref_cod_curso_' => $this->course->getKey(),
        'ref_cod_serie_' => $grade->getKey(),
        'ref_cod_escola' => '',
        'ref_cod_serie' => '',
    ]);

    $this->post('/intranet/educar_escola_serie_cad.php', $payload)->assertRedirect('educar_escola_serie_lst.php');

    expect(LegacySchoolGrade::query()->where('ref_cod_escola', $this->school->getKey())->where('ref_cod_serie', $grade->getKey())->exists())->toBeTrue();
});

// Sem nenhum componente marcado, a edição não mexe nos componentes vinculados.
test('sem nenhum componente marcado a edição mantém os componentes como estão', function () {
    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), ['tipoacao' => 'Editar']);

    unset($payload['disciplinas']);

    $this->post($this->url, $payload)->assertRedirect('educar_escola_serie_lst.php');

    expect(($this->years)())->toBe($this->unchanged);
});

// Defeito conhecido: sem nenhum componente marcado a exclusão repassa nulo à
// verificação de dependência, que exige lista, e estoura.
test('defeito: sem nenhum componente marcado a exclusão estoura', function () {
    $payload = LegacyFields::from($this->get($this->url)->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    unset($payload['disciplinas']);

    $this->post($this->url, $payload)->assertServerError();
});
