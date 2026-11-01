<?php

use iEducar\Support\Navigation\Breadcrumb;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

uses(TestCase::class);

// As telas legadas rodam sem avisos do PHP: o LegacyController zera o error_reporting.
function comoTelaLegada(Closure $callback): mixed
{
    $level = error_reporting(0);

    try {
        return $callback();
    } finally {
        error_reporting($level);
    }
}

function listagem(array $propriedades = []): clsListagem
{
    $listagem = comoTelaLegada(fn () => new clsListagem);

    foreach ($propriedades as $nome => $valor) {
        $listagem->$nome = $valor;
    }

    return $listagem;
}

beforeEach(function () {
    $_GET = [];
    $_POST = [];
    request()->server->set('REQUEST_URI', '/intranet/educar_teste_lst.php');
});

afterEach(function () {
    $_GET = [];
    $_POST = [];
});

test('reabre a tela sem filtros com os últimos filtros usados nela', function () {
    Session::put('previous_filters', ['/intranet/educar_teste_lst.php' => 'ano=2030|' . now()]);

    try {
        listagem();
        $this->fail('A listagem deveria redirecionar para os filtros anteriores.');
    } catch (HttpResponseException $exception) {
        expect($exception->getResponse()->getTargetUrl())->toEndWith('/intranet/educar_teste_lst.php?ano=2030');
    }
});

test('guarda os filtros de no máximo quatro telas', function () {
    Session::put('previous_filters', ['/a' => 'x=1|t', '/b' => 'x=2|t', '/c' => 'x=3|t', '/d' => 'x=4|t']);
    $_GET = ['ano' => 2030];

    listagem();

    expect(Session::get('previous_filters'))
        ->toHaveCount(4)
        ->not->toHaveKey('/a')
        ->and(Session::get('previous_filters')['/intranet/educar_teste_lst.php'])->toStartWith('ano=2030|');
});

test('o Gerar padrão não monta nada', function () {
    expect(listagem()->Gerar())->toBeFalse();
});

test('o paginador usa a variável de página informada e mantém a query em texto', function () {
    $_GET = ['pagina_turmas' => 2];
    $listagem = listagem();

    $listagem->addPaginador2('educar_teste_lst.php', 50, 'ano=2030', 'formulario', 20, 3, 'pagina_turmas');

    expect($listagem->paginador2)
        ->toContain('Total de registros: 50')
        ->toContain('educar_teste_lst.php?ano=2030&amp;pagina_turmas=1');
});

test('usa o título da tela na ordem title, _title e titulo', function (string $propriedade) {
    $listagem = listagem([$propriedade => 'Título da tela']);

    comoTelaLegada(fn () => $listagem->RenderHTML());

    expect(view()->shared('title'))->toBe('Título da tela');
})->with(['_title', 'titulo']);

test('monta filtros, colunas ordenáveis, linhas especiais, paginador antigo, campos de resultado e botões', function () {
    $paginador = [
        ['<<', 'primeira', true], ['-10', 'menos_dez', true], ['>>', 'ultima', true], ['p10', 'mais_dez', true], ['3', 'pagina_3', true], ['4', '', false],
    ];

    foreach (range(5, 20) as $pagina) {
        $paginador[] = [(string) $pagina, "pagina_{$pagina}", true];
    }

    $listagem = listagem([
        'locale' => 'Turmas',
        'appendInTop' => true,
        'funcAcao' => 'function acao() { enviar(); }',
        'botao_submit' => true,
        'cabecalho' => ['Nome'],
        'colunas' => [[0, alTopCenter]],
        'campos_ordenacao' => [0 => 'nome'],
        'linhas' => [
            ['tipo' => 'html-puro', 'conteudo' => '<tr><td>Linha pura</td></tr>'],
            'Linha solta',
            ['Ana'],
        ],
        'paginador' => $paginador,
        'tituloFormResultado' => 'Resultado',
        'camposResultado' => [
            'rotulo' => ['rotulo', 'Rótulo', 'Valor do rótulo'],
            'texto' => ['texto', 'Texto', 'Valor do texto', 10, 20],
            'memo' => ['memo', 'Memo', 'Valor do memo', 10, 3],
            'lista' => ['lista', 'Lista', [1 => 'Um', 2 => 'Dois'], 2],
            'outra_lista' => ['lista', 'Lista', [1 => 'Um'], 1],
            'oculto' => ['oculto'],
        ],
        'acao_voltar' => 'history.back();',
        'acao_imprimir' => 'print();',
        'array_botao' => ['Alertar'],
        'array_botao_script' => ['alert(1)'],
        'array_botao_id' => ['alertar'],
    ]);
    $listagem->campoTexto('nome', 'Nome', 'Ana');

    $html = comoTelaLegada(fn () => $listagem->RenderHTML());

    expect(app(Breadcrumb::class)->getLegacy())->toBe('Turmas')
        ->and($html)
        ->toContain('function acao() { enviar(); }')
        ->toContain("<input type='submit' class='botaolistagem' value='Buscar' id='botao_busca'>")
        ->toContain('definirOrdenacao("nome")')
        ->toContain('<tr><td>Linha pura</td></tr>')
        ->toContain("colspan='1'>Linha solta</td>")
        ->toContain("<a href='primeira' class='nvp_paginador'><<</a>")
        ->toContain("<a href='mais_dez' class='nvp_paginador'>+10</a>")
        ->toContain("<span class='linkBory' style='text-decoration: underline; color: black;'> 4 </span>")
        ->toContain('<br>')
        ->toContain('<b>Resultado</b>')
        ->toContain('Valor do rótulo')
        ->toContain("<input class='form' type='text' name='texto' value='Valor do texto' size='10' maxlength='20'>")
        ->toContain('>Valor do memo</textarea>')
        ->toContain("<option value='2' selected>Dois</option>")
        ->toContain("id='imprimir' class='botaolistagem' onclick='javascript: print();'")
        ->toContain("<input type='button' class='botaolistagem' onclick='alert(1)' value='Alertar' id='alertar'>");
});

test('sem filtros, o breadcrumb da tela vem depois da listagem', function () {
    $listagem = listagem(['locale' => 'Escolas', 'acao_voltar' => 'history.back();']);

    comoTelaLegada(fn () => $listagem->RenderHTML());

    expect(app(Breadcrumb::class)->getLegacy())->toBe('Escolas');
});

test('com filtros, o breadcrumb da tela vem junto do formulário', function () {
    $listagem = listagem(['locale' => 'Cursos']);
    $listagem->campoTexto('nome', 'Nome', '');

    comoTelaLegada(fn () => $listagem->RenderHTML());

    expect(app(Breadcrumb::class)->getLegacy())->toBe('Cursos');
});
