<?php

require_once "PHPUnit/Framework/TestCase.php";
require_once '../intranet/educar_biblioteca_det.php';

class indiceTest extends PHPUnit_Framework_TestCase {

    
    protected $object;

    
    protected function setUp() {
        $this->object = new indice;
        $_SESSION['id_pessoa'] = 1;
        $_GET["cod_biblioteca"] = 1;
    }

    public function testGerar() {
        /* Esta função não possui retorno, tem o único propósito de redirecionar o 
         * usuário para a pagina de cadastro ou edição de uma biblioteca (educar_biblioteca_cad.php).
         * Sendo assim o teste se baseia na criação da url de direcionamento, se ela existe ou não,
         * o que vai depender da interação do usuário. 
         * Dessa forma nem todos os  testes rodarão devidamente.
         * Por default, antes da interação com o usuário todas as opções de url são nulas
         */
        $this->assertNull($this->object->url_novo);
        $this->assertNull($this->object->url_editar);
        $this->assertNull($this->object->url_cancelar);
        
    }

}
