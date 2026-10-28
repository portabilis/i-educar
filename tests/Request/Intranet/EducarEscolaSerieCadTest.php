<?php

use App\Models\LegacySchoolGrade;
use App\Models\LegacySchoolGradeDiscipline;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineDependenceFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacySchoolCourseFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

/**
 * No cadastro, a lista de componentes é montada pelo JavaScript depois que a
 * série é escolhida; os testes enviam o formato final (`disciplinas[id]`,
 * `carga_horaria[id]`, ...), que é o que o servidor lê.
 */
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = ['ref_cod_instituicao' => $this->user->ref_cod_instituicao];
    $this->course = LegacyCourseFactory::new()->create($this->institution);
    $this->grade = LegacyGradeFactory::new()->create(['ref_cod_curso' => $this->course, 'nm_serie' => 'Primeiro Ano']);
    $this->school = LegacySchoolFactory::new()->create($this->institution);

    // O curso da escola é quem diz os anos letivos que a série pode usar.
    LegacySchoolCourseFactory::new()->create([
        'ref_cod_escola' => $this->school,
        'ref_cod_curso' => $this->course,
        'anos_letivos' => '{2030,2031}',
    ]);

    // Um componente curricular da série, válido em 2030 e 2031.
    $this->component = function (string $name) {
        $discipline = LegacyDisciplineFactory::new()->create(['instituicao_id' => $this->user->ref_cod_instituicao, 'nome' => $name]);

        LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $this->grade,
            'carga_horaria' => 80,
            'anos_letivos' => '{2030,2031}',
        ]);

        return $discipline->getKey();
    };

    $this->arts = ($this->component)('Artes Visuais');
    $this->botany = ($this->component)('Botânica');

    $this->link = fn (array $attributes = []) => LegacySchoolGradeFactory::new()->create($attributes + [
        'ref_cod_escola' => $this->school,
        'ref_cod_serie' => $this->grade,
        'anos_letivos' => '{2030}',
    ]);

    $this->linked = fn (int $component, array $attributes = []) => LegacySchoolGradeDisciplineFactory::new()->create($attributes + [
        'ref_ref_cod_escola' => $this->school,
        'ref_ref_cod_serie' => $this->grade,
        'ref_cod_disciplina' => $component,
        'anos_letivos' => '{2030}',
    ]);

    // As linhas da tabela de componentes como o formulário as envia.
    $this->rows = function (array $components) {
        $payload = [];

        foreach ($components as $id => $row) {
            $payload['disciplinas'][$id] = $id;

            foreach ($row as $field => $value) {
                $payload[$field][$id] = $value;
            }
        }

        return $payload;
    };

    $this->saved = fn () => LegacySchoolGradeDiscipline::query()
        ->where('ref_ref_cod_escola', $this->school->getKey())
        ->where('ref_ref_cod_serie', $this->grade->getKey())
        ->get()
        ->keyBy('ref_cod_disciplina');

    $this->keys = "ref_cod_escola={$this->school->getKey()}&ref_cod_serie={$this->grade->getKey()}";
    $this->new = '/intranet/educar_escola_serie_cad.php';
    $this->edit = '/intranet/educar_escola_serie_cad.php?' . $this->keys;
});

test('abre o cadastro sem série escolhida e sem componentes', function () {
    $response = $this->get($this->new)->assertOk()->assertSee('Nenhum ano letivo selecionado');

    expect(LegacyFields::from($response->getContent())['tipoacao'])->toBe('Novo');
});

test('abre a edição com os dados do vínculo e os componentes da série', function () {
    ($this->link)([
        'hora_inicial' => '07:30:00',
        'hora_final' => '11:45:00',
        'hora_inicio_intervalo' => '09:30:00',
        'hora_fim_intervalo' => '09:50:00',
        'bloquear_enturmacao_sem_vagas' => 1,
    ]);

    ($this->linked)($this->arts, ['carga_horaria' => 120, 'hora_falta' => 0.75, 'aulas_por_semana' => 3]);
    ($this->linked)($this->botany);

    $html = $this->get($this->edit)->assertOk()->getContent();
    $fields = LegacyFields::from($html);
    $form = LegacyForm::from($html);

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['ref_cod_escola_'])->toBe((string) $this->school->getKey())
        ->and($fields['ref_cod_serie_'])->toBe((string) $this->grade->getKey())
        ->and($fields['ref_cod_curso_'])->toBe((string) $this->course->getKey())
        ->and($fields['hora_inicial'])->toBe('07:30')
        ->and($fields['hora_final'])->toBe('11:45')
        ->and($fields['hora_inicio_intervalo'])->toBe('09:30')
        ->and($fields['hora_fim_intervalo'])->toBe('09:50')
        ->and($fields['bloquear_enturmacao_sem_vagas'])->toBe('1')
        ->and($fields)->not->toHaveKey('bloquear_cadastro_turma_para_serie_com_vagas')
        // Os componentes vinculados vêm marcados; o que tem valores próprios
        // mostra a carga e a hora-falta em minutos, o outro usa os do componente.
        ->and($fields['disciplinas'])->toBe([$this->arts => (string) $this->arts, $this->botany => (string) $this->botany])
        ->and($fields['carga_horaria'][$this->arts])->toBe('120')
        ->and($fields['hora_falta'][$this->arts])->toBe('45')
        ->and($fields['aulas_semana'][$this->arts])->toBe('3')
        ->and($fields['usar_componente'])->toBe([$this->botany => '1'])
        ->and($fields['usar_componente_hora_falta'])->toBe([$this->botany => '1'])
        // Os anos letivos oferecidos são os do curso na escola.
        ->and(array_values(array_filter(array_keys($form->options('anos_letivos')))))->toBe([2030, 2031]);

    expect(json_decode(urldecode($fields['componentes_sombra']), true)[0])
        ->toMatchArray(['ref_cod_disciplina' => $this->arts, 'carga_horaria' => 120, 'anos_letivos' => [2030]]);
});

test('oferece os campos de etapas específicas quando a regra de avaliação do ano permite', function () {
    ($this->link)();
    ($this->linked)($this->arts, ['etapas_especificas' => 1, 'etapas_utilizadas' => '1,3']);

    LegacyEvaluationRuleGradeYearFactory::new()->create([
        'regra_avaliacao_id' => LegacyEvaluationRuleFactory::new()->create(['definir_componente_etapa' => 1]),
        'serie_id' => $this->grade,
        'ano_letivo' => 2030,
    ]);

    $fields = LegacyFields::from(
        $this->get($this->edit)->assertOk()->assertSee('Usado em etapas específicas?')->getContent()
    );

    expect($fields['etapas_especificas'])->toBe([$this->arts => '1'])
        ->and($fields['etapas_utilizadas'][$this->arts])->toBe('1,3');
});

test('volta para a listagem ao abrir a edição de um vínculo que não existe', function () {
    $this->get($this->edit)->assertRedirect('educar_escola_serie_lst.php');
});

test('volta para a listagem quando o usuário só pode visualizar', function () {
    LegacyGeneralConfigurationFactory::new()->create($this->institution + ['active_on_ieducar' => true]);

    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(585, modify: false, remove: false)->create($this->institution));

    $this->get($this->new)->assertRedirect('educar_escola_serie_lst.php');
});

test('cadastra a série na escola com horários, bloqueios, anos letivos e componentes', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'ref_cod_instituicao' => $this->user->ref_cod_instituicao,
        'ref_cod_escola' => $this->school->getKey(),
        'ref_cod_curso' => $this->course->getKey(),
        'ref_cod_serie' => $this->grade->getKey(),
        'anos_letivos' => [2030, 2031],
        'hora_inicial' => '07:30',
        'hora_final' => '11:45',
        'hora_inicio_intervalo' => '09:30',
        'hora_fim_intervalo' => '09:50',
        'bloquear_enturmacao_sem_vagas' => 'on',
    ] + ($this->rows)([
        $this->arts => ['carga_horaria' => 120, 'hora_falta' => 45, 'aulas_semana' => 3, 'componente_anos_letivos' => [2030, 2031]],
        $this->botany => ['carga_horaria' => '', 'hora_falta' => '', 'aulas_semana' => '', 'componente_anos_letivos' => [2030]],
    ]));

    $this->post($this->new, $payload)->assertRedirect('educar_escola_serie_lst.php');

    $link = LegacySchoolGrade::query()->where('ref_cod_escola', $this->school->getKey())->where('ref_cod_serie', $this->grade->getKey())->sole();

    expect($link->ativo)->toBe(1)
        ->and($link->anos_letivos)->toBe('{2030,2031}')
        ->and($link->hora_inicial)->toBe('07:30:00')
        ->and($link->hora_final)->toBe('11:45:00')
        ->and($link->hora_inicio_intervalo)->toBe('09:30:00')
        ->and($link->hora_fim_intervalo)->toBe('09:50:00')
        ->and($link->bloquear_enturmacao_sem_vagas)->toEqual(1)
        ->and($link->bloquear_cadastro_turma_para_serie_com_vagas)->toEqual(0)
        ->and($link->ref_usuario_cad)->toBe($this->user->getKey());

    $saved = ($this->saved)();

    expect($saved->keys()->sort()->values()->all())->toBe([$this->arts, $this->botany])
        ->and($saved[$this->arts]->carga_horaria)->toEqual(120)
        // A hora-falta vem em minutos e é gravada em horas.
        ->and($saved[$this->arts]->hora_falta)->toEqual(0.75)
        ->and($saved[$this->arts]->aulas_por_semana)->toEqual(3)
        ->and($saved[$this->arts]->anos_letivos)->toBe('{2030,2031}')
        ->and($saved[$this->botany]->carga_horaria)->toBeNull()
        ->and($saved[$this->botany]->hora_falta)->toBeNull()
        ->and($saved[$this->botany]->aulas_por_semana)->toBeNull()
        ->and($saved[$this->botany]->anos_letivos)->toBe('{2030}');
});

test('cadastra a série na escola sem nenhum componente', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'ref_cod_escola' => $this->school->getKey(),
        'ref_cod_serie' => $this->grade->getKey(),
    ]);

    $this->post($this->new, $payload)->assertRedirect('educar_escola_serie_lst.php');

    $link = LegacySchoolGrade::query()->where('ref_cod_escola', $this->school->getKey())->where('ref_cod_serie', $this->grade->getKey())->sole();

    expect($link->anos_letivos)->toBe('{}')
        ->and($link->hora_inicial)->toBeNull()
        ->and(($this->saved)())->toBeEmpty();
});

test('cadastrar uma série que a escola já teve reativa o vínculo e regrava os componentes', function () {
    $link = ($this->link)(['ativo' => 0, 'anos_letivos' => '{}']);
    ($this->linked)($this->arts, ['carga_horaria' => 60]);

    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'ref_cod_escola' => $this->school->getKey(),
        'ref_cod_serie' => $this->grade->getKey(),
        'anos_letivos' => [2031],
        'bloquear_cadastro_turma_para_serie_com_vagas' => 'on',
    ] + ($this->rows)([
        $this->arts => ['carga_horaria' => 90, 'componente_anos_letivos' => [2031]],
    ]));

    $this->post($this->new, $payload)->assertRedirect('educar_escola_serie_lst.php');

    expect($link->refresh())
        ->ativo->toBe(1)
        ->anos_letivos->toBe('{2031}')
        ->ref_usuario_exc->toBe($this->user->getKey())
        ->and($link->bloquear_cadastro_turma_para_serie_com_vagas)->toEqual(1);

    expect(($this->saved)()[$this->arts])
        ->carga_horaria->toEqual(90)
        ->anos_letivos->toBe('{2031}');
});

test('recusa o cadastro sem escola ou sem série', function (array $overrides) {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), $overrides + [
        'ref_cod_escola' => $this->school->getKey(),
        'ref_cod_serie' => $this->grade->getKey(),
    ]);

    $this->post($this->new, $payload)->assertOk()->assertSee('Cadastro não realizado.');

    expect(LegacySchoolGrade::query()->where('ref_cod_escola', $this->school->getKey())->exists())->toBeFalse();
})->with([
    'sem escola' => [['ref_cod_escola' => '']],
    'sem série' => [['ref_cod_serie' => '']],
]);

// Defeito conhecido: um componente que não existe faz o cadastro ser recusado
// depois de o vínculo já ter sido gravado; a tela diz que não cadastrou, mas a
// série fica vinculada à escola.
test('defeito: o cadastro recusado por componente inexistente deixa o vínculo gravado', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'ref_cod_escola' => $this->school->getKey(),
        'ref_cod_serie' => $this->grade->getKey(),
    ] + ($this->rows)([999999999 => ['carga_horaria' => 10]]));

    $this->post($this->new, $payload)->assertOk()->assertSee('Cadastro não realizado.');

    expect(LegacySchoolGrade::query()->where('ref_cod_escola', $this->school->getKey())->where('ref_cod_serie', $this->grade->getKey())->exists())->toBeTrue()
        ->and(($this->saved)())->toBeEmpty();
});

test('edita horários, bloqueios, anos letivos e componentes do vínculo', function () {
    $link = ($this->link)(['hora_inicial' => '07:30:00', 'bloquear_enturmacao_sem_vagas' => 1, 'anos_letivos' => '{2030,2031}']);

    ($this->linked)($this->arts, ['carga_horaria' => 120, 'hora_falta' => 0.75, 'aulas_por_semana' => 3]);

    $chemistry = ($this->component)('Química');
    ($this->linked)($chemistry);

    $payload = LegacyFields::from($this->get($this->edit)->assertOk()->getContent(), [
        'hora_inicial' => '13:00',
        'hora_final' => '17:15',
        'anos_letivos' => [2030, 2031],
        'bloquear_cadastro_turma_para_serie_com_vagas' => 'on',
    ]);

    unset($payload['bloquear_enturmacao_sem_vagas']);

    // Artes passa a usar os padrões do componente; Botânica entra com valores
    // próprios; Química sai.
    $payload = array_merge($payload, [
        'disciplinas' => [$this->arts => $this->arts, $this->botany => $this->botany],
        'usar_componente' => [$this->arts => 1],
        'usar_componente_hora_falta' => [$this->arts => 1],
        'carga_horaria' => [$this->arts => 120, $this->botany => 100],
        'hora_falta' => [$this->arts => 45, $this->botany => 30],
        'aulas_semana' => [$this->arts => '', $this->botany => 2],
        'componente_anos_letivos' => [$this->arts => [2030, 2031], $this->botany => [2031]],
    ]);

    $this->post($this->edit, $payload)->assertRedirect('educar_escola_serie_lst.php');

    expect($link->refresh())
        ->hora_inicial->toBe('13:00:00')
        ->hora_final->toBe('17:15:00')
        ->anos_letivos->toBe('{2030,2031}')
        ->ref_usuario_exc->toBe($this->user->getKey())
        ->and($link->bloquear_enturmacao_sem_vagas)->toEqual(0)
        ->and($link->bloquear_cadastro_turma_para_serie_com_vagas)->toEqual(1);

    $saved = ($this->saved)();

    expect($saved->keys()->sort()->values()->all())->toBe([$this->arts, $this->botany])
        ->and($saved[$this->arts]->carga_horaria)->toBeNull()
        ->and($saved[$this->arts]->hora_falta)->toBeNull()
        ->and($saved[$this->arts]->aulas_por_semana)->toBeNull()
        ->and($saved[$this->arts]->anos_letivos)->toBe('{2030,2031}')
        ->and($saved[$this->botany]->carga_horaria)->toEqual(100)
        ->and($saved[$this->botany]->hora_falta)->toEqual(0.5)
        ->and($saved[$this->botany]->aulas_por_semana)->toEqual(2)
        ->and($saved[$this->botany]->anos_letivos)->toBe('{2031}');
});

test('recusa a edição sem os códigos da escola e da série', function () {
    ($this->link)();
    ($this->linked)($this->arts);

    $payload = LegacyFields::from($this->get($this->edit)->assertOk()->getContent(), ['ref_cod_escola_' => '']);

    $this->post($this->edit, $payload)->assertOk()->assertSee('Edição não realizada.');
});

test('exclui o vínculo, desativando a série na escola e apagando os componentes', function () {
    $link = ($this->link)();
    ($this->linked)($this->arts);

    $payload = LegacyFields::from($this->get($this->edit)->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post($this->edit, $payload)->assertRedirect('educar_escola_serie_lst.php');

    expect($link->refresh())
        ->ativo->toBe(0)
        ->anos_letivos->toBe('{}')
        ->ref_usuario_exc->toBe($this->user->getKey())
        ->and(($this->saved)())->toBeEmpty();
});

test('recusa a exclusão quando um componente tem dependência registrada', function () {
    $link = ($this->link)();
    ($this->linked)($this->arts);

    LegacyDisciplineDependenceFactory::new()->create([
        'ref_cod_escola' => $this->school,
        'ref_cod_serie' => $this->grade,
        'ref_cod_disciplina' => $this->arts,
    ]);

    $payload = LegacyFields::from($this->get($this->edit)->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post($this->edit, $payload)
        ->assertRedirect('educar_escola_serie_cad.php?' . $this->keys)
        ->assertSessionHas('legacy', 'Não foi possível remover o componente. Existe registros de dependência neste componente.<br>');

    expect($link->refresh()->ativo)->toBe(1)
        ->and(($this->saved)())->toHaveCount(1);
});

test('recusa a exclusão sem os códigos da escola e da série', function () {
    $link = ($this->link)();
    ($this->linked)($this->arts);

    $payload = LegacyFields::from($this->get($this->edit)->assertOk()->getContent(), ['tipoacao' => 'Excluir', 'ref_cod_serie_' => '']);

    $this->post($this->edit, $payload)->assertOk()->assertSee('Exclusão não realizada.');

    expect($link->refresh()->ativo)->toBe(1);
});
