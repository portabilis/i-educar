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

class clsIndexBase extends clsBase
{

    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Instituicao" );
        $this->processoAp = "559";
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

    var $cod_instituicao;
    var $nm_instituicao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $ref_idtlog;
    var $ref_sigla_uf;
    var $cep;
    var $cidade;
    var $bairro;
    var $logradouro;
    var $numero;
    var $complemento;
    var $nm_responsavel;
    var $ddd_telefone;
    var $telefone;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Gerar()
    {
        $this->titulo = "Institui&ccedil;&atilde;o - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;


        $this->addCabecalhos( array( "Nome da Institui&ccedil;&atilde;o" ) );

        // outros Filtros
        $this->campoTexto( "nm_instituicao", "Nome da Institui&ccedil;&atilde;o", $this->nm_instituicao, 30, 255, false );

        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_instituicao = new clsPmieducarInstituicao();
        $obj_instituicao->setOrderby( "nm_responsavel ASC" );
        $obj_instituicao->setLimite( $this->limite, $this->offset );
        $lista = $obj_instituicao->lista(
            $this->cod_instituicao,
            $this->ref_sigla_uf,
            $this->cep,
            $this->cidade,
            $this->bairro,
            $this->logradouro,
            $this->numero,
            $this->complemento,
            $this->nm_responsavel,
            $this->ddd_telefone,
            $this->telefone,
            $this->data_cadastro,
            $this->data_exclusao,
            1,
            $this->nm_instituicao
        );

        $total = $obj_instituicao->_total;

        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                $this->addLinhas( array(
                    "<a href=\"educar_instituicao_det.php?cod_instituicao={$registro["cod_instituicao"]}\">{$registro["nm_instituicao"]}</a>"
                ) );
            }
        }
        $this->addPaginador2( "educar_instituicao_lst.php", $total, $_GET, $this->nome, $this->limite );

        $this->largura = "100%";

        $this->breadcrumb('Listagem de instituições', [
            url('intranet/educar_index.php') => 'Escola',
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
