<?php

require_once "PHPUnit/Framework/TestCase.php";
require_once '../intranet/educar_acervo_det.php';


/**
 * Description of educar_acervo_detTest
 *
 * @author ieducar
 */
class educar_acervo_detTest extends PHPUnit_Framework_TestCase{
   
    protected $object;
    

    protected function setUp() {
        $this->object = new indice;
        
        $_SESSION['id_pessoa'] = 1;
        $_GET["cod_acervo"] = 1;
    }
    
    public function testGerar() {
        
        $this->assertNull($this->object->url_novo, $message = 
                "A Url deve ser nula por default");
        $this->assertNull($this->object->url_editar, $message = 
                "A Url deve ser nula por default");
        $this->assertNull($this->object->url_cancelar, $message = 
                "A Url deve ser nula por default");
        
    }
}
