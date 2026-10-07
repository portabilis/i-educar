<?php

use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyExemptionStage;
use Database\Factories\LegacyDisciplineExemptionFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyExemptionTypeFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Database\Factories\LegacyStageTypeFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Api\DiarioApiFakeDataTestTrait;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;
use Tests\Request\Support\LegacyForm;

uses(DiarioApiFakeDataTestTrait::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    // Matrícula enturmada numa turma com regra de avaliação, duas etapas no ano
    // letivo e dois componentes curriculares vinculados à série e à escola:
    // gravar uma dispensa reprocessa a promoção da matrícula.
    $enrollment = $this->getPromotionFromAverageAndAttendanceWithoutRetake();
    $this->schoolClass = $enrollment->schoolClass;
    $this->registration = $enrollment->registration;
    $this->createStages($this->schoolClass->school, 2);
    $this->createDisciplines($this->schoolClass, 2);

    [$this->arts, $this->botany] = $this->schoolClass->disciplines()->orderBy('id')->get()->all();

    $institution = ['ref_cod_instituicao' => $this->schoolClass->ref_cod_instituicao];

    $this->medical = LegacyExemptionTypeFactory::new()->create($institution + ['nm_tipo' => 'Atestado médico']);
    $this->report = LegacyExemptionTypeFactory::new()->create($institution + ['nm_tipo' => 'Laudo']);

    $this->new = '/intranet/educar_dispensa_disciplina_cad.php?ref_cod_matricula=' . $this->registration->getKey();
    $this->list = 'educar_dispensa_disciplina_lst.php?ref_cod_matricula=' . $this->registration->getKey();

    $this->exemption = function ($discipline, array $stages = [1]) {
        $exemption = LegacyDisciplineExemptionFactory::new()->create([
            'ref_cod_matricula' => $this->registration,
            'ref_cod_disciplina' => $discipline->getKey(),
            'ref_cod_escola' => $this->schoolClass->ref_ref_cod_escola,
            'ref_cod_serie' => $this->schoolClass->ref_ref_cod_serie,
            'ref_cod_tipo_dispensa' => $this->medical,
            'observacao' => 'Dispensa original',
        ]);

        foreach ($stages as $stage) {
            LegacyExemptionStage::query()->create(['ref_cod_dispensa' => $exemption->getKey(), 'etapa' => $stage]);
        }

        return $exemption;
    };

    $this->edit = fn ($exemption) => '/intranet/educar_dispensa_disciplina_cad.php?' . http_build_query([
        'ref_cod_matricula' => $this->registration->getKey(),
        'ref_cod_disciplina' => $exemption->ref_cod_disciplina,
    ]);

    $this->exemptions = fn () => LegacyDisciplineExemption::query()->where('ref_cod_matricula', $this->registration->getKey())->get()->keyBy('ref_cod_disciplina');
    $this->stages = fn ($exemption) => LegacyExemptionStage::query()->where('ref_cod_dispensa', $exemption->getKey())->orderBy('etapa')->pluck('etapa')->all();
});

test('abre o cadastro com os tipos de dispensa ativos e as etapas do ano letivo', function () {
    LegacyExemptionTypeFactory::new()->create(['nm_tipo' => 'Tipo extinto'])->update(['ativo' => 0]);

    $html = $this->get($this->new)->assertOk()->getContent();
    $fields = LegacyFields::from($html);
    $form = LegacyForm::from($html);

    expect($fields['tipoacao'])->toBe('Novo')
        ->and($fields['ref_cod_matricula'])->toBe((string) $this->registration->getKey())
        ->and($fields['ref_cod_turma'])->toBe((string) $this->schoolClass->getKey())
        ->and($fields['ano'])->toBe((string) $this->registration->ano)
        ->and($form->options('ref_cod_tipo_dispensa'))->toContain('Atestado médico', 'Laudo')
        ->and($form->options('ref_cod_tipo_dispensa'))->not->toContain('Tipo extinto')
        ->and($html)->toContain('name="etapa[1]"')
        ->and($html)->toContain('name="etapa[2]"')
        ->and($html)->not->toContain('name="etapa[3]"');
});

test('em curso sem padrão de ano escolar as etapas vêm da turma', function () {
    $this->schoolClass->course->update(['padrao_ano_escolar' => 0]);

    $stageType = LegacyStageTypeFactory::new()->create(['nm_tipo' => 'Trimestre']);

    foreach ([1, 2, 3] as $sequence) {
        LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $this->schoolClass, 'ref_cod_modulo' => $stageType, 'sequencial' => $sequence]);
    }

    $this->get($this->new)->assertOk()->assertSee('3º Trimestre')->assertSee('name="etapa[3]"', false);
});

test('avisa que a matrícula precisa estar enturmada', function () {
    $registration = LegacyRegistrationFactory::new()->create(['ativo' => 1]);

    $this->get('/intranet/educar_dispensa_disciplina_cad.php?ref_cod_matricula=' . $registration->getKey())
        ->assertOk()
        ->assertSee('Para dispensar um aluno de um componente curricular, é necessário que este esteja enturmado.');
});

// Defeito conhecido: a tela se prepara para avisar quando a busca dos
// componentes falha, mas captura App_Model_Exception e a busca lança outra
// classe; a matrícula de uma turma sem componentes dá erro 500.
test('defeito: estoura quando a série da turma não tem componentes na escola', function () {
    $scenario = EnrollmentScenario::for($this->user, 2030);
    $registration = $scenario->registration();
    $scenario->enroll($registration, $scenario->schoolClass());

    $this->get('/intranet/educar_dispensa_disciplina_cad.php?ref_cod_matricula=' . $registration->getKey())->assertServerError();
});

test('dispensa o aluno dos componentes escolhidos nas etapas marcadas', function () {
    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'componentecurricular' => [$this->arts->getKey(), $this->botany->getKey()],
        'ref_cod_tipo_dispensa' => $this->medical->getKey(),
        'etapa' => [1 => 1, 2 => 2],
        'observacao' => 'Atestado de 60 dias',
    ]);

    $this->post($this->new, $payload)->assertRedirect($this->list);

    $exemptions = ($this->exemptions)();

    expect($exemptions)->toHaveCount(2);

    foreach ([$this->arts, $this->botany] as $discipline) {
        expect($exemptions[$discipline->getKey()])
            ->ref_cod_tipo_dispensa->toBe($this->medical->getKey())
            ->observacao->toBe('Atestado de 60 dias')
            ->ref_cod_serie->toBe($this->schoolClass->ref_ref_cod_serie)
            ->ref_cod_escola->toBe($this->schoolClass->ref_ref_cod_escola)
            ->ativo->toBe(1)
            ->and(($this->stages)($exemptions[$discipline->getKey()]))->toBe([1, 2]);
    }
});

test('dispensar de novo um componente já dispensado regrava a dispensa em vez de duplicar', function () {
    $exemption = ($this->exemption)($this->arts, [1]);

    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'componentecurricular' => [$this->arts->getKey()],
        'ref_cod_tipo_dispensa' => $this->report->getKey(),
        'etapa' => [2 => 2],
        'observacao' => 'Laudo novo',
    ]);

    $this->post($this->new, $payload)->assertRedirect($this->list);

    expect(($this->exemptions)())->toHaveCount(1)
        ->and($exemption->refresh()->ref_cod_tipo_dispensa)->toBe($this->report->getKey())
        ->and($exemption->observacao)->toBe('Laudo novo')
        ->and(($this->stages)($exemption))->toBe([2]);
});

test('recusa o componente fora da série da escola dizendo qual é', function () {
    $loose = LegacyDisciplineFactory::new()->create(['nome' => 'Componente Avulso']);

    $payload = LegacyFields::from($this->get($this->new)->assertOk()->getContent(), [
        'componentecurricular' => [$loose->getKey()],
        'ref_cod_tipo_dispensa' => $this->medical->getKey(),
        'etapa' => [1 => 1],
    ]);

    $this->post($this->new, $payload)
        ->assertOk()
        ->assertSee('O(s) componente(s):<b>Componente Avulso</b>. não está(ão) habilitado(s) na série da escola.', false);

    expect(($this->exemptions)())->toBeEmpty();
});

test('recusa a dispensa sem nenhuma etapa', function (bool $editing) {
    $url = $editing ? ($this->edit)(($this->exemption)($this->arts, [1])) : $this->new;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), [
        'componentecurricular' => [$this->botany->getKey()],
        'ref_cod_tipo_dispensa' => $this->medical->getKey(),
    ]);

    unset($payload['etapa']);

    $this->post($url, $payload)->assertOk()->assertSee('É necessário informar pelo menos uma etapa.');

    expect(($this->exemptions)())->toHaveCount($editing ? 1 : 0);
})->with([
    'no cadastro' => false,
    'na edição' => true,
]);

test('abre a edição com o componente fixo, o tipo, a observação e as etapas já dispensadas marcadas', function () {
    $exemption = ($this->exemption)($this->arts, [2]);

    $html = $this->get(($this->edit)($exemption))->assertOk()->assertSee($this->arts->nome)->getContent();
    $fields = LegacyFields::from($html);

    expect($fields['tipoacao'])->toBe('Editar')
        ->and($fields['cod_dispensa'])->toBe((string) $exemption->getKey())
        ->and($fields['ref_cod_disciplina'])->toBe((string) $this->arts->getKey())
        ->and($fields['ref_cod_tipo_dispensa'])->toBe((string) $this->medical->getKey())
        ->and($fields['observacao'])->toBe('Dispensa original')
        ->and($fields['etapa'])->toBe([2 => '2']);
});

test('edita o tipo, a observação e as etapas da dispensa', function () {
    $exemption = ($this->exemption)($this->arts, [1]);

    $payload = LegacyFields::from($this->get(($this->edit)($exemption))->assertOk()->getContent(), [
        'ref_cod_tipo_dispensa' => $this->report->getKey(),
        'observacao' => 'Observação alterada',
        'etapa' => [2 => 2],
    ]);

    $this->post(($this->edit)($exemption), $payload)->assertRedirect($this->list);

    expect($exemption->refresh())
        ->ref_cod_tipo_dispensa->toBe($this->report->getKey())
        ->observacao->toBe('Observação alterada')
        ->and(($this->stages)($exemption))->toBe([2]);
});

test('exclui a dispensa e as etapas dela', function () {
    $exemption = ($this->exemption)($this->arts, [1, 2]);
    $kept = ($this->exemption)($this->botany, [1]);

    $payload = LegacyFields::from($this->get(($this->edit)($exemption))->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post(($this->edit)($exemption), $payload)->assertRedirect($this->list);

    expect(($this->exemptions)()->keys()->all())->toBe([$this->botany->getKey()])
        ->and(($this->stages)($exemption))->toBe([])
        ->and(($this->stages)($kept))->toBe([1]);
});

test('volta para a listagem de matrículas sem matrícula ou com matrícula inexistente', function () {
    $this->get('/intranet/educar_dispensa_disciplina_cad.php')->assertRedirect('educar_matricula_lst.php');
    $this->get('/intranet/educar_dispensa_disciplina_cad.php?ref_cod_matricula=999999999')->assertRedirect('educar_matricula_lst.php');
});

// Defeito conhecido: o redirecionamento de quem não pode cadastrar leva o
// parâmetro com o nome errado (`ref_ref_cod_matricula`); a listagem não
// reconhece a matrícula e manda o usuário para outra tela.
test('defeito: quem só pode visualizar volta para a listagem de dispensas sem a matrícula', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->schoolClass->ref_cod_instituicao, 'active_on_ieducar' => true]);

    $this->actingAs(
        LegacyUserFactory::new()->institutional()->withAccess(628, modify: false, remove: false)->create(['ref_cod_instituicao' => $this->schoolClass->ref_cod_instituicao])
    );

    $this->get($this->new)->assertRedirect('educar_dispensa_disciplina_lst.php?ref_ref_cod_matricula=' . $this->registration->getKey());
});
