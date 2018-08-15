<?php

namespace Tests\unit\Intranet;

use clsCampos;
use Tests\SuiteTestCase\TestCase;

require_once __DIR__ . '/../../../intranet/include/clsCampos.inc.php';


class ClsCamposTest extends TestCase
{
    public function testShowFileField()
    {
        $clsCampos = new clsCampos();

        $clsCampos->campoArquivo('file', 'Foto', '/caminho/foto', 40, '<b>Foto do aluno</b>');

        $this->assertEquals(
            $this->getHtmlCodeFromFile('form_scripts.html') . $this->getHtmlCodeFromFile('file_field.html'),
            $clsCampos->makeCampos());
    }
}