<?php
require_once "PHPUnit/Framework/TestCase.php";
require_once '../intranet/educar_biblioteca_cad.php';

class EducarBibliotecaCadTest extends PHPUnit_Framework_TestCase 
{

    protected $object;
    protected $permissoes;

    protected function setUp() {
        $this->object = new indice;
        $this->permissoes = new clsPermissoes();
        $_SESSION['id_pessoa'] = 1;
        $_SESSION['tipo_biblioteca'] = null;
        $_GET["cod_biblioteca"] = 1;
    }

    public function testInicializar() {
        /* Se a função Inicializar for executada corretamente a variável $retorno 
         * deve passar de "Novo" para "Cancelar" ou "editar"
         */
        $this->assertStringEndsWith("ar", $this->object->Inicializar());
    }

    public function testGerar() {
        /* Para que seja possível rodar a função GERAR é necessário que exista o atributo 
         * biblioteca_usuario e que o atributo ref_cod_usuário se inicie como NULL
         */
        $this->assertObjectHasAttribute("biblioteca_usuario", $this->object);
        $this->assertNull($this->object->ref_cod_usuario);
    }

    public function testNovo() {
        // Se não acontecer interação o retorno da função novo deve obrigatoriamente ser FALSE
        $this->assertFalse($this->object->Novo()) ;
    }

    public function testEditar() {
        /* Se acontecer interação e houver algum cadastro para ser alterado, o retorno será TRUE,
         * caso contrario, o retorno padrão da função é False
         */
        $this->assertFalse($this->object->Editar());
    }

    public function testExcluir() {
        /* O retorno padrão da função é False caso não exista usuários a excluir
         * ou o usuário não tenha permissão para exclusão
         */
        
        $this->assertFalse($this->object->Excluir());
    }

}
