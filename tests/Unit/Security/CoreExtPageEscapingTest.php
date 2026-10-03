<?php

namespace Tests\Unit\Security;

use clsCampos;
use Core_Controller_Page_ListController;
use Core_Controller_Page_ViewController;
use CoreExt_Entity;
use TabelaArredondamento_Model_Tabela;
use Tests\TestCase;

/**
 * CVE-2025-9720, CVE-2025-9721, CVE-2025-7113 e relacionadas: as listagens e os
 * detalhes genéricos dos módulos exibiam os valores gravados sem escape.
 */
class CoreExtPageEscapingTest extends TestCase
{
    private const MARKER = '<b>CVECANARY</b>';

    private function entry(): TabelaArredondamento_Model_Tabela
    {
        return new TabelaArredondamento_Model_Tabela([
            'id' => 1,
            'nome' => 'x"\'>' . self::MARKER,
        ]);
    }

    public function test_detalhe_escapa_os_valores_da_entidade()
    {
        $entry = $this->entry();

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
    }

    public function test_listagem_escapa_os_valores_da_entidade()
    {
        $entry = $this->entry();

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

        $this->assertStringNotContainsString(self::MARKER, $cell);
        $this->assertStringContainsString(e(self::MARKER), $cell);
    }

    public function test_opcoes_de_lista_escapam_o_texto()
    {
        $html = (new clsCampos)->getCampoLista(
            'campo',
            'campo',
            '',
            [1 => 'x"\'>' . self::MARKER, 2 => 'optgroup:' . self::MARKER, 3 => 'Educa&ccedil;&atilde;o'],
            '',
            '',
            false,
            'geral'
        );

        $this->assertStringNotContainsString(self::MARKER, $html);
        $this->assertStringContainsString(e(self::MARKER), $html);
        $this->assertStringContainsString('Educa&ccedil;&atilde;o', $html);
    }
}
