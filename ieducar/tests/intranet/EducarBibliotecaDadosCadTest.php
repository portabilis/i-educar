<?php

require_once "PHPUnit/Framework/TestCase.php";
require_once '/home/ieducar/ieducar/ieducar/intranet/educar_biblioteca_dados_cad.php';


class EducarBibliotecaDadosCadTest extends PHPUnit_Framework_TestCase
{
    protected $object;
    
    
    protected function setUp()
    {
        $this->object = new Indice_Biblioteca_dados();
    }
    
    public function testFeriadosNacionais()
    {
       
        $this->assertEquals(0,$this->object->CadastraFeriadosNacionais());
        
    }
    
    
}