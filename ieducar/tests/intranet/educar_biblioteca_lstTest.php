<?php

require_once "PHPUnit/Framework/TestCase.php";
require_once '../intranet/educar_biblioteca_lst.php';
class indiceTest extends PHPUnit_Framework_TestCase {

    protected $object;

   
    protected function setUp() {
        $this->object = new indice;
        $_SESSION['id_pessoa'] = 1;
    }

    
    public function testGerar() {
        /*Esta função tem o único propósito de listar as bibliotecas cadastradas
         * e direcionar o usuário no caso de cadastro de uma  biblioteca
         * Seu retorno deve ser sempre uma lista de bibliotecas cadastradas, e não pode ser nulo 
         * pois a biblioteca da portablis é pré cadastrada
         */
        $this->assertNotEmpty($this->object->Gerar());
        $this->assertNotNull($this->object->Gerar());  
   
    }

}
