<?php

use App\Models\LegacyEvaluationRuleGradeYear;
use App\Models\LegacySchool;
use App\Models\LegacySchoolGradeDiscipline;
use Database\Factories\LegacyDeficiencyFactory;
use Database\Factories\LegacyDisciplineDependenceFactory;
use Database\Factories\LegacyDisciplineExemptionFactory;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyExemptionStageFactory;
use Database\Factories\LegacyKnowledgeAreaFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

// Outros testes guardam dublês destas classes no repositório estático do
// IedFinder; aqui as consultas precisam das classes reais, e os dublês dos
// outros testes não podem ficar presos às instâncias daqui.
function esqueceClassesDoIedFinder(): void
{
    $storage = new ReflectionProperty(CoreExt_Entity::class, '_classStorage');
    $classes = $storage->getValue();

    unset($classes['clspmieducarturma'], $classes['clspmieducarescola'], $classes['clspmieducarinstituicao']);

    $storage->setValue(null, $classes);
}

afterEach(fn () => esqueceClassesDoIedFinder());

beforeEach(function () {
    esqueceClassesDoIedFinder();

    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma Finder']);
    $this->enrollment = $this->scenario->enrolled('Ana Finder', $this->schoolClass);
    $this->registration = $this->enrollment->registration;
    $this->rule = $this->scenario->evaluationRule();
});

test('instituição e série que não existem viram exceção', function () {
    expect(fn () => App_Model_IedFinder::getInstituicao(0))->toThrow(App_Model_Exception::class, 'Instituição com o código "0" não existe.')
        ->and(fn () => App_Model_IedFinder::getSerie('x'))->toThrow(App_Model_Exception::class, 'Série com o código "0" não existe.');
});

test('lista as séries com o curso, a turma, as turmas do Censo, as matrículas e os turnos', function () {
    $grade = $this->scenario->grade;

    expect(App_Model_IedFinder::getSeriesWithCourse($this->scenario->institution))->toHaveKey($grade->getKey(), "{$grade->nm_serie} ({$this->scenario->course->nm_curso})")
        ->and(App_Model_IedFinder::getTurma($this->schoolClass->getKey())['nm_turma'])->toBe('Turma Finder')
        ->and(fn () => App_Model_IedFinder::getTurma(0))->toThrow(App_Model_Exception::class, 'Turma com o código "0" não existe.')
        ->and(App_Model_IedFinder::getTurmasEducacenso($this->scenario->school->getKey(), 2030))->toBe([$this->schoolClass->getKey() => 'Turma Finder - 2030'])
        ->and(App_Model_IedFinder::getMatriculas($this->schoolClass->getKey())->all())->toBe([$this->registration->getKey() => 'Ana Finder'])
        ->and(App_Model_IedFinder::getTurnos())->toContain($this->schoolClass->period->nome);
});

test('os componentes da série na escola exigem a escola e podem vir sem detalhes', function () {
    $math = $this->scenario->discipline('Matemática');

    expect(fn () => App_Model_IedFinder::getEscolaSerieDisciplina($this->scenario->grade->getKey(), null))->toThrow(App_Model_Exception::class, 'O parametro escolaId não pode ser nulo')
        ->and(collect(App_Model_IedFinder::getEscolaSerieDisciplina($this->scenario->grade->getKey(), $this->scenario->school->getKey(), trazerDetalhes: false))->pluck('id')->all())->toBe([$math->getKey()]);
});

test('os componentes definidos na turma respeitam as etapas específicas, a disciplina dispensada e podem vir sem detalhes', function () {
    $math = $this->scenario->discipline('Matemática');
    $history = $this->scenario->discipline('História');
    $art = $this->scenario->discipline('Arte');
    $class = ['turma_id' => $this->schoolClass, 'escola_id' => $this->scenario->school, 'ano_escolar_id' => $this->scenario->grade];
    LegacyDisciplineSchoolClassFactory::new()->create($class + ['componente_curricular_id' => $math, 'etapas_especificas' => 1, 'etapas_utilizadas' => '1,2']);
    LegacyDisciplineSchoolClassFactory::new()->create($class + ['componente_curricular_id' => $history, 'etapas_especificas' => 1, 'etapas_utilizadas' => '3']);
    LegacyDisciplineSchoolClassFactory::new()->create($class + ['componente_curricular_id' => $art]);

    $components = App_Model_IedFinder::getComponentesTurma($this->scenario->grade->getKey(), $this->scenario->school->getKey(), $this->schoolClass->getKey(), etapa: 2, trazerDetalhes: false);

    expect(collect($components)->pluck('id')->map(fn ($id) => (int) $id)->sort()->values()->all())->toBe(collect([$math->getKey(), $art->getKey()])->sort()->values()->all())
        ->and(App_Model_IedFinder::getEtapasComponente($this->schoolClass->getKey(), $math->getKey()))->toBe(['1', '2'])
        ->and(App_Model_IedFinder::getQtdeEtapasComponente($this->schoolClass->getKey(), $history->getKey()))->toBe(1)
        ->and(App_Model_IedFinder::getQtdeEtapasComponente($this->schoolClass->getKey(), $art->getKey()))->toBeNull()
        ->and(App_Model_IedFinder::getUltimaEtapaComponente($this->schoolClass->getKey(), $math->getKey()))->toBe('2');

    $dispensada = $this->scenario->schoolClass(['ref_cod_disciplina_dispensada' => $math->getKey()]);

    expect(App_Model_IedFinder::disciplinaDispensadaDaTurma(null))->toBeNull()
        ->and(App_Model_IedFinder::disciplinaDispensadaDaTurma($dispensada->getKey()))->toBe($math->getKey())
        ->and(collect(App_Model_IedFinder::getComponentesTurma($this->scenario->grade->getKey(), $this->scenario->school->getKey(), $dispensada->getKey(), trazerDetalhes: false))->pluck('id')->map(fn ($id) => (int) $id)->all())->not->toContain($math->getKey());
});

test('as etapas específicas da série na escola valem quando a turma não define as suas', function () {
    $math = $this->scenario->discipline('Matemática');
    LegacySchoolGradeDiscipline::query()->where('ref_cod_disciplina', $math->getKey())->update(['etapas_especificas' => 1, 'etapas_utilizadas' => '4']);

    expect(App_Model_IedFinder::getEtapasComponente($this->schoolClass->getKey(), $math->getKey()))->toBe(['4']);
});

test('a regra da turma e a da matrícula usam a regra diferenciada da série quando a escola pede ou o aluno tem deficiência', function () {
    $special = LegacyEvaluationRuleFactory::new()->create(['instituicao_id' => $this->scenario->institution]);
    $rule = $this->rule;
    $rule->forceFill(['regra_diferenciada_id' => $special->getKey()])->save();

    // A regra da turma vem das colunas legadas da série.
    $this->scenario->grade->update(['regra_avaliacao_id' => $rule->getKey(), 'regra_avaliacao_diferenciada_id' => $special->getKey()]);

    expect(App_Model_IedFinder::getRegraAvaliacaoPorTurma($this->schoolClass->getKey())->id)->toEqual($rule->getKey());

    LegacySchool::query()->whereKey($this->scenario->school->getKey())->update(['utiliza_regra_diferenciada' => true]);
    LegacyEvaluationRuleGradeYear::query()->where('regra_avaliacao_id', $rule->getKey())->update(['regra_avaliacao_diferenciada_id' => $special->getKey()]);

    expect(App_Model_IedFinder::getRegraAvaliacaoPorTurma($this->schoolClass->getKey())->id)->toEqual($special->getKey())
        ->and(App_Model_IedFinder::getRegraAvaliacaoPorMatricula($this->registration->getKey())->id)->toEqual($special->getKey());

    LegacySchool::query()->whereKey($this->scenario->school->getKey())->update(['utiliza_regra_diferenciada' => false]);
    $this->registration->student->individual->deficiency()->attach(LegacyDeficiencyFactory::new()->create());

    expect(App_Model_IedFinder::getRegraAvaliacaoPorMatricula($this->registration->getKey())->id)->toEqual($special->getKey());
});

test('a matrícula em dependência só cursa as disciplinas da dependência que não foram dispensadas', function () {
    $math = $this->scenario->discipline('Matemática');
    $history = $this->scenario->discipline('História');
    $art = $this->scenario->discipline('Arte');
    $this->registration->update(['dependencia' => true]);

    foreach ([$math, $art] as $discipline) {
        LegacyDisciplineDependenceFactory::new()->create([
            'ref_cod_matricula' => $this->registration,
            'ref_cod_disciplina' => $discipline,
            'ref_cod_escola' => $this->scenario->school,
            'ref_cod_serie' => $this->scenario->grade,
        ]);
    }

    LegacyExemptionStageFactory::new()->create(['ref_cod_dispensa' => LegacyDisciplineExemptionFactory::new()->create([
        'ref_cod_matricula' => $this->registration,
        'ref_cod_disciplina' => $art,
        'ref_cod_escola' => $this->scenario->school,
        'ref_cod_serie' => $this->scenario->grade,
    ])]);

    $components = App_Model_IedFinder::getComponentesPorMatricula($this->registration->getKey());

    expect(array_keys($components))->toBe([$math->getKey()])
        ->and($components)->not->toHaveKey($history->getKey());
});

test('conta os módulos da turma da matrícula e lista as etapas, as áreas e os lançamentos', function () {
    $area = LegacyKnowledgeAreaFactory::new()->create(['instituicao_id' => $this->scenario->institution, 'nome' => 'Área Finder']);

    // As etapas da escola leem a relação `module`, que o modelo da etapa não tem:
    // nas telas legadas, sem avisos do PHP, o nome de cada etapa sai vazio.
    $level = error_reporting(0);

    try {
        $etapas = App_Model_IedFinder::getEtapasEscola(2030, $this->scenario->school->getKey());
    } finally {
        error_reporting($level);
    }

    // O curso segue o ano escolar padrão: os módulos são as etapas da escola no ano.
    expect(App_Model_IedFinder::getQuantidadeDeModulosMatricula($this->registration->getKey()))->toBe(2)
        ->and(array_keys($etapas))->toBe([1, 2])
        ->and(collect(App_Model_IedFinder::getAreasConhecimento($this->scenario->institution))->pluck('nome')->all())->toContain('Área Finder')
        ->and(App_Model_IedFinder::getNotasLancadasAluno($this->registration->getKey(), 1, 1))->toBe([])
        ->and(App_Model_IedFinder::getFaltasLancadasAluno($this->registration->getKey(), 1, 1))->toBe([])
        ->and(App_Model_IedFinder::getEtapa(0))->toBeNull()
        ->and((new App_Model_IedFinder)->getDefaultValidatorCollection())->toBe([]);
});

test('sem o pacote da biblioteca, as consultas da biblioteca não encontram nada', function () {
    expect(fn () => App_Model_IedFinder::getBiblioteca(1))->toThrow(App_Model_Exception::class, 'Seu usuário não está vinculado a nenhuma biblioteca.')
        ->and(fn () => App_Model_IedFinder::getBibliotecaObra(1, 2))->toThrow(App_Model_Exception::class, 'Obra com o código "2" não existe.')
        ->and(App_Model_IedFinder::getBibliotecas())->toBe([])
        ->and(App_Model_IedFinder::getBibliotecaSituacoes())->toBe([])
        ->and(App_Model_IedFinder::getBibliotecaFontes())->toBe([])
        ->and(App_Model_IedFinder::getBibliotecaTiposCliente(1))->toBe([]);
});
