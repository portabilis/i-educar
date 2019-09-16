<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once 'include/modules/clsModulesItinerarioTransporteEscolar.inc.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Itinerário" );
        $this->processoAp = "21238";
    }
}

class indice extends clsCadastro
{

    var $cod_rota;


    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_rota=$_GET["cod_rota"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "transporte_itinerario_cad.php?cod_rota={$this->cod_rota}" );

        $obj  = new clsModulesItinerarioTransporteEscolar();
        $excluiu = $obj->excluirTodos( $this->cod_rota );

        if($excluiu)
        {
            echo "<script>
                window.location='transporte_rota_det.php?cod_rota={$this->cod_rota}';
                </script>";
        }


        die();
        return;
    }

    function Gerar()
    {

    }

    function Novo()
    {

    }

    function Excluir()
    {

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
