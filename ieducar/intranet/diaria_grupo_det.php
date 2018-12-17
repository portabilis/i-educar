<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    *                                                                        *
    *   @author Prefeitura Municipal de Itajaí                               *
    *   @updated 29/03/2007                                                  *
    *   Pacote: i-PLB Software Público Livre e Brasileiro                    *
    *                                                                        *
    *   Copyright (C) 2006  PMI - Prefeitura Municipal de Itajaí             *
    *                       ctima@itajai.sc.gov.br                           *
    *                                                                        *
    *   Este  programa  é  software livre, você pode redistribuí-lo e/ou     *
    *   modificá-lo sob os termos da Licença Pública Geral GNU, conforme     *
    *   publicada pela Free  Software  Foundation,  tanto  a versão 2 da     *
    *   Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.    *
    *                                                                        *
    *   Este programa  é distribuído na expectativa de ser útil, mas SEM     *
    *   QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-     *
    *   ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-     *
    *   sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.     *
    *                                                                        *
    *   Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU     *
    *   junto  com  este  programa. Se não, escreva para a Free Software     *
    *   Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA     *
    *   02111-1307, USA.                                                     *
    *                                                                        *
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{
    
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} Diária Grupo" );
        $this->processoAp = "297";
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsDetalhe
{
    function Gerar()
    {
        $this->titulo = "Detalhe do Grupo";

        $cod_diaria_grupo = $_GET['cod_diaria_grupo'] ?? null;
        
        $db = new clsBanco();

        if ($cod_diaria_grupo) {
            $db->Consulta( "SELECT cod_diaria_grupo, desc_grupo FROM pmidrh.diaria_grupo WHERE cod_diaria_grupo='{$cod_diaria_grupo}'" );
        }

        if( $cod_diaria_grupo && $db->ProximoRegistro()) {
            list( $cod_diaria_grupo, $desc_grupo ) = $db->Tupla();
            $this->addDetalhe( array("Grupo", $desc_grupo) );
        } else {
            $this->addDetalhe( array( "Erro", "Codigo de diária grupo inválido" ) );
        }

        $this->url_editar = "diaria_grupo_cad.php?cod_diaria_grupo={$cod_diaria_grupo}";
        $this->url_novo = "diaria_grupo_cad.php";
        $this->url_cancelar = "diaria_grupo_lst.php";
        $this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Escola",
         ""                                  => "Detalhe do grupo de di&aacute;rias"
    ));
    $this->enviaLocalizacao($localizacao->montar());        
    }
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();
?>
