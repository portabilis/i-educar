<?php
/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Module
 * @since     07/2013
 * @version   $Id$
 */
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/public/geral.inc.php" );

require_once("include/modules/clsModulesRotaTransporteEscolar.inc.php");
require_once("include/modules/clsModulesEmpresaTransporteEscolar.inc.php");

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Rotas" );
        $this->processoAp = "21238";
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

    var $descricao;
    var $ref_idpes_destino;
    var $ano;
    var $tipo_rota;
    var $km_pav;
    var $km_npav;
    var $ref_cod_empresa_transporte_escolar;
    var $tercerizado;
    var $nome_destino;

    function Gerar()
    {
        $this->titulo = "Rotas - Listagem";

        foreach( $_GET AS $var => $val )
            $this->$var = ( $val === "" ) ? null: $val;



        $this->addCabecalhos( array(
            "Ano",
            "Código da rota",
            "Descrição",
            "Destino",
            "Empresa",
            "Terceirizado"
        ) );

        // Filtros de Foreign Keys
        $opcoes = array( "" => "Selecione" );

        $objTemp = new clsModulesEmpresaTransporteEscolar();
        $objTemp->setOrderby(' nome_empresa ASC');
        $lista = $objTemp->lista();
        if ( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista as $registro )
            {
                $opcoes["{$registro['cod_empresa_transporte_escolar']}"] = "{$registro['nome_empresa']}";
            }
        }else{
            $opcoes = array( "" => "Sem empresas cadastradas" );
        }

        $this->campoLista( "ref_cod_empresa_transporte_escolar", "Empresa", $opcoes, $this->ref_cod_empresa_transporte_escolar, "", false, "", "", false, false );
        $this->campoTexto('descricao','Descrição',$this->descricao,50,30);
        $this->campoNumero('ano','Ano',$this->cnh,4,5);
        $this->campoTexto('nome_destino','Destino',$this->nome_destino,50,30);


        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_rota = new clsModulesRotaTransporteEscolar();
        $obj_rota->setOrderby( " descricao ASC" );
        $obj_rota->setLimite( $this->limite, $this->offset );

        $lista = $obj_rota->lista(
            null,
            $this->descricao,
            null,
            $this->nome_destino,
            $this->ano,
            $this->ref_cod_empresa_transporte_escolar
        );

        $total = $obj_rota->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                $this->addLinhas( array(
                    "<a href=\"transporte_rota_det.php?cod_rota={$registro["cod_rota_transporte_escolar"]}\">{$registro["ano"]}</a>",
                    "<a href=\"transporte_rota_det.php?cod_rota={$registro["cod_rota_transporte_escolar"]}\">{$registro["cod_rota_transporte_escolar"]}</a>",
                    "<a href=\"transporte_rota_det.php?cod_rota={$registro["cod_rota_transporte_escolar"]}\">{$registro["descricao"]}</a>",
                    "<a href=\"transporte_rota_det.php?cod_rota={$registro["cod_rota_transporte_escolar"]}\">{$registro["nome_destino"]}</a>",
                    "<a href=\"transporte_rota_det.php?cod_rota={$registro["cod_rota_transporte_escolar"]}\">{$registro["nome_empresa"]}</a>",
                    "<a href=\"transporte_rota_det.php?cod_rota={$registro["cod_rota_transporte_escolar"]}\">".($registro["tercerizado"] == 'S'? 'Sim' : 'Não')."</a>"
                ) );
            }
        }

        $this->addPaginador2( "transporte_rota_lst.php", $total, $_GET, $this->nome, $this->limite );

        $obj_permissao = new clsPermissoes();

        if($obj_permissao->permissao_cadastra(21238, $this->pessoa_logada,7,null,true))
        {
            $this->acao = "go(\"/module/TransporteEscolar/Rota\")";
            $this->nome_acao = "Novo";
        }

        $this->largura = "100%";

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_transporte_escolar_index.php"                  => "Transporte escolar",
             ""                                  => "Listagem de rotas"
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
