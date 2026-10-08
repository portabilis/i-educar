<?php

use App\Models\LegacyCourse;
use App\Models\LegacyCourseEducacensoStage;
use App\Models\LegacySchoolClassStage;
use Database\Factories\LegacyCourseEducacensoStageFactory;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyEducacensoStagesFactory;
use Database\Factories\LegacyEducationLevelFactory;
use Database\Factories\LegacyEducationTypeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyRegimeTypeFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->institution = ['ref_cod_instituicao' => $this->user->ref_cod_instituicao];

    // Os campos obrigatórios de um curso, do jeito que o formulário os envia.
    $this->required = fn () => $this->institution + [
        'ref_cod_nivel_ensino' => LegacyEducationLevelFactory::new()->current()->getKey(),
        'ref_cod_tipo_ensino' => LegacyEducationTypeFactory::new()->current()->getKey(),
        'ref_cod_tipo_regime' => LegacyRegimeTypeFactory::new()->current()->getKey(),
        'nm_curso' => 'Curso Alfa',
        'sgl_curso' => 'CA',
        'qtd_etapas' => '3',
        'carga_horaria' => '800',
        'hora_falta' => '45',
        'modalidade_curso' => 1,
    ];

    $this->novo = fn (array $overrides = []) => LegacyFields::from(
        $this->get('/intranet/educar_curso_cad.php')->assertOk()->getContent(),
        $overrides + ($this->required)(),
    );

    $this->course = LegacyCourseFactory::new()->create($this->institution + [
        'nm_curso' => 'Curso Existente',
        'hora_falta' => 0.75,
        'padrao_ano_escolar' => 0,
    ]);
    $this->url = '/intranet/educar_curso_cad.php?cod_curso=' . $this->course->getKey();

    $this->edicao = fn (array $overrides = []) => LegacyFields::from($this->get($this->url)->assertOk()->getContent(), $overrides);

    $this->stages = fn (int $course) => LegacyCourseEducacensoStage::query()
        ->where('curso_id', $course)
        ->orderBy('etapa_id')
        ->pluck('etapa_id')
        ->all();
});

test('abre o formulário novo sem instituição escolhida e sem as listas que dependem dela', function () {
    $response = $this->get('/intranet/educar_curso_cad.php')->assertOk();
    $form = LegacyForm::from($response->getContent());

    expect(LegacyFields::from($response->getContent())['tipoacao'])->toBe('Novo')
        ->and($form->options('ref_cod_instituicao'))->toHaveKey((string) $this->user->ref_cod_instituicao)
        ->and(array_filter(array_keys($form->options('ref_cod_nivel_ensino'))))->toBe([]);
});

test('abre a edição com os dados do curso e as listas da instituição dele', function () {
    $response = $this->get($this->url)->assertOk();
    $fields = LegacyFields::from($response->getContent());
    $form = LegacyForm::from($response->getContent());

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['nm_curso'])->toBe('Curso Existente')
        // A hora-falta é guardada em horas e mostrada em minutos.
        ->and($fields['hora_falta'])->toBe('45')
        ->and($form->options('ref_cod_nivel_ensino'))->toHaveKey((string) $this->course->ref_cod_nivel_ensino)
        ->and($form->options('ref_cod_tipo_ensino'))->toHaveKey((string) $this->course->ref_cod_tipo_ensino)
        ->and($form->options('ref_cod_tipo_regime'))->toHaveKey((string) $this->course->ref_cod_tipo_regime);
});

test('cadastra um curso com todos os campos', function () {
    $first = LegacyEducacensoStagesFactory::new()->create(['id' => 9001]);
    $second = LegacyEducacensoStagesFactory::new()->create(['id' => 9002]);

    $payload = ($this->novo)([
        'descricao' => 'noturno',
        'carga_horaria' => '1.200,5',
        'ato_poder_publico' => 'Portaria 1',
        'objetivo_curso' => 'Formar leitores',
        'publico_alvo' => 'Crianças de seis a dez anos',
        'modalidade_curso' => 2,
        'padrao_ano_escolar' => 'on',
        'multi_seriado' => 'on',
        'importar_curso_pre_matricula' => 'on',
        'bloquear_novas_matriculas' => 'on',
        'etapacurso' => [$first->getKey(), '', $second->getKey()],
    ]);

    expect($payload['tipoacao'])->toBe('Novo');

    $this->post('/intranet/educar_curso_cad.php', $payload)->assertRedirect('educar_curso_lst.php');

    $course = LegacyCourse::query()->where('nm_curso', 'Curso Alfa')->sole();

    expect($course->getAttributes())
        ->sgl_curso->toBe('CA')
        ->descricao->toBe('noturno')
        ->qtd_etapas->toBe(3)
        ->carga_horaria->toEqual(1200.5)
        ->hora_falta->toEqual(0.75)
        ->ato_poder_publico->toBe('Portaria 1')
        ->objetivo_curso->toBe('Formar leitores')
        ->publico_alvo->toBe('Crianças de seis a dez anos')
        ->modalidade_curso->toBe(2)
        ->padrao_ano_escolar->toBe(1)
        ->multi_seriado->toBe(1)
        ->ativo->toBe(1)
        ->ref_usuario_cad->toBe($this->user->getKey())
        ->ref_cod_instituicao->toBe($this->user->ref_cod_instituicao);

    expect((bool) $course->importar_curso_pre_matricula)->toBeTrue()
        ->and((bool) $course->bloquear_novas_matriculas)->toBeTrue()
        // A opção vazia da lista de etapas é ignorada.
        ->and(($this->stages)($course->getKey()))->toBe([9001, 9002]);
});

test('cadastra um curso só com o obrigatório, com as caixas desmarcadas', function () {
    $this->post('/intranet/educar_curso_cad.php', ($this->novo)(['etapacurso' => []]))
        ->assertRedirect('educar_curso_lst.php');

    $course = LegacyCourse::query()->where('nm_curso', 'Curso Alfa')->sole();

    expect($course->getAttributes())
        ->padrao_ano_escolar->toBe(0)
        ->multi_seriado->toBe(0)
        ->carga_horaria->toEqual(800);

    expect((bool) $course->importar_curso_pre_matricula)->toBeFalse()
        ->and((bool) $course->bloquear_novas_matriculas)->toBeFalse()
        ->and(($this->stages)($course->getKey()))->toBe([]);
});

test('recusa o cadastro com quantidade de etapas ou hora-falta inválida', function (array $overrides, string $message) {
    $this->post('/intranet/educar_curso_cad.php', ($this->novo)($overrides))
        ->assertOk()
        ->assertSee($message, false);

    expect(LegacyCourse::query()->where('nm_curso', 'Curso Alfa')->exists())->toBeFalse();
})->with([
    'zero etapas' => [['qtd_etapas' => '0'], "O campo 'Quantidade Etapas' deve ser maior que zero."],
    'etapas não numéricas' => [['qtd_etapas' => 'três'], "O campo 'Quantidade Etapas' deve ser maior que zero."],
    'hora-falta negativa' => [['hora_falta' => '-1'], "O campo 'Hora Falta (min)' não pode ser negativo."],
]);

test('recusa a edição com quantidade de etapas inválida', function () {
    $this->post($this->url, ($this->edicao)(['nm_curso' => 'Curso Mudado', 'qtd_etapas' => '0']))
        ->assertOk()
        ->assertSee("O campo 'Quantidade Etapas' deve ser maior que zero.", false);

    expect($this->course->refresh()->nm_curso)->toBe('Curso Existente');
});

test('avisa quando o cadastro não é gravado por falta de um campo obrigatório', function () {
    $payload = array_diff_key(($this->novo)(['etapacurso' => []]), ['ref_cod_nivel_ensino' => true]);

    $this->post('/intranet/educar_curso_cad.php', $payload)
        ->assertOk()
        ->assertSee('Cadastro não realizado.');

    expect(LegacyCourse::query()->where('nm_curso', 'Curso Alfa')->exists())->toBeFalse();
});

test('não grava nada quando o formulário chega só para incluir um item numa lista', function () {
    // O ícone de "+" ao lado das listas reenvia o formulário com incluir = S.
    $this->post('/intranet/educar_curso_cad.php', ($this->novo)(['incluir' => 'S']))->assertOk();
    $this->post($this->url, ($this->edicao)(['incluir' => 'S', 'nm_curso' => 'Curso Mudado']))->assertOk();

    expect(LegacyCourse::query()->where('nm_curso', 'Curso Alfa')->exists())->toBeFalse()
        ->and($this->course->refresh()->nm_curso)->toBe('Curso Existente');
});

test('edita o curso e sincroniza as etapas do Educacenso', function () {
    $kept = LegacyEducacensoStagesFactory::new()->create(['id' => 9001]);
    $removed = LegacyEducacensoStagesFactory::new()->create(['id' => 9002]);
    $added = LegacyEducacensoStagesFactory::new()->create(['id' => 9003]);

    foreach ([$kept, $removed] as $stage) {
        LegacyCourseEducacensoStageFactory::new()->create(['curso_id' => $this->course, 'etapa_id' => $stage]);
    }

    $payload = ($this->edicao)([
        'nm_curso' => 'Curso Renomeado',
        'hora_falta' => '30',
        'carga_horaria' => '1.000,00',
        'etapacurso' => [$kept->getKey(), $added->getKey()],
    ]);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($this->url, $payload)->assertRedirect('educar_curso_lst.php');

    expect($this->course->refresh()->getAttributes())
        ->nm_curso->toBe('Curso Renomeado')
        ->hora_falta->toEqual(0.5)
        ->carga_horaria->toEqual(1000)
        ->ref_usuario_exc->toBe($this->user->getKey());

    expect(($this->stages)($this->course->getKey()))->toBe([9001, 9003]);
});

test('ao passar o curso para o padrão de ano escolar, apaga as etapas próprias das turmas dele', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create(['ref_cod_curso' => $this->course, 'ano' => now()->year]);
    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $schoolClass]);

    $this->post($this->url, ($this->edicao)(['padrao_ano_escolar' => 'on', 'etapacurso' => []]))
        ->assertRedirect('educar_curso_lst.php');

    expect((bool) $this->course->refresh()->padrao_ano_escolar)->toBeTrue()
        ->and(LegacySchoolClassStage::query()->where('ref_cod_turma', $schoolClass->getKey())->exists())->toBeFalse();
});

test('sem mudar o padrão de ano escolar, as etapas das turmas ficam como estão', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create(['ref_cod_curso' => $this->course, 'ano' => now()->year]);
    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $schoolClass]);

    $this->post($this->url, ($this->edicao)(['nm_curso' => 'Curso Renomeado', 'etapacurso' => []]))
        ->assertRedirect('educar_curso_lst.php');

    expect(LegacySchoolClassStage::query()->where('ref_cod_turma', $schoolClass->getKey())->exists())->toBeTrue();
});

test('excluir o curso só desativa e mantém a hora-falta dele', function () {
    $this->post($this->url, ($this->edicao)(['tipoacao' => 'Excluir']))
        ->assertRedirect('educar_curso_lst.php');

    expect($this->course->refresh()->getAttributes())
        ->ativo->toBe(0)
        ->ref_usuario_exc->toBe($this->user->getKey())
        ->hora_falta->toEqual(0.75);
});

// Defeito conhecido: com um código de curso não numérico a gravação falha e a
// tela ia avisar, mas ao se redesenhar ela consulta as etapas do curso com
// esse mesmo código e estoura por tipo. Vale para a edição e para a exclusão.
test('defeito: código de curso inválido estoura ao redesenhar o formulário', function (string $action) {
    $payload = ['cod_curso' => 'abc', 'tipoacao' => $action] + ($this->edicao)();

    $this->post('/intranet/educar_curso_cad.php', $payload)->assertServerError();

    expect($this->course->refresh()->ativo)->toBe(1);
})->with(['Editar', 'Excluir']);

test('avisa ao editar um curso que não existe', function () {
    $payload = ['cod_curso' => 999999999] + ($this->edicao)(['nm_curso' => 'Curso Fantasma']);

    $this->post('/intranet/educar_curso_cad.php', $payload)->assertOk()->assertSee('Edição não realizada.');

    expect(LegacyCourse::query()->where('nm_curso', 'Curso Fantasma')->exists())->toBeFalse();
});

test('volta para a listagem quando o usuário só pode visualizar curso', function () {
    LegacyGeneralConfigurationFactory::new()->create($this->institution + ['active_on_ieducar' => true]);

    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(566, modify: false, remove: false)->create());

    $this->get($this->url)->assertRedirect('educar_curso_lst.php');
});

test('o usuário institucional recebe a própria instituição num campo oculto', function () {
    LegacyGeneralConfigurationFactory::new()->create($this->institution + ['active_on_ieducar' => true]);

    $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(566)->create());

    $form = LegacyForm::from($this->get('/intranet/educar_curso_cad.php')->assertOk()->getContent());

    expect($form->hidden()['ref_cod_instituicao'])->toBe((string) $this->user->ref_cod_instituicao)
        ->and($form->options('ref_cod_instituicao'))->toBe([]);
});
