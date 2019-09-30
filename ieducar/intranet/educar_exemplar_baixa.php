<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once 'Portabilis/Date/Utils.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Exemplar" );
        $this->processoAp = "606";
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

    var $cod_exemplar;
    var $ref_cod_fonte;
    var $ref_cod_motivo_baixa;
    var $ref_cod_situacao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $permite_emprestimo;
    var $preco;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $data_aquisicao;
    var $data_baixa_exemplar;

    var $ref_cod_instituicao;
    var $ref_cod_escola;
    var $ref_cod_biblioteca;
    var $ref_cod_acervo;

    var $nm_biblioteca;

    function Inicializar()
    {
        //$retorno = "Novo";


        $this->cod_exemplar=$_GET["cod_exemplar"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 606, $this->pessoa_logada, 11,  "educar_exemplar_lst.php" );

        if( is_numeric( $this->cod_exemplar ) )
        {

            $obj = new clsPmieducarExemplar( $this->cod_exemplar );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                $obj_obra = new clsPmieducarAcervo($this->ref_cod_acervo);
                $det_obra = $obj_obra->detalhe();

                $obj_biblioteca = new clsPmieducarBiblioteca($det_obra["ref_cod_biblioteca"]);
                $obj_det = $obj_biblioteca->detalhe();

                $this->ref_cod_biblioteca = $det_obra["ref_cod_biblioteca"];
                $this->ref_cod_acervo = $det_obra["titulo"];


                //$this->ref_cod_instituicao = $obj_det["nm_biblioteca"];
                //$this->ref_cod_escola = $obj_det["ref_cod_escola"];
                $this->nm_biblioteca = $obj_det["nm_biblioteca"];


                //$this->data_aquisicao = dataFromPgToBr( $this->data_aquisicao );

            /*$obj_permissoes = new clsPermissoes();
            if( $obj_permissoes->permissao_excluir( 606, $this->pessoa_logada, 11 ) )
            {
                $this->fexcluir = true;
            }*/

                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_exemplar_det.php?cod_exemplar={$registro["cod_exemplar"]}" : "educar_exemplar_lst.php";
        $this->nome_url_cancelar = "Cancelar";

        $this->breadcrumb('Motivo de baixa do exemplar', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    function Gerar()
    {
        $this->data_baixa_exemplar = $this->data_baixa_exemplar ?: date('d/m/Y');
        // primary keys
        $this->campoOculto( "cod_exemplar", $this->cod_exemplar );

        $this->campoRotulo("biblioteca","Biblioteca",$this->nm_biblioteca);
        $this->campoRotulo("obra","Obra",$this->ref_cod_acervo);



        $opcoes = array( "" => "Selecione" );

        $objTemp = new clsPmieducarMotivoBaixa();
        $lista = $objTemp->lista(null, null, null, null, null, null, null, null, null, 1, $this->ref_cod_biblioteca);
        if ( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista as $registro )
            {
                $opcoes["{$registro['cod_motivo_baixa']}"] = "{$registro['nm_motivo_baixa']}";
            }
        }

        $this->campoLista( "ref_cod_motivo_baixa", "Motivo Baixa", $opcoes, $this->ref_cod_motivo_baixa );
        $this->campoData('data_baixa_exemplar', 'Data', $this->data_baixa_exemplar, TRUE);

        $this->nome_url_sucesso = "Efetuar baixa";
        $this->acao_enviar = "if(confirm(\"Deseja baixar este exemplar?\"))acao();";

    }

    function Novo()
    {
/*

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 606, $this->pessoa_logada, 11,  "educar_exemplar_lst.php" );

        $this->preco = str_replace(".","",$this->preco);
        $this->preco = str_replace(",",".",$this->preco);

        $obj = new clsPmieducarExemplar( $this->cod_exemplar, null, $this->ref_cod_motivo_baixa, $this->ref_cod_acervo, $this->ref_cod_situacao, $this->pessoa_logada, $this->pessoa_logada, $this->permite_emprestimo, $this->preco, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->data_aquisicao );
        $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            header( "Location: educar_exemplar_lst.php" );
            die();
            return true;
        }
*/
        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {



        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 606, $this->pessoa_logada, 11,  "educar_exemplar_lst.php" );

        $this->preco = str_replace(".","",$this->preco);
        $this->preco = str_replace(",",".",$this->preco);

        $obj = new clsPmieducarExemplar($this->cod_exemplar, $this->ref_cod_fonte, $this->ref_cod_motivo_baixa, $this->ref_cod_acervo, $this->ref_cod_situacao, $this->pessoa_logada, $this->pessoa_logada, $this->permite_emprestimo, $this->preco, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->data_aquisicao, NULL, NULL, Portabilis_Date_Utils::brToPgSQL($this->data_baixa_exemplar));
        $detalheAntigo = $obj->detalhe();
    $editou = $obj->edita();
        if( $editou )
        {
      $detalheAtual = $obj->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("exemplar", $this->pessoa_logada, $this->cod_exemplar);
      $auditoria->alteracao($detalheAntigo, $detalheAtual);
            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_exemplar_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
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
