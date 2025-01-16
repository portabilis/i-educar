<?php

namespace Tests\Unit\Intranet;

use clsCampos;
use Tests\TestCase;

class ClsCamposTest extends TestCase
{
    public function test_show_file_field()
    {
        $clsCampos = new clsCampos;

        $clsCampos->campoArquivo('file', 'Foto', '/caminho/foto', 40, '<b>Foto do aluno</b>');

        $this->assertEquals(
            $this->getHtmlCodeFromFile('form_scripts.html') . trim($this->getHtmlCodeFromFile('file_field.html')),
            $clsCampos->makeCampos()
        );
    }
}
