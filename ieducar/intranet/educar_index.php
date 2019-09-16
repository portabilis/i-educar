<?php


$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{

    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar" );
        $this->processoAp = 55;
    }
}

class indice
{
    function RenderHTML()
    {
        return "<!--
                <table width='100%' style='height: 100%;'>
                    <tr align=center valign='top'><td><div id='flash-container' align='right' style='width: 200px; right: 10px;top: 27px; position: absolute;'><p style='min-height: 0px;'' class='flash sucess'>Olá! Alteramos o menu do lançamento de notas, agora, acesse apenas <strong>Movimentação > Faltas/Notas</strong> e pronto! Qualquer dúvida, entre em contato. :)</p></div></td></tr>
                </table>-->
                ";
    }
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
