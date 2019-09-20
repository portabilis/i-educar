<?php

/*
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
 */

/**
 * Cadastro de nível de categoria.
 *
 * @author   Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Core
 * @since    Arquivo disponível desde a versão 1.0.0
 * @version  $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';


class clsIndexBase extends clsBase
{
  public function Formular() {
    $this->SetTitulo($this->_instituicao . 'Servidores - Cadastro Categoria N&iacute;vel');
    $this->processoAp = '829';
  }
}


class indice extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

    var $cod_categoria_nivel;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_categoria_nivel;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_categoria_nivel=$_GET["cod_categoria_nivel"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 829, $this->pessoa_logada, 3,  "educar_categoria_nivel_lst.php", true );

        if( is_numeric( $this->cod_categoria_nivel ) )
        {

            $obj = new clsPmieducarCategoriaNivel( $this->cod_categoria_nivel );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;


            $obj_permissoes = new clsPermissoes();
            if( $obj_permissoes->permissao_excluir( 829, $this->pessoa_logada, 3, null, true ) )
            {
                $this->fexcluir = true;
            }

                $retorno = "Editar";
            }
        }

        $this->url_cancelar = ($retorno == "Editar") ? "educar_categoria_nivel_det.php?cod_categoria_nivel={$registro["cod_categoria_nivel"]}" : "educar_categoria_nivel_lst.php";
        $this->nome_url_cancelar = "Cancelar";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' categoria/nível', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_categoria_nivel", $this->cod_categoria_nivel );

        // foreign keys

        // text
        $this->campoTexto( "nm_categoria_nivel", "Nome Categoria", $this->nm_categoria_nivel, 30, 255, true );


    }

    function Novo()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 829, $this->pessoa_logada, 3,  "educar_categoria_nivel_lst.php", true );


        $obj = new clsPmieducarCategoriaNivel( $this->cod_categoria_nivel, $this->pessoa_logada, $this->pessoa_logada, $this->nm_categoria_nivel, $this->data_cadastro, $this->data_exclusao, $this->ativo );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {

            $categoriaNivel = new clsPmieducarCategoriaNivel($cadastrou);
            $categoriaNivel = $categoriaNivel->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("categoria_nivel", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($categoriaNivel);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_categoria_nivel_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 829, $this->pessoa_logada, 3,  "educar_categoria_nivel_lst.php", true );

        $categoriaNivel = new clsPmieducarCategoriaNivel($this->cod_categoria_nivel);
        $categoriaNivelAntes = $categoriaNivel->detalhe();

        $obj = new clsPmieducarCategoriaNivel($this->cod_categoria_nivel, $this->pessoa_logada, $this->pessoa_logada, $this->nm_categoria_nivel, $this->data_cadastro, $this->data_exclusao, $this->ativo);
        $editou = $obj->edita();
        if( $editou )
        {

            $categoriaNivelDepois = $categoriaNivel->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("categoriaNivel", $this->pessoa_logada, $this->cod_categoria_nivel);
            $auditoria->alteracao($categoriaNivelAntes, $categoriaNivelDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_categoria_nivel_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir( 829, $this->pessoa_logada, 3,  "educar_categoria_nivel_lst.php", true );


        $obj = new clsPmieducarCategoriaNivel($this->cod_categoria_nivel, $this->pessoa_logada, $this->pessoa_logada, $this->nm_categoria_nivel, $this->data_cadastro, $this->data_exclusao, 0);

        $categoriaNivel = $obj->detalhe();

        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("categoria_nivel", $this->pessoa_logada, $this->cod_categoria_nivel);
            $auditoria->exclusao($categoriaNivel);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_categoria_nivel_lst.php');
        }

        $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";

        return false;
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
