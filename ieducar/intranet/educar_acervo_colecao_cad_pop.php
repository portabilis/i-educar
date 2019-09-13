<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Cole&ccedil;&atilde;o" );
        $this->processoAp = "593";
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
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

    var $cod_acervo_colecao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_colecao;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_biblioteca;

    function Inicializar()
    {
        $retorno = "Novo";


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 593, $this->pessoa_logada, 11,  "educar_acervo_colecao_lst.php" );

        //$this->url_cancelar = "";
        //$this->nome_url_cancelar = "Cancelar";
        return $retorno;
    }

    function Gerar()
    {
        echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir')}</script>";
        $this->campoOculto("ref_cod_biblioteca", $this->ref_cod_biblioteca);

        $this->campoTexto( "nm_colecao", "Cole&ccedil;&atilde;o", $this->nm_colecao, 30, 255, true );
        $this->campoMemo( "descricao", "Descri&ccedil;&atilde;o", $this->descricao, 60, 5, false );

    }

    function Novo()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 593, $this->pessoa_logada, 11,  "educar_acervo_colecao_lst.php" );


        $obj = new clsPmieducarAcervoColecao( $this->cod_acervo_colecao, $this->pessoa_logada, $this->pessoa_logada, $this->nm_colecao, $this->descricao, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_biblioteca );
        $this->cod_acervo_colecao = $cadastrou = $obj->cadastra();
        //$cadastrou = 5;
        if( $cadastrou )
        {
      $obj->cod_acervo_colecao = $this->cod_acervo_colecao;
      $acervo_colecao = $obj->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("acervo_colecao", $this->pessoa_logada, $this->cod_acervo_colecao);
      $auditoria->inclusao($acervo_colecao);
            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            echo "<script>
                    parent.document.getElementById('colecao').value = '$cadastrou';
                    parent.document.getElementById('ref_cod_acervo_colecao').disabled = false;
                    parent.document.getElementById('tipoacao').value = '';
                    parent.document.getElementById('formcadastro').submit();
                 </script>";
            die();
            return true;
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {
    }

    function Excluir()
    {
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
<script>
    document.getElementById('ref_cod_biblioteca').value = parent.document.getElementById('ref_cod_biblioteca').value;
</script>
