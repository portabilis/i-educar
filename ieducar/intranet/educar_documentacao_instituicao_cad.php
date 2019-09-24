<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Documentação padrão" );
        $this->processoAp = "578";
    }
}

class indice extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

    var $cod_instituicao;

    function Inicializar()
    {
        $retorno = "Documentação padrão";

        $this->breadcrumb('Documentação padrão', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->cod_instituicao=$_GET["cod_instituicao"];

        return $retorno;
    }

    function Gerar()
    {
        Portabilis_View_Helper_Application::loadJavascript($this, array('/modules/Cadastro/Assets/Javascripts/DocumentacaoPadrao.js'));

        $obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
        $obj_usuario_det = $obj_usuario->detalhe();
        $this->ref_cod_escola = $obj_usuario_det["ref_cod_escola"];

        $this->campoOculto( "cod_instituicao", $this->cod_instituicao );
        $this->campoOculto( "pessoa_logada", $this->pessoa_logada );
        $this->campoOculto( "ref_cod_escola", $this->ref_cod_escola );


        $this->campoTexto( "titulo_documento", "Título", $this->titulo_documento, 30, 50, false );

        $this->campoArquivo('documento','Documentação padrão',$this->documento,40,Portabilis_String_Utils::toLatin1("<span id='aviso_formato'>São aceitos apenas arquivos no formato PDF com até 2MB.</span>", array('escape' => false)));

        $this->array_botao[] = 'Salvar';
        $this->array_botao_url_script[] = "go('educar_instituicao_lst.php')";

        $this->array_botao[] = 'Voltar';
        $this->array_botao_url_script[] = "go('educar_instituicao_lst.php')";
    }

}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>
