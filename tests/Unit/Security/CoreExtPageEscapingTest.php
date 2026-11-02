<?php

use Tests\TestCase;

/**
 * CVE-2025-9720, CVE-2025-9721, CVE-2025-7113 e relacionadas: as listagens e os
 * detalhes genéricos dos módulos exibiam os valores gravados sem escape.
 */
uses(TestCase::class);

beforeEach(function () {
    $this->entry = function (): TabelaArredondamento_Model_Tabela {
        return new TabelaArredondamento_Model_Tabela([
            'id' => 1,
            'nome' => 'x"\'>' . '<b>CVECANARY</b>',
        ]);
    };
});

test('detalhe escapa os valores da entidade', function () {
    $entry = ($this->entry)();

    $controller = new class($entry) extends Core_Controller_Page_ViewController
    {
        protected $_titulo = 'Teste';

        protected $_tableMap = ['Nome' => 'nome'];

        public function __construct(private CoreExt_Entity $fakeEntry) {}

        public function getEntry()
        {
            return $this->fakeEntry;
        }

        public function setUrlEditar(CoreExt_Entity $entry) {}

        public function setUrlCancelar(CoreExt_Entity $entry) {}
    };

    $controller->Gerar();

    $this->assertSame(['Nome', e($entry->nome)], $controller->detalhe[0]);
});

test('listagem escapa os valores da entidade', function () {
    $entry = ($this->entry)();

    $controller = new class($entry) extends Core_Controller_Page_ListController
    {
        protected $_titulo = 'Teste';

        protected $_tableMap = ['Nome' => 'nome'];

        public function __construct(private CoreExt_Entity $fakeEntry) {}

        public $nome = 'teste';

        public function getEntries()
        {
            return [$this->fakeEntry];
        }

        public function setAcao() {}
    };

    $_GET = ['pagina_teste' => 1];

    $controller->Gerar();

    $cell = $controller->linhas[0][0];

    $this->assertStringNotContainsString('<b>CVECANARY</b>', $cell);
    $this->assertStringContainsString(e('<b>CVECANARY</b>'), $cell);
});

test('opcoes de lista escapam o texto', function () {
    $html = (new clsCampos)->getCampoLista(
        'campo',
        'campo',
        '',
        [1 => 'x"\'>' . '<b>CVECANARY</b>', 2 => 'optgroup:' . '<b>CVECANARY</b>', 3 => 'Educa&ccedil;&atilde;o'],
        '',
        '',
        false,
        'geral'
    );

    $this->assertStringNotContainsString('<b>CVECANARY</b>', $html);
    $this->assertStringContainsString(e('<b>CVECANARY</b>'), $html);
    $this->assertStringContainsString('Educa&ccedil;&atilde;o', $html);
});

test('link do url helper escapa o texto', function () {
    $html = CoreExt_View_Helper_UrlHelper::l('x"\'>' . '<b>CVECANARY</b>', 'educar_servidor_det.php', ['query' => ['cod_servidor' => 1]]);

    $this->assertStringNotContainsString('<b>CVECANARY</b>', $html);
    $this->assertStringContainsString(e('<b>CVECANARY</b>'), $html);
    $this->assertStringStartsWith('<a href="', $html);
});

test('campo de cor escapa o valor', function () {
    $campos = new clsCampos;
    $campos->campoCor('color', 'Cor', '"><b>CVECANARY</b>', ['#FFFFFF']);

    $html = $campos->MakeCampos();

    $this->assertStringNotContainsString('<b>CVECANARY</b>', $html);
    $this->assertStringContainsString(e('<b>CVECANARY</b>'), $html);
});
