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
require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesPontoTransporteEscolar.inc.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Pontos" );
        $this->processoAp = "21239";
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

    var $cod_ponto;
    var $descricao;

    function Gerar()
    {

        $this->titulo = "Pontos - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;



        $this->campoNumero("cod_ponto","C&oacute;digo do ponto",$this->cod_ponto,20,255,false);
        $this->campoTexto("descricao","Descrição", $this->descricao,50,255,false);


        $obj_permissoes = new clsPermissoes();

        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        $this->addCabecalhos( array(
            "C&oacute;digo do ponto",
            "Descrição",
            'CEP',
            'Munic&iacute;pio - UF',
            'Bairro',
            'Logradouro'
        ) );

        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;


        $obj_ponto = new clsModulesPontoTransporteEscolar();
        $obj_ponto->setOrderBy(' descricao asc ');
        $obj_ponto->setLimite($this->limite,$this->offset);

        $pontos = $obj_ponto->lista($this->cod_ponto,$this->descricao);
        $total = $pontos->_total;

        foreach ( $pontos AS $registro ) {
            $cep = is_numeric($registro['cep']) ? int2CEP($registro["cep"]) : '-';
            $municipio = is_string($registro['municipio']) ? $registro["municipio"] . ' - '. $registro['sigla_uf'] : '-';
            $bairro = is_string($registro['bairro']) ? $registro["bairro"] : '-';
            $logradouro = is_string($registro['logradouro']) ? $registro["logradouro"] : '-';

            $this->addLinhas( array(
                "<a href=\"transporte_ponto_det.php?cod_ponto={$registro["cod_ponto_transporte_escolar"]}\">{$registro["cod_ponto_transporte_escolar"]}</a>",
                "<a href=\"transporte_ponto_det.php?cod_ponto={$registro["cod_ponto_transporte_escolar"]}\">{$registro["descricao"]}</a>",
                "<a href=\"transporte_ponto_det.php?cod_ponto={$registro["cod_ponto_transporte_escolar"]}\">{$cep}</a>",
                "<a href=\"transporte_ponto_det.php?cod_ponto={$registro["cod_ponto_transporte_escolar"]}\">{$municipio}</a>",
                "<a href=\"transporte_ponto_det.php?cod_ponto={$registro["cod_ponto_transporte_escolar"]}\">{$bairro}</a>",
                "<a href=\"transporte_ponto_det.php?cod_ponto={$registro["cod_ponto_transporte_escolar"]}\">{$logradouro}</a>",
            ) );
        }

        $this->addPaginador2( "transporte_ponto_lst.php", $total, $_GET, $this->nome, $this->limite );

        $obj_permissao = new clsPermissoes();

        if($obj_permissao->permissao_cadastra(21239, $this->pessoa_logada,7,null,true))
        {
            $this->acao = "go(\"../module/TransporteEscolar/Ponto\")";
            $this->nome_acao = "Novo";
        }

        $this->largura = "100%";

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_transporte_escolar_index.php"                  => "Transporte escolar",
             ""                                  => "Listagem de pontos"
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
