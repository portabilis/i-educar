<?php

require_once "PHPUnit/Framework/TestCase.php";
require_once '../intranet/educar_acervo_cad.php';

class educar_acervo_cadTest extends PHPUnit_Framework_TestCase 
{
    
    protected $object;
    protected $permissoes;

    protected function setUp() {
        $this->object = new indice;
        $this->permissoes = new clsPermissoes();
        $_SESSION['id_pessoa'] = 1;
        $_GET["cod_acervo"] = 1;
    }
    
    public function testInicializar() {
        
        $this->assertStringEndsWith("ar", $this->object->Inicializar(), 
               $message = "O retorno deve passar de 'Novo' para 'Cancelar' ou 'Editar");
       
    }
    
    public function testGerar() {
        
        $this->assertObjectHasAttribute("acervo_autor", $this->object, 
                $message = "O atributo acervo autor deve existir");
        $this->assertNull($this->object->ref_cod_acervo_autor, $message = 
                "A referencia para o autor da obra deve ser nula");
        $this->assertNull($this->object->principal, $message = 
                "A referÊncia principal deve ser nula");
                           
    }
    
    public function testNovo() {
       
        $this->assertFalse($this->object->Novo(), $message = 
                "O retorno da função deve ser False");
    }

    public function testEditar() {
      
        $this->assertFalse($this->object->Editar(), $message = 
                "Não existe cadastro para ser alterado");
    }

    public function testExcluir() {
               
        $this->assertFalse($this->object->Excluir(), $message = 
                "Não existem cadastros para serem exluidos");
    }

    
}

