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
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Bloqueio do ano letivo" );
        $this->processoAp = "21251";
        $this->addEstilo("localizacaoSistema");
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

    var $ref_instituicao;
    var $ref_ano;
    var $data_inicio;
    var $data_fim;

    function Gerar()
    {
        $this->titulo = "Bloqueio do ano letivo - Listagem";

        foreach( $_GET AS $var => $val )
            $this->$var = ( $val === "" ) ? null: $val;

        $this->ref_ano = $this->ano;

        $this->addCabecalhos( array(
            "Instituição",
            'Ano',
            'Data inicial permitida',
            'Data final permitida',
        ) );

        $this->inputsHelper()->dynamic('instituicao');
        $this->inputsHelper()->dynamic('ano', array('value' => $this->ref_ano));


        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj = new clsPmieducarBloqueioAnoLetivo();
        $obj->setOrderby( "instituicao ASC, ref_ano DESC" );
        $obj->setLimite( $this->limite, $this->offset );

        $lista = $obj->lista(
            $this->ref_cod_instituicao,
            $this->ref_ano
        );

        $total = $obj->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                $data_inicio = dataToBrasil($registro['data_inicio']);
                $data_fim = dataToBrasil($registro['data_fim']);

                $this->addLinhas( array(
                    "<a href=\"educar_bloqueio_ano_letivo_det.php?ref_cod_instituicao={$registro["ref_cod_instituicao"]}&ref_ano={$registro["ref_ano"]} \">{$registro["instituicao"]}</a>",
                    "<a href=\"educar_bloqueio_ano_letivo_det.php?ref_cod_instituicao={$registro["ref_cod_instituicao"]}&ref_ano={$registro["ref_ano"]} \">{$registro["ref_ano"]}</a>",
                    "<a href=\"educar_bloqueio_ano_letivo_det.php?ref_cod_instituicao={$registro["ref_cod_instituicao"]}&ref_ano={$registro["ref_ano"]} \">{$data_inicio}</a>",
                    "<a href=\"educar_bloqueio_ano_letivo_det.php?ref_cod_instituicao={$registro["ref_cod_instituicao"]}&ref_ano={$registro["ref_ano"]} \">{$data_fim}</a>"
                ) );
            }
        }
        $this->addPaginador2( "educar_bloqueio_ano_letivo_lst.php", $total, $_GET, $this->nome, $this->limite );


        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if($obj_permissao->permissao_cadastra(21251, $this->pessoa_logada,3))
        {
            $this->acao = "go(\"educar_bloqueio_ano_letivo_cad.php\")";
            $this->nome_acao = "Novo";
        }
        //**

        $this->largura = "100%";

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_index.php"                  => "Escola",
             ""                                  => "Listagem de bloqueios do ano letivo"
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
