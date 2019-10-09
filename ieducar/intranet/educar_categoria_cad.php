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
require_once( "include/pmieducar/geral.inc.php" );
require_once("include/pmieducar/clsPmieducarCategoriaObra.inc.php");
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Categoria obras" );
        $this->processoAp = "598";
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

    var $id;
    var $descricao;
    var $observacoes;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->id = $_GET["id"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(592, $this->pessoa_logada, 11,  "educar_categoria_lst.php");

        if(is_numeric($this->id)){
            $obj = new clsPmieducarCategoriaObra($this->id);
            $registro = $obj->detalhe();
            if($registro){
                //passa todos os valores obtidos no registro para atributos do objeto
                foreach($registro AS $campo => $val){
                    $this->$campo = $val;
                }

                $obj_permissoes = new clsPermissoes();
                if($obj_permissoes->permissao_excluir(592, $this->pessoa_logada, 11)){
                    $this->fexcluir = true;
                }
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_categoria_obra_det.php?id={$registro["id"]}" : "educar_categoria_lst.php";
        $this->nome_url_cancelar = "Cancelar";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' categoria', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    function Gerar(){
        $this->campoOculto("id", $this->id);
        $this->campoTexto("descricao", "Descri&ccedil;&atilde;o", $this->descricao, 30, 255, true);
        $this->campoMemo("observacoes", "Observa&ccedil;&otilde;es", $this->observacoes, 60, 5, false );
    }

    function Novo(){


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 592, $this->pessoa_logada, 11,  "educar_categoria_lst.php" );

        $obj = new clsPmieducarCategoriaObra(0, $this->descricao, $this->observacoes);
        $this->id = $cadastrou = $obj->cadastra();
        if($cadastrou){
      $obj->id = $this->id;
      $detalhe = $obj->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("categoria_obra", $this->pessoa_logada, $this->id);
      $auditoria->inclusao($detalhe);

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_categoria_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar(){


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(592, $this->pessoa_logada, 11, "educar_categoria_lst.php");

        $obj = new clsPmieducarCategoriaObra($this->id, $this->descricao, $this->observacoes);
    $detalheAntigo = $obj->detalhe();
        $editou = $obj->edita();
        if( $editou ){
      $detalheAtual = $obj->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("categoria_obra", $this->pessoa_logada, $this->id);
      $auditoria->alteracao($detalheAntigo, $detalheAtual);
            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_categoria_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir(){


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(592, $this->pessoa_logada, 11,  "educar_categoria_lst.php");

        $obj = new clsPmieducarCategoriaObra($this->id);
    $detalhe = $obj->detalhe();
        $excluiu = $obj->excluir();
        if($excluiu){

      $auditoria = new clsModulesAuditoriaGeral("categoria_obra", $this->pessoa_logada, $this->id);
      $auditoria->exclusao($detalhe);
            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_categoria_lst.php');
        }

        $this->mensagem = "N&atilde;o &eacute; poss&iacute;vel excluir esta categoria. Verifique se a mesma possui v&iacute;nculo com obras.<br>";

        $this->array_botao[] = 'Voltar';
        $this->array_botao_url_script[] = "go('educar_categoria_obra_det.php?id=". $this->id ."')";
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
