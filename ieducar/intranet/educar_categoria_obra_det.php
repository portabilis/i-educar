<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    *                                                                        *
    *   @author Prefeitura Municipal de ItajaÃ­                              *
    *   @updated 29/03/2007                                                  *
    *   Pacote: i-PLB Software PÃºblico Livre e Brasileiro                   *
    *                                                                        *
    *   Copyright (C) 2006  PMI - Prefeitura Municipal de ItajaÃ­            *
    *                       ctima@itajai.sc.gov.br                           *
    *                                                                        *
    *   Este  programa  Ã©  software livre, vocÃª pode redistribuÃ­-lo e/ou  *
    *   modificÃ¡-lo sob os termos da LicenÃ§a PÃºblica Geral GNU, conforme  *
    *   publicada pela Free  Software  Foundation,  tanto  a versÃ£o 2 da    *
    *   LicenÃ§a   como  (a  seu  critÃ©rio)  qualquer  versÃ£o  mais  nova.     *
    *                                                                        *
    *   Este programa  Ã© distribuÃ­do na expectativa de ser Ãºtil, mas SEM  *
    *   QUALQUER GARANTIA. Sem mesmo a garantia implÃ­cita de COMERCIALI-    *
    *   ZAÃÃO  ou  de ADEQUAÃÃO A QUALQUER PROPÃSITO EM PARTICULAR. Con-     *
    *   sulte  a  LicenÃ§a  PÃºblica  Geral  GNU para obter mais detalhes.   *
    *                                                                        *
    *   VocÃª  deve  ter  recebido uma cÃ³pia da LicenÃ§a PÃºblica Geral GNU     *
    *   junto  com  este  programa. Se nÃ£o, escreva para a Free Software    *
    *   Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA     *
    *   02111-1307, USA.                                                     *
    *                                                                        *
    * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once("include/clsBase.inc.php");
require_once("include/clsDetalhe.inc.php");
require_once("include/clsBanco.inc.php");
require_once("include/pmieducar/geral.inc.php");
require_once("include/pmieducar/clsPmieducarCategoriaObra.inc.php");

class clsIndexBase extends clsBase{
    function Formular(){
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Categoria Obras");
        $this->processoAp = 599;
    }
}

class indice extends clsDetalhe{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $titulo;

    var $id;
    var $descricao;
    var $observacoes;

    function Gerar(){
        $this->titulo = "Categoria Obras - Detalhe";

        $this->id = $_GET["id"];

        $tmp_obj = new clsPmieducarCategoriaObra($this->id);
        $registro = $tmp_obj->detalhe();
        if(!$registro){
            $this->simpleRedirect('educar_categoria_nivel_lst.php');
        }
        if($registro["id"]){
            $this->addDetalhe(array("C&oacute;digo", "{$registro["id"]}"));
        }
        if($registro["descricao"]){
            $this->addDetalhe(array("Descri&ccedil;&atilde;o", "{$registro["descricao"]}"));
        }
        if($registro["observacoes"]){
            $this->addDetalhe(array("Observa&ccedil;&otilde;es", "{$registro["observacoes"]}"));
        }

        $obj_permissoes = new clsPermissoes();
        if($obj_permissoes->permissao_cadastra(592, $this->pessoa_logada, 11)){
            $this->url_novo = "educar_categoria_cad.php";
            $this->url_editar = "educar_categoria_cad.php?id={$registro["id"]}";
        }

        $this->url_cancelar = "educar_categoria_lst.php";
        $this->largura = "100%";

        $this->breadcrumb('Listagem de categorias', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);
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
