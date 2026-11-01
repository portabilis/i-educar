<?php

use App\Models\LegacyInstitution;
use App\Models\LegacySchoolClassGrade;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\LegacySchoolClassStageFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\SchoolClassInepFactory;
use iEducar\Modules\SchoolClass\Period;
use Illuminate\Support\Facades\Cache;
use Tests\Request\Support\EnrollmentScenario;
use Tests\Request\Support\LegacyFields;

// O formulário é gravado por ajax em /turma; aqui só a abertura da tela.
beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);

    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->math = $this->scenario->discipline('Matemática');
    $this->stageType = DB::table('pmieducar.modulo')->where('ref_cod_instituicao', $this->scenario->institution)->value('cod_modulo');

    $this->open = fn ($schoolClass = null) => $this->get('/intranet/educar_turma_cad.php' . ($schoolClass ? '?cod_turma=' . $schoolClass->getKey() : ''));
});

test('abre o formulário de nova turma com o ano corrente', function () {
    $payload = LegacyFields::from(($this->open)()->assertOk()->getContent());

    expect($payload['tipoacao'])->toBe('Novo')
        ->and($payload['ano_letivo'])->toBe((string) now()->year)
        ->and($payload['visivel'])->toBe('1');
});

test('abre a turma em edição com componentes, etapas próprias, regente, Inep por turno e alunos enturmados', function () {
    LegacyInstitution::query()->whereKey($this->scenario->institution)->update(['componente_curricular_turma' => true]);
    $rule = $this->scenario->evaluationRule(['definir_componente_etapa' => 1]);
    $this->scenario->grade->update(['regra_avaliacao_id' => $rule->getKey()]);
    $this->scenario->course->update(['padrao_ano_escolar' => 0]);

    $schoolClass = $this->scenario->schoolClass([
        'nm_turma' => 'Turma Alfa',
        'turma_turno_id' => $this->scenario->period(Period::FULLTIME)->getKey(),
        'ref_cod_regente' => LegacyPersonFactory::new()->create(['nome' => 'Regina Regente']),
        'ref_cod_disciplina_dispensada' => $this->math->getKey(),
    ]);
    LegacyDisciplineSchoolClassFactory::new()->create([
        'componente_curricular_id' => $this->math,
        'ano_escolar_id' => $this->scenario->grade,
        'escola_id' => $this->scenario->school,
        'turma_id' => $schoolClass,
        'carga_horaria' => 80,
        'docente_vinculado' => 1,
        'etapas_especificas' => 1,
        'etapas_utilizadas' => '1,2',
    ]);
    LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $schoolClass, 'sequencial' => 1, 'ref_cod_modulo' => $this->stageType, 'data_inicio' => '2030-02-01', 'data_fim' => '2030-06-30', 'dias_letivos' => 100]);
    foreach ([null => 111, Period::FULLTIME => 444, Period::MORNING => 222, Period::AFTERNOON => 333] as $period => $inep) {
        SchoolClassInepFactory::new()->create(['cod_turma' => $schoolClass, 'cod_turma_inep' => $inep, 'turma_turno_id' => $period ?: null]);
    }
    Cache::put('hasStudentsPartials_' . $schoolClass->getKey(), true, 60);
    $this->scenario->enrolled('Bia Borges', $schoolClass);

    $response = ($this->open)($schoolClass)
        ->assertOk()
        ->assertSee('Regina Regente')
        ->assertSee('excluir_turma_com_matriculas();', false)
        ->assertSee('Matemática')
        ->assertSee("name='etapas_utilizadas[{$this->math->getKey()}]' value='1,2'", false)
        ->assertSee('01/02/2030');

    $payload = LegacyFields::from($response->getContent());

    expect($payload['tipoacao'])->toBe('Editar')
        ->and($payload['nm_turma'])->toBe('Turma Alfa')
        ->and($payload['codigo_inep_matutino'])->toBe('222')
        ->and($payload['codigo_inep_vespertino'])->toBe('333')
        ->and($payload['ref_cod_disciplina_dispensada'])->toBe((string) $this->math->getKey())
        ->and($payload)->not->toHaveKey('ano');
});

test('a turma multisseriada mostra as séries e manda definir os componentes nas séries da escola', function () {
    $schoolClass = $this->scenario->schoolClass(['multiseriada' => 1]);
    $second = $this->scenario->grade('Segundo Ano');
    foreach ([$this->scenario->grade, $second] as $grade) {
        LegacySchoolClassGrade::query()->create(['escola_id' => $this->scenario->school->getKey(), 'serie_id' => $grade->getKey(), 'turma_id' => $schoolClass->getKey(), 'boletim_id' => 1]);
    }

    ($this->open)($schoolClass)
        ->assertOk()
        ->assertSee('Os componentes curriculares de turmas multisseriadas devem ser definidos em suas respectivas Séries')
        ->assertSee('excluirAjax();', false);
});

test('a série sem componentes abre sem a lista de componentes', function () {
    $empty = $this->scenario->grade('Série Vazia');
    $schoolClass = $this->scenario->schoolClass(['ref_ref_cod_serie' => $empty]);

    ($this->open)($schoolClass)
        ->assertOk()
        ->assertDontSee('Nome abreviado');
});

test('reabre a tela com o que foi enviado quando o formulário volta por POST', function () {
    $schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma Alfa']);

    $this->post('/intranet/educar_turma_cad.php', ['tipoacao' => 'Editar', 'cod_turma' => $schoolClass->getKey(), 'nm_turma' => 'Turma Alfa'])
        ->assertOk();
});

test('o usuário de escola só edita turmas das próprias escolas', function () {
    LegacyGeneralConfigurationFactory::new()->create(['ref_cod_instituicao' => $this->scenario->institution, 'active_on_ieducar' => true]);
    $schoolClass = $this->scenario->schoolClass();
    $this->actingAs(LegacyUserFactory::new()->school()->withAccess(586)->create());

    ($this->open)($schoolClass)->assertRedirect('educar_turma_lst.php');

    $this->actingAs(LegacyUserFactory::new()->school()->withSchool($this->scenario->school)->withAccess(586)->create());

    ($this->open)($schoolClass)->assertOk();
});
