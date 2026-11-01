<?php

use App\Models\LegacyInstitution;
use Database\Factories\LegacyEmployeeFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Tests\Request\Support\EmployeeScenario;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

function telaDosRamos(array $propriedades = []): clsCadastro
{
    $tela = new class extends clsCadastro
    {
        public $ano;

        public $ref_cod_escola;

        public $ref_cod_instituicao;

        public $ref_cod_serie;

        public $ref_cod_curso;

        public $ref_cod_componente_curricular;

        public $data_inicial;

        public $data_final;

        public $ref_cod_biblioteca;

        public $idesco;
    };

    foreach ($propriedades as $nome => $valor) {
        $tela->$nome = $valor;
    }

    return $tela;
}

// As telas legadas rodam sem avisos do PHP (o LegacyController zera o error_reporting).
function semAvisosNosRamos(Closure $callback): mixed
{
    $level = error_reporting(0);

    try {
        return $callback();
    } finally {
        error_reporting($level);
    }
}

function nucleoDosInputs(clsCadastro $tela): Portabilis_View_Helper_Input_Core
{
    return new class($tela, new Portabilis_View_Helper_Inputs($tela)) extends Portabilis_View_Helper_Input_Core
    {
        public function chama(string $metodo, ...$argumentos)
        {
            return $this->$metodo(...$argumentos);
        }
    };
}

beforeEach(function () {
    Portabilis_View_Helper_Application::forgetLoadedAssets();
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->scenario = EnrollmentScenario::for($this->user, 2030);
});

test('o núcleo dos inputs resolve instituição, escola, curso, série, escolaridade e biblioteca pelo valor, pela tela ou pelo usuário', function () {
    $tela = telaDosRamos(['ref_cod_escola' => $this->scenario->school->getKey(), 'idesco' => 3]);
    $nucleo = nucleoDosInputs($tela);

    expect($nucleo->chama('inputValue', 'valor'))->toBe('valor')
        ->and($nucleo->chama('getInstituicaoId', 7))->toBe(7)
        ->and($nucleo->chama('getInstituicaoId'))->toBe($this->scenario->institution)
        ->and($nucleo->chama('getEscolaId', 9))->toBe(9)
        ->and($nucleo->chama('getCursoId', 5))->toBe(5)
        ->and($nucleo->chama('getSerieId', 6))->toBe(6)
        ->and($nucleo->chama('getEscolaridadesId', 4))->toBe(4)
        ->and($nucleo->chama('getEscolaridadesId'))->toBe(3)
        ->and($nucleo->chama('getBibliotecaId', 8))->toBe(8)
        ->and($nucleo->chama('getBibliotecaId'))->toBeNull()
        ->and(nucleoDosInputs(telaDosRamos(['ref_cod_biblioteca' => 2]))->chama('getBibliotecaId'))->toBe(2)
        ->and($nucleo->chama('hasNivelAcesso', 'POLI_INSTITUCIONAL'))->toBeTrue()
        ->and($nucleo->chama('getDataMapperFor', 'educacenso', 'ies'))->toBeInstanceOf(Educacenso_Model_IesDataMapper::class);
});

test('sem o pacote da biblioteca, a escola e a instituição da biblioteca da tela não existem', function () {
    $nucleo = nucleoDosInputs(telaDosRamos(['ref_cod_biblioteca' => 2]));

    expect(fn () => $nucleo->chama('getInstituicaoId'))->toThrow(App_Model_Exception::class)
        ->and(fn () => $nucleo->chama('getEscolaId'))->toThrow(App_Model_Exception::class);
});

test('carrega o estilo do helper quando existe', function () {
    $pasta = public_path('vendor/legacy/DynamicInput/Assets/Stylesheets');
    $criou = !is_dir($pasta) && mkdir($pasta, 0777, true);
    touch("{$pasta}/Setor.css");
    $tela = telaDosRamos();

    try {
        semAvisosNosRamos(fn () => (new Portabilis_View_Helper_Inputs($tela))->dynamic('setor'));
    } finally {
        unlink("{$pasta}/Setor.css");

        if ($criou) {
            rmdir($pasta);
        }
    }

    expect($tela->getAppendedOutput())->toContain('DynamicInput/Assets/Stylesheets/Setor.css');
});

test('o professor só vê os cursos, as séries e as turmas em que está alocado, e o ano filtra as turmas', function () {
    $schoolClass = $this->scenario->schoolClass(['nm_turma' => 'Turma Alocada', 'turma_turno_id' => $this->scenario->period(1)->getKey()]);
    $teacher = EmployeeScenario::named('Bia Professora', $this->scenario->institution);
    $teacher->role('Professora', 'bia');
    $teacher->allocation($this->scenario->school, ['periodo' => 1, 'ano' => 2030]);
    LegacyEmployeeFactory::new()->create(['ref_cod_pessoa_fj' => $teacher->employee->getKey()]);
    $this->actingAs(LegacyUserFactory::new()->institutional()->create(['cod_usuario' => $teacher->employee->getKey(), 'ref_cod_instituicao' => $this->scenario->institution]));
    LegacyInstitution::query()->whereKey($this->scenario->institution)->update(['exigir_vinculo_turma_professor' => 1]);
    LegacySchoolAcademicYearFactory::new()->create(['ref_cod_escola' => $this->scenario->school, 'ano' => 2031, 'turmas_por_ano' => 1]);

    $tela = telaDosRamos(['ano' => 2031, 'ref_cod_instituicao' => $this->scenario->institution, 'ref_cod_escola' => $this->scenario->school->getKey(), 'ref_cod_curso' => $this->scenario->course->getKey(), 'ref_cod_serie' => $this->scenario->grade->getKey()]);

    semAvisosNosRamos(fn () => (new Portabilis_View_Helper_Inputs($tela))->dynamic(['curso', 'serie']));

    LegacyInstitution::query()->whereKey($this->scenario->institution)->update(['exigir_vinculo_turma_professor' => 0]);

    semAvisosNosRamos(fn () => (new Portabilis_View_Helper_Inputs($tela))->dynamic('turma'));

    expect($tela->campos['ref_cod_turma'][3])->not->toHaveKey($schoolClass->getKey());
});

test('as datas, a escola obrigatória, o componente e a etapa da escola usam o que a tela já tem', function () {
    Session::put('nivel', App_Model_NivelTipoUsuario::ESCOLA);
    $tela = telaDosRamos([
        'data_inicial' => '01/02/2030',
        'data_final' => '15/12/2030',
        'ref_cod_componente_curricular' => 9,
        'ref_cod_escola' => $this->scenario->school->getKey(),
        'ano' => 2030,
    ]);

    semAvisosNosRamos(fn () => (new Portabilis_View_Helper_Inputs($tela))->dynamic(['dataInicial', 'dataFinal', 'escolaRequired', 'escolaObrigatorioParaNivelEscolar', 'componenteCurricular', 'etapaEscola']));
    semAvisosNosRamos(fn () => (new Portabilis_View_Helper_Inputs($tela))->dateDiaMes('aniversario', ['value' => '2030-02-10']));

    expect($tela->campos['data_inicial'][3])->toBe('01/02/2030')
        ->and($tela->campos['data_final'][3])->toBe('15/12/2030')
        ->and($tela->campos['aniversario'][3])->toBe('10/02/2030');
});

test('a pesquisa de IES mostra o código e o nome da instituição escolhida', function () {
    $id = DB::table('modules.educacenso_ies')->insertGetId(['ies_id' => 606060, 'nome' => 'Faculdade Escolhida', 'dependencia_administrativa_id' => 1, 'tipo_instituicao_id' => 1, 'uf' => 'SC', 'user_id' => $this->user->getKey(), 'created_at' => now()]);
    $tela = telaDosRamos();

    semAvisosNosRamos(fn () => (new Portabilis_View_Helper_Inputs($tela))->simpleSearchIes('ies', [], ['hiddenInputOptions' => ['options' => ['value' => $id]]]));

    expect(collect($tela->campos)->pluck(3)->filter(fn ($valor) => is_string($valor))->implode('|'))->toContain('606060 - Faculdade Escolhida');
});

test('a pesquisa simples genérica, a pesquisa comum (cujo helper não existe), deficiências e transtornos e os erros de uso dos inputs', function () {
    $tela = telaDosRamos();
    $inputs = new Portabilis_View_Helper_Inputs($tela);

    semAvisosNosRamos(function () use ($inputs) {
        $inputs->multipleSearchDeficiencias('deficiencias');
        $inputs->multipleSearchTranstornos('transtornos');
        $inputs->simpleSearch('pessoa', 'pessoa_generica');
    });

    expect(fn () => semAvisosNosRamos(fn () => $inputs->simpleSearch('pessoa', 'pessoa', [], ['hiddenInputOptions' => ['options' => ['value' => 1]]])))
        ->toThrow(Exception::class, 'please, define the method resourceValue in a resource subclass')
        ->and(fn () => semAvisosNosRamos(fn () => $inputs->simpleSearch('pessoa', 'id')))->toThrow(CoreExt_Exception::class, "the \$attrName (of the visible input) must be different than 'id'")
        ->and(fn () => semAvisosNosRamos(fn () => $inputs->search('busca')))->toThrow(CoreExt_Exception::class, "Class 'Portabilis_View_Helper_Input_Search' not found")
        ->and(fn () => $inputs->text('nome', ['label' => 'Nome'], ['options' => ['label' => 'Outro']]))->toThrow(Exception::class, "Don't send \$inputOptions and \$helperOptions['options'] at the same time!");

    foreach (['simpleSearchCliente', 'simpleSearchAcervo', 'multipleSearchAssuntos', 'multipleSearchCategoriaObra', 'multipleSearchAutores'] as $biblioteca) {
        expect(fn () => semAvisosNosRamos(fn () => $inputs->$biblioteca('campo')))->toThrow(CoreExt_Exception::class, 'not found in path');
    }
});

test('as datas informadas, a IES informada por texto e a lista padrão do select', function () {
    $tela = telaDosRamos();
    $inputs = new Portabilis_View_Helper_Inputs($tela);

    semAvisosNosRamos(function () use ($inputs) {
        $inputs->dynamic('dataInicial', ['value' => '05/02/2030']);
        $inputs->dynamic('dataFinal', ['value' => '20/12/2030']);
        $inputs->simpleSearchIes('ies', [], ['hiddenInputOptions' => ['options' => ['value' => 'IES digitada']], 'showIdOnValue' => false]);
    });

    $select = new class($tela, $inputs) extends Portabilis_View_Helper_Input_CoreSelect
    {
        protected function inputName()
        {
            return 'opcao';
        }
    };

    semAvisosNosRamos(fn () => $select->select());

    expect($tela->campos['data_inicial'][3])->toBe('05/02/2030')
        ->and($tela->campos['data_final'][3])->toBe('20/12/2030')
        ->and(collect($tela->campos)->pluck(3)->filter(fn ($valor) => is_string($valor))->implode('|'))->toContain('IES digitada')
        ->and($tela->campos)->toHaveKey('opcao');
});
