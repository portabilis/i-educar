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
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/pmieducar/geral.inc.php");
require_once ("include/modules/clsModulesAuditoriaGeral.inc.php");

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Motivo Abandono" );
        $this->processoAp = "950";
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

    var $cod_abandono_tipo;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nome;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_instituicao;

    function Inicializar()
    {
        $retorno = "Novo";

        $this->cod_abandono_tipo=$_GET["cod_abandono_tipo"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 950, $this->pessoa_logada, 7, "educar_abandono_tipo_lst.php" );

        if( is_numeric( $this->cod_abandono_tipo ) )
        {
            $obj = new clsPmiEducarAbandonoTipo();
            $lst  = $obj->lista( $this->cod_abandono_tipo );
            $registro  = array_shift($lst);
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                $this->fexcluir = $obj_permissoes->permissao_excluir( 950, $this->pessoa_logada,7 );
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_abandono_tipo_det.php?cod_abandono_tipo={$registro["cod_abandono_tipo"]}" : "educar_abandono_tipo_lst.php";
        $this->nome_url_cancelar = "Cancelar";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' tipo de abandono', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_abandono_tipo", $this->cod_abandono_tipo );

        $obrigatorio = true;
        include("include/pmieducar/educar_campo_lista.php");

        // text
        $this->campoTexto( "nome", "Motivo Abandono", $this->nome, 30, 255, true );
    }

    function Novo()
    {

        $obj = new clsPmiEducarAbandonoTipo( null,
                                             null,
                                             $this->pessoa_logada,
                                             $this->nome,
                                             null,
                                             null,
                                             1,
                                             $this->ref_cod_instituicao );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $abandonoTipo = new clsPmiEducarAbandonoTipo($cadastrou);
            $abandonoTipo = $abandonoTipo->detalhe();

            $auditoria = new clsModulesAuditoriaGeral("abandono_tipo", $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($abandonoTipo);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";

            $this->simpleRedirect('educar_abandono_tipo_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $abandonoTipoDetalhe = new clsPmiEducarAbandonoTipo($this->cod_abandono_tipo);
        $abandonoTipoDetalheAntes = $abandonoTipoDetalhe->detalhe();

        $obj = new clsPmiEducarAbandonoTipo( $this->cod_abandono_tipo,$this->pessoa_logada,null,$this->nome,null,null,1,$this->ref_cod_instituicao );
        $editou = $obj->edita();
        if( $editou )
        {
            $abandonoTipoDetalheDepois = $abandonoTipoDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral("abandono_tipo", $this->pessoa_logada, $this->cod_abandono_tipo);
            $auditoria->alteracao($abandonoTipoDetalheAntes, $abandonoTipoDetalheDepois);

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";

            $this->simpleRedirect('educar_abandono_tipo_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj = new clsPmiEducarAbandonoTipo( $this->cod_abandono_tipo, $this->pessoa_logada, null, null, null, null, null, 0);
        $abandonoTipo = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $auditoria = new clsModulesAuditoriaGeral("abandono_tipo", $this->pessoa_logada, $this->cod_abandono_tipo);
            $auditoria->exclusao($abandonoTipo);

            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";

            $this->simpleRedirect('educar_abandono_tipo_lst.php');
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
