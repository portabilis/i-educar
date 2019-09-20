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
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once( "include/pmieducar/clsPmieducarCategoriaObra.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Categoria de obras" );
        $this->processoAp = "598";
    }
}

class indice extends clsListagem
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    var $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    var $offset;

    var $id;
    var $descricao;
    var $observacoes;

    function Gerar()
    {
        $this->titulo = "Categoria de obras - Listagem";

        //passa todos os valores obtidos no GET para atributos do objeto
        foreach($_GET AS $var => $val){
            $this->$var = ($val === "") ? null: $val;
        }

        // outros Filtros
        $this->campoTexto("descricao", "Descri&ccedil;&atilde;o", $this->descricao, 49, 255, false);

        $this->addCabecalhos(array('Descri&ccedil;&atilde;o', 'Observa&ccedil;&otilde;es'));

        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_categoria_obra = new clsPmieducarCategoriaObra();
        $obj_categoria_obra->setOrderby("descricao ASC");
        $obj_categoria_obra->setLimite($this->limite, $this->offset);

        $lista = $obj_categoria_obra->lista($this->descricao);

        $total = $obj_categoria_obra->_total;

        // monta a lista
        if(is_array($lista) && count( $lista)){
            foreach($lista AS $registro){
                $this->addLinhas( array(
                    "<a href=\"educar_categoria_obra_det.php?id={$registro["id"]}\">{$registro["descricao"]}</a>",
                    "<a href=\"educar_categoria_obra_det.php?id={$registro["id"]}\">{$registro['observacoes']}</a>"
                ));
            }
        }
        $this->addPaginador2("educar_categoria_lst.php", $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if($obj_permissoes->permissao_cadastra(592, $this->pessoa_logada, 11)){
            $this->acao = "go(\"educar_categoria_cad.php\")";
            $this->nome_acao = "Novo";
        }

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
