<?php
require_once "PHPUnit/Framework/TestCase.php";
require_once '/home/ieducar/ieducar/ieducar/intranet/educar_biblioteca_cad.php';

class EducarBibliotecaCadTest extends PHPUnit_Framework_TestCase 
{

    protected $object;

    protected function setUp() {
        $this->object = new indice;
    }

    public function testInicializar() {
        // Remove the following lines when you implement this test.
        $this->assertStringEndsWith("ar", $this->object->Inicializar());
    }

    public function testGerar() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function testNovo() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function testEditar() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function testExcluir() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}
