<?php

require_once "PHPUnit/Framework/TestCase.php";
require_once '../intranet/educar_acervo_lst.php';

/**
 * Description of educar_acervo_lstTest
 *
 * @author ieducar
 */
class educar_acervo_lstTest extends PHPUnit_Framework_TestCase{
   
    protected $object;
        
    protected function setUp() {
        $this->object = new indice;
        $_SESSION['id_pessoa'] = 1;
    }
        
   public function testGerar() {
     
     $this->assertNotNull($this->object->Gerar());
     $this->assertNotEmpty($this->object->Gerar(), $message = "A lista do acervo"
             . " de obras não pode estar vazia");

    
    }

}
