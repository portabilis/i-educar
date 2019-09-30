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
require_once 'include/modules/clsModulesRotaTransporteEscolar.inc.php';
require_once 'include/modules/clsModulesPessoaTransporte.inc.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Usuários de transporte" );
        $this->processoAp = "21240";
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

    var $cod_pessoa_transporte;
    var $ref_cod_rota_transporte_escolar;
    var $nome_pessoa;
    var $nome_destino;
    var $ano_rota;

    function Gerar()
    {

        $this->titulo = "Usuário de transporte - Listagem";

        foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ( $val === "" ) ? null: $val;

        $this->campoNumero("cod_pessoa_transporte","C&oacute;digo",$this->cod_pessoa_transporte,20,255,false);
        $this->campoTexto("nome_pessoa","Nome da pessoa", $this->nome_pessoa,50,255,false);
        $this->campoTexto("nome_destino","Nome do destino", $this->nome_destino,70,255,false);

        $this->campoTexto("ano_rota","Ano", $this->ano_rota,20,4,false);

        $this->inputsHelper()->dynamic('rotas',  array('required' =>  false));

        $obj_permissoes = new clsPermissoes();

        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        $this->addCabecalhos( array(
            "C&oacute;digo",
            "Nome da pessoa",
            "Rota",
            "Destino",
            "Ponto de embarque"
        ) );

        // Paginador
        $this->limite = 20;
        $this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj = new clsModulesPessoaTransporte();
        $obj->setLimite($this->limite,$this->offset);

        $lista = $obj->lista($this->cod_pessoa_transporte, null, $this->ref_cod_rota_transporte_escolar,null, null,$this->nome_pessoa,$this->nome_destino, $this->ano_rota);

        $total = $obj->_total;

        foreach ( $lista AS $registro ) {

            $this->addLinhas( array(
                "<a href=\"transporte_pessoa_det.php?cod_pt={$registro["cod_pessoa_transporte"]}\">{$registro["cod_pessoa_transporte"]}</a>",
                "<a href=\"transporte_pessoa_det.php?cod_pt={$registro["cod_pessoa_transporte"]}\">{$registro["nome_pessoa"]}</a>",
                "<a href=\"transporte_pessoa_det.php?cod_pt={$registro["cod_pessoa_transporte"]}\">{$registro["nome_rota"]}</a>",
                "<a href=\"transporte_pessoa_det.php?cod_pt={$registro["cod_pessoa_transporte"]}\">".(trim($registro["nome_destino"])=='' ? $registro["nome_destino2"] : $registro["nome_destino"])."</a>",
                "<a href=\"transporte_pessoa_det.php?cod_pt={$registro["cod_pessoa_transporte"]}\">{$registro["nome_ponto"]}</a>"
            ) );
        }

        $this->addPaginador2( "transporte_pessoa_lst.php", $total, $_GET, $this->nome, $this->limite );

        //**
        $this->largura = "100%";

        $obj_permissao = new clsPermissoes();

        if($obj_permissao->permissao_cadastra(21240, $this->pessoa_logada,7,null,true))
        {
            $this->acao = "go(\"../module/TransporteEscolar/Pessoatransporte\")";
            $this->nome_acao = "Novo";
        }

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_transporte_escolar_index.php"                  => "Transporte escolar",
             ""                                  => "Listagem de usu&aacute;rios de tranposrte"
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
