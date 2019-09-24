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

require_once("include/modules/clsModulesEmpresaTransporteEscolar.inc.php");
require_once("include/modules/clsModulesVeiculo.inc.php");

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Veículos" );
        $this->processoAp = "21237";
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

    var $cod_veiculo;
    var $descricao;
    var $placa;
    var $renavam;
    var $marca;
    var $ativo;
    var $cod_empresa;
    var $nome_motorista;

    function Gerar()
    {
        $this->titulo = "Veículos - Listagem";

        foreach( $_GET AS $var => $val )
            $this->$var = ( $val === "" ) ? null: $val;



        $this->addCabecalhos( array(
            "Código do veículo",
            "Descrição",
            "Placa",
            "Marca",
            "Empresa",
            "Motorista responsável"
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

        $this->campoNumero('cod_veiculo','Código do veículo',$this->cod_veiculo,29,15);
        $this->campoTexto( "descricao", "Descrição", $this->descricao, 29, 50, false );
        $this->campoTexto( "placa", "Placa", $this->placa, 29, 10, false );
        $this->campoTexto( "renavam", "Renavam", $this->renavam, 29, 30, false );
        $this->campoTexto( "marca", "Marca", $this->marca, 29, 50, false );

        $this->campoLista( "ativo", "Ativo", array( null => 'Selecione', 'S' => 'Ativo', 'N' => 'Inativo'), $this->ativo, "", false, "", "", false, false );
        $this->campoLista( "cod_empresa", "Empresa", $opcoes, $this->cod_empresa, "", false, "", "", false, false );
        $this->campoTexto( "nome_motorista", "Motorista responsável", $this->nome_motorista, 29, 30, false );


        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj = new clsModulesVeiculo();
        $obj->setOrderby( " descricao ASC" );
        $obj->setLimite( $this->limite, $this->offset );

        $lista = $obj->lista(
            $this->cod_veiculo,
            $this->descricao,
            $this->placa,
            $this->renavam,
            $this->nome_motorista,
            $this->cod_empresa,
            $this->marca,
            $this->ativo

        );

        $total = $obj->_total;

        // monta a lista
        if( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista AS $registro )
            {
                $this->addLinhas( array(
                    "<a href=\"transporte_veiculo_det.php?cod_veiculo={$registro["cod_veiculo"]}\">{$registro["cod_veiculo"]}</a>",
                    "<a href=\"transporte_veiculo_det.php?cod_veiculo={$registro["cod_veiculo"]}\">{$registro["descricao"]}</a>",
                    "<a href=\"transporte_veiculo_det.php?cod_veiculo={$registro["cod_veiculo"]}\">{$registro["placa"]}</a>",
                    "<a href=\"transporte_veiculo_det.php?cod_veiculo={$registro["cod_veiculo"]}\">{$registro["marca"]}</a>",
                    "<a href=\"transporte_veiculo_det.php?cod_veiculo={$registro["cod_veiculo"]}\">{$registro["nome_empresa"]}</a>",
                    "<a href=\"transporte_veiculo_det.php?cod_veiculo={$registro["cod_veiculo"]}\">{$registro["nome_motorista"]}</a>"
                ) );
            }
        }

        $this->addPaginador2( "transporte_veiculo_lst.php", $total, $_GET, $this->nome, $this->limite );

        $this->largura = "100%";

        $obj_permissao = new clsPermissoes();

        if($obj_permissao->permissao_cadastra(21237, $this->pessoa_logada,7,null,true))
        {
            $this->acao = "go(\"/module/TransporteEscolar/Veiculo\")";
            $this->nome_acao = "Novo";
        }

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_transporte_escolar_index.php"                  => "Transporte escolar",
             ""                                  => "Listagem de ve&iacute;culos"
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
