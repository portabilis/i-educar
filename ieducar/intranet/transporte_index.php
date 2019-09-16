<?php


$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{

    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Transporte Escolar" );
        $this->processoAp = "21234";
    }
}

class indice
{
    function RenderHTML()
    {
        return "
                <table width='100%' style='height: 100%;'>
                    <tr align=center valign='top'><td></td></tr>
                </table>
                ";
    }
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
