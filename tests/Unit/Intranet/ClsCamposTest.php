<?php

namespace Tests\unit\Intranet;

use clsCampos;
use Tests\SuiteTestCase\TestCase;

class ClsCamposTest extends TestCase
{
    public function testShowFileField()
    {
        $clsCampos = new clsCampos();

        $clsCampos->campoArquivo('file', 'Foto', '/caminho/foto', 40, '<b>Foto do aluno</b>');

        $expected =
            file_get_contents(__DIR__ . '/../assets/form_scripts.html') .
            file_get_contents(__DIR__ . '/../assets/file_field.html');
        $this->assertEquals(
            $expected,
            $clsCampos->makeCampos()
        );
    }
}