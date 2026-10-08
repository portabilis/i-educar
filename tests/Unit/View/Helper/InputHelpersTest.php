<?php

use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

// Tela de cadastro mínima onde os helpers colocam os campos.
function telaDosHelpers(array $propriedades = []): clsCadastro
{
    $tela = new class extends clsCadastro
    {
        public $ano;

        public $ref_cod_escola;

        public $ref_cod_instituicao;

        public $ref_cod_serie;

        public $ref_cod_curso;

        public $ref_cod_componente_curricular;
    };

    foreach ($propriedades as $nome => $valor) {
        $tela->$nome = $valor;
    }

    return $tela;
}

// Os helpers leem opções opcionais sem conferir se existem; as telas legadas
// rodam sem avisos do PHP (o LegacyController zera o error_reporting).
function semAvisos(Closure $callback): void
{
    $level = error_reporting(0);

    try {
        $callback();
    } finally {
        error_reporting($level);
    }
}

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
    Portabilis_View_Helper_Application::forgetLoadedAssets();
});

test('as pesquisas simples criam o campo oculto e o de texto, buscando o nome do registro escolhido', function (string $metodo, string $campo) {
    // As pesquisas do transporte leem tabelas que o pacote de transporte cria;
    // aqui elas existem só durante o teste.
    DB::unprepared('
        create table if not exists modules.empresa_transporte_escolar (cod_empresa_transporte_escolar int, ref_idpes int);
        create table if not exists modules.motorista (cod_motorista int, ref_idpes int);
        create table if not exists modules.ponto_transporte_escolar (cod_ponto_transporte_escolar int, descricao text);
        create table if not exists modules.rota_transporte_escolar (cod_rota_transporte_escolar int, descricao text);
        create table if not exists modules.veiculo (cod_veiculo int, descricao text, placa text);
    ');

    $tela = telaDosHelpers();
    $inputs = new Portabilis_View_Helper_Inputs($tela);

    semAvisos(function () use ($inputs, $metodo, $campo) {
        // Aluno, escola e menu não buscam o nome: o valor vem pronto da tela.
        $buscaNome = !in_array($metodo, ['simpleSearchAluno', 'simpleSearchEscola', 'simpleSearchMenu'], true);

        $inputs->$metodo($campo, [], ['hiddenInputOptions' => ['options' => ['value' => $buscaNome ? 1 : null]]]);
        $inputs->$metodo("{$campo}_vazio");
    });

    expect(count($tela->campos))->toBeGreaterThanOrEqual(2);
})->with([
    'país' => ['simpleSearchPais', 'pais'],
    'empresa' => ['simpleSearchEmpresa', 'empresa'],
    'pessoa jurídica' => ['simpleSearchPessoaj', 'pessoaj'],
    'motorista' => ['simpleSearchMotorista', 'motorista'],
    'ponto' => ['simpleSearchPonto', 'ponto'],
    'rota' => ['simpleSearchRota', 'rota'],
    'veículo' => ['simpleSearchVeiculo', 'veiculo'],
    'bairro' => ['simpleSearchBairro', 'bairro'],
    'distrito' => ['simpleSearchDistrito', 'distrito'],
    'IES' => ['simpleSearchIes', 'ies'],
    'rotinas de auditoria' => ['simpleSearchRotinasAuditoria', 'rotina'],
    'curso superior' => ['simpleSearchCursoSuperior', 'curso_superior'],
    'escola' => ['simpleSearchEscola', 'escola'],
    'projeto' => ['simpleSearchProjeto', 'projeto'],
    'menu' => ['simpleSearchMenu', 'menu'],
    'aluno' => ['simpleSearchAluno', 'aluno'],
]);

test('as pesquisas múltiplas carregam as opções e o texto de ajuda', function (string $metodo, string $campo) {
    $tela = telaDosHelpers();
    $inputs = new Portabilis_View_Helper_Inputs($tela);

    semAvisos(fn () => $inputs->$metodo($campo));

    expect($tela->campos)->not->toBeEmpty()
        ->and($tela->getAppendedOutput())->toContain('placeholder');
})->with([
    'documentos do atestado de vaga' => ['multipleSearchDocumentosAtestadoVaga', 'documentos'],
    'áreas de conhecimento' => ['multipleSearchAreasConhecimento', 'areas'],
    'cursos do aluno' => ['multipleSearchCursoAluno', 'cursos'],
    'benefícios' => ['multipleSearchBeneficios', 'beneficios'],
    'transtornos' => ['multipleSearchTranstornos', 'transtornos'],
]);

test('as pesquisas múltiplas por ajax montam o campo de cada recurso', function (string $classe, string $metodo) {
    $tela = telaDosHelpers();
    $helper = new $classe($tela, new Portabilis_View_Helper_Inputs($tela));

    semAvisos(fn () => $helper->$metodo('selecionados', ['options' => ['values' => [1]]]));

    expect($tela->campos)->not->toBeEmpty();
})->with([
    'deficiências' => [Portabilis_View_Helper_Input_Resource_MultipleSearchAjaxDeficiencias::class, 'multipleSearchDeficiencias'],
    'transtornos' => [Portabilis_View_Helper_Input_Resource_MultipleSearchAjaxTranstornos::class, 'multipleSearchTranstornos'],
    'áreas de conhecimento' => [Portabilis_View_Helper_Input_Resource_MultipleSearchAjaxAreasConhecimento::class, 'multipleSearchAjaxAreasConhecimento'],
]);

test('os campos dinâmicos montam as listas com o que a tela já tem', function (string $helper, string $campo) {
    $tela = telaDosHelpers(['ano' => 2030]);

    semAvisos(fn () => (new Portabilis_View_Helper_Inputs($tela))->dynamic($helper));

    expect($tela->campos)->not->toBeEmpty();
})->with([
    'componente curricular' => ['componenteCurricular', 'ref_cod_componente_curricular'],
    'turma' => ['turma', 'ref_cod_turma'],
    'etapa da escola' => ['etapaEscola', 'etapa'],
    'vínculo' => ['vinculo', 'vinculo'],
    'setor' => ['setor', 'id_setor'],
    'área de conhecimento' => ['areaConhecimento', 'area_conhecimento'],
    'escola obrigatória para o nível escolar' => ['escolaObrigatorioParaNivelEscolar', 'ref_cod_escola'],
    'escola obrigatória' => ['escolaRequired', 'ref_cod_escola'],
    'rotas' => ['rotas', 'ref_cod_rota_transporte_escolar'],
    'transferido' => ['transferido', 'ref_cod_matricula'],
    'escola de destino do transporte' => ['escolaDestinoTransporteEscolar', 'escola_destino_transporte_escolar'],
    'etapa' => ['etapa', 'etapa'],
    'curso' => ['curso', 'ref_cod_curso'],
    'data final' => ['dataFinal', 'data_final'],
    'data inicial' => ['dataInicial', 'data_inicial'],
    'componente curricular do diário' => ['componenteCurricularForDiario', 'ref_cod_componente_curricular'],
    'série' => ['serie', 'ref_cod_serie'],
]);

test('tipo de logradouro e dia e mês viram campos da tela', function () {
    $tela = telaDosHelpers();
    $inputs = new Portabilis_View_Helper_Inputs($tela);

    semAvisos(function () use ($inputs) {
        $inputs->tipoLogradouro();
        $inputs->dateDiaMes('aniversario');
    });

    expect(array_keys($tela->campos))->toContain('aniversario');
});

test('o campo de benefício só funciona com as opções prontas: sem elas usa a classe legada de benefícios, que foi removida', function () {
    $inputs = new Portabilis_View_Helper_Inputs(telaDosHelpers());

    expect(fn () => semAvisos(fn () => $inputs->beneficio()))->toThrow(Error::class, 'clsPmieducarAlunoBeneficio');

    $tela = telaDosHelpers();
    semAvisos(fn () => (new Portabilis_View_Helper_Inputs($tela))->beneficio([], ['resources' => [1 => 'Bolsa']]));

    expect($tela->campos)->not->toBeEmpty();
});
