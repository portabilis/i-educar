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
 * Cadastro de nível.
 *
 * @author      Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  Relatório
 * @since       Arquivo disponível desde a versão 1.0.0
 * @version     $Id$
 */

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';


class clsIndexBase extends clsBase
{
  public function Formular()
  {
    $this->SetTitulo($this->_instituicao . 'Nivel');
    $this->processoAp   = '829';
    $this->renderBanner = FALSE;
    $this->renderMenu   = FALSE;
    $this->renderMenuSuspenso = FALSE;
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

    var $cod_nivel;
    var $ref_cod_categoria;
    var $ref_cod_nivel;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $ref_cod_nivel_anterior;
    var $nm_nivel;
    var $salario_base;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $nm_categoria;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->ref_cod_categoria = $_GET["ref_cod_categoria"];
        $this->ref_cod_nivel = $_GET["ref_cod_nivel"];

        $obj_permissoes = new clsPermissoes();
        $permite_cadastrar = $obj_permissoes->permissao_cadastra( 829, $this->pessoa_logada, 3,  "", true );

        if(!$permite_cadastrar)
        {
            echo "<script>window.parent.fechaExpansivel( '{$_GET['div']}');</script>";
            die();
        }

        if( is_numeric( $this->ref_cod_categoria ) && is_numeric( $this->ref_cod_nivel ))
        {

            $obj_nivel_categoria = new clsPmieducarNivel();
            $lst_nivel_categoria = $obj_nivel_categoria->lista($this->ref_cod_nivel,$this->ref_cod_categoria,null,null,null,null,null,null,null,null);

            if( $lst_nivel_categoria )
            {

                $lst_niveis = array_shift($lst_nivel_categoria);

                $obj = new clsPmieducarCategoriaNivel( $this->ref_cod_categoria );
                $registro  = $obj->detalhe();

                $this->nm_categoria = $registro['nm_categoria_nivel'];

                $this->nm_nivel = $lst_niveis['nm_nivel'];

                $obj_niveis = new clsPmieducarSubnivel();
                $obj_niveis->setOrderby("cod_subnivel");
                $lst_niveis = $obj_niveis->lista(null,null,null,null,$this->ref_cod_nivel,null,null,null,null,null,1);

                if($lst_niveis)
                {
                    foreach ($lst_niveis as $id => $nivel)
                    {
                        $id++;
                        $nivel['salario'] = number_format($nivel['salario'],2,',','.');
                        $this->cod_nivel[] = array($nivel['nm_subnivel'],$nivel['salario'],$id,$nivel['cod_subnivel']);
                    }
                }else
                {
                    $this->cod_nivel[] = array('','','1','');
                }

                $retorno = "Editar";
            }
        }
        else
        {
            echo "<script>window.parent.fechaExpansivel( '{$_GET['div']}');</script>";
            die();
        }

        $this->url_cancelar = false;
        return $retorno;
    }

    function Gerar()
    {

        $this->campoOculto("ref_cod_categoria", $this->ref_cod_categoria);
        $this->campoOculto("ref_cod_nivel", $this->ref_cod_nivel);

        $this->campoRotulo("nm_categoria","Categoria",$this->nm_categoria);
        $this->campoRotulo("nm_nivel","Nível",$this->nm_nivel);

        $this->campoTabelaInicio("tab01","Subn&iacute;veis",array("Nome Subn&iacute;vel",'Sal&aacute;rio','Ordem'),$this->cod_nivel);

            $this->campoTexto("nm_nivel","Nome Subn&iacute;vel","",30,100,true);
            $this->campoMonetario( "salario_base", "Salario Base", $this->salario_base, 10, 8, true );
            $this->campoNumero("nr_nivel","Ordem","1",5,5,false,false,false,false,false,false,true);
            $this->campoOculto("cod_nivel","");

        $this->campoTabelaFim();




    }

    function Novo()
    {


        $obj_permissoes = new clsPermissoes();
        $permite_cadastrar = $obj_permissoes->permissao_cadastra( 829, $this->pessoa_logada, 3,  "",true );

        if(!$permite_cadastrar)
        {
            echo "<script>window.parent.fechaExpansivel( '{$_GET['div']}');</script>";
            die();
        }

        $obj = new clsPmieducarSubnivel(null,$this->pessoa_logada,$this->pessoa_logada,null,$this->ref_cod_nivel);

        // FIXME #parameters
        $obj->desativaTodos(null);

        if($this->nm_nivel)
        {
            $nivel_anterior = null;
            foreach ($this->nm_nivel as $id => $nm_nivel)
            {
                $obj_nivel = new clsPmieducarSubnivel($this->cod_nivel[$id],$this->pessoa_logada,$this->pessoa_logada,$nivel_anterior,$this->ref_cod_nivel,$nm_nivel,null,null,1,str_replace(',','.',str_replace('.','',$this->salario_base[$id])));
                if($obj_nivel->existe())
                {
                    $obj_nivel->edita();
                    $nivel_anterior = $this->cod_nivel[$id];
                }
                else
                    $nivel_anterior = $obj_nivel->cadastra();
            }

            echo "<script>window.parent.fechaExpansivel( '{$_GET['div']}');</script>";
            die();

        }


        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {
        if(!$this->Novo())

            return false;
    }

    function Excluir()
    {


        $obj_permissoes = new clsPermissoes();
        $permite_excluir= $obj_permissoes->permissao_excluir( 829, $this->pessoa_logada, 3,  "", true );

        if(!$permite_excluir)
        {
            echo "<script>window.parent.fechaExpansivel( '{$_GET['div']}');</script>";
            die();
        }

        $obj = new clsPmieducarSubnivel($this->cod_nivel,$this->pessoa_logada,$this->pessoa_logada);
        $excluiu = $obj->excluirTodos();
        if( $excluiu )
        {
            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";

            throw new HttpResponseException(
                new RedirectResponse("educar_categoria_nivel_det.php?cod_categoria_nivel={$this->ref_cod_categoria_nivel}")
            );
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

<script type="text/javascript">

    function setOrdem(id)
    {
        document.getElementById('nr_nivel['+(id)+']').value = (id+1);
    }

    tab_add_1.afterAddRow = function() {
        setOrdem(this.id-1);
    }

    tab_add_1.afterRemoveRow = function() {
        reordena();
    }

    function reordena()
    {
        for(var ct=0;ct < tab_add_1.getId();ct++)
        {
            setOrdem(ct);
        }
    }
</script>
