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
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Fonte" );
        $this->processoAp = "608";
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsDetalhe
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $titulo;

    var $cod_fonte;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_fonte;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        $this->titulo = "Fonte - Detalhe";
        

        $this->cod_fonte=$_GET["cod_fonte"];

        $tmp_obj = new clsPmieducarFonte( $this->cod_fonte );
        $registro = $tmp_obj->detalhe();

        if( ! $registro )
        {
            $this->simpleRedirect('educar_fonte_lst.php');
        }


        if( $registro["cod_fonte"] )
        {
            $this->addDetalhe( array( "Código Fonte", "{$registro["cod_fonte"]}") );
        }
        if( $registro["nm_fonte"] )
        {
            $this->addDetalhe( array( "Fonte", "{$registro["nm_fonte"]}") );
        }
        if( $registro["descricao"] )
        {
            $registro["descricao"] = nl2br($registro["descricao"]);
            $this->addDetalhe( array( "Descri&ccedil;&atilde;o", "{$registro["descricao"]}") );
        }

        $obj_permissoes = new clsPermissoes();
        if( $obj_permissoes->permissao_cadastra( 608, $this->pessoa_logada, 11 ) )
        {
        $this->url_novo = "educar_fonte_cad.php";
        $this->url_editar = "educar_fonte_cad.php?cod_fonte={$registro["cod_fonte"]}";
        }

        $this->url_cancelar = "educar_fonte_lst.php";
        $this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_biblioteca_index.php"                  => "Biblioteca",
         ""                                  => "Detalhe da fonte"
    ));
    $this->enviaLocalizacao($localizacao->montar());        
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
