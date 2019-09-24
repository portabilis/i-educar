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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Editora" );
        $this->processoAp = "595";
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

    var $cod_acervo_editora;
    var $ref_usuario_cad;
    var $ref_usuario_exc;
    var $ref_idtlog;
    var $ref_sigla_uf;
    var $nm_editora;
    var $cep;
    var $cidade;
    var $bairro;
    var $logradouro;
    var $numero;
    var $telefone;
    var $ddd_telefone;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $ref_cod_biblioteca;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_acervo_editora=$_GET["cod_acervo_editora"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 595, $this->pessoa_logada, 11,  "educar_acervo_editora_lst.php" );

        if( is_numeric( $this->cod_acervo_editora ) )
        {

            $obj = new clsPmieducarAcervoEditora( $this->cod_acervo_editora );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

            if( $obj_permissoes->permissao_excluir( 595, $this->pessoa_logada, 11 ) )
            {
                $this->fexcluir = true;
            }

                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_acervo_editora_det.php?cod_acervo_editora={$registro["cod_acervo_editora"]}" : "educar_acervo_editora_lst.php";
        $this->nome_url_cancelar = "Cancelar";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' editora', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_acervo_editora", $this->cod_acervo_editora );

    //foreign keys
    $this->inputsHelper()->dynamic(array('instituicao', 'escola', 'biblioteca'));

        //text
        $this->campoTexto( "nm_editora", "Editora", $this->nm_editora, 30, 255, true );

        // foreign keys
        if ($this->cod_acervo_editora)
        {
            $this->cep = int2CEP($this->cep);
        }

        $this->campoCep( "cep", "CEP", $this->cep, false );

        $opcoes = array( "" => "Selecione" );

        $objTemp = new clsUf();
        $lista = $objTemp->lista();
        if ( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista as $registro )
            {
                $opcoes["{$registro['sigla_uf']}"] = "{$registro['nome']}";
            }
        }

        $this->campoLista( "ref_sigla_uf", "Estado", $opcoes, $this->ref_sigla_uf, '', false, '', '', false, false );

        $this->campoTexto( "cidade", "Cidade", $this->cidade, 30, 60, false );
        $this->campoTexto( "bairro", "Bairro", $this->bairro, 30, 60, false );

        $opcoes = array( "" => "Selecione" );

        $objTemp = new clsTipoLogradouro();
        $lista = $objTemp->lista();
        if ( is_array( $lista ) && count( $lista ) )
        {
            foreach ( $lista as $registro )
            {
                $opcoes["{$registro['idtlog']}"] = "{$registro['descricao']}";
            }
        }

        $this->campoLista( "ref_idtlog", "Tipo Logradouro", $opcoes, $this->ref_idtlog, '', false, '', '', false, false );

        $this->campoTexto( "logradouro", "Logradouro", $this->logradouro, 30, 255, false );

        $this->campoNumero( "numero", "N&uacute;mero", $this->numero, 6, 6 );

        $this->campoNumero( "ddd_telefone", "DDD Telefone", $this->ddd_telefone, 2, 2, false );
        $this->campoNumero( "telefone", "Telefone", $this->telefone, 10, 15, false );

        // data

    }

    function Novo()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 595, $this->pessoa_logada, 11,  "educar_acervo_editora_lst.php" );

        $this->cep = idFederal2int($this->cep);

        $obj = new clsPmieducarAcervoEditora( null, $this->pessoa_logada, null, $this->ref_idtlog, $this->ref_sigla_uf, $this->nm_editora, $this->cep, $this->cidade, $this->bairro, $this->logradouro, $this->numero, $this->telefone, $this->ddd_telefone, null, null, 1, $this->ref_cod_biblioteca );
        $this->cod_acervo_editora = $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
      $obj->cod_acervo_editora = $this->cod_acervo_editora;
      $acervo_editora = $obj->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("acervo_editora", $this->pessoa_logada, $this->cod_acervo_editora);
      $auditoria->inclusao($acervo_editora);
            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";

            $this->simpleRedirect('educar_acervo_editora_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 595, $this->pessoa_logada, 11,  "educar_acervo_editora_lst.php" );

        $this->cep = idFederal2int($this->cep);

        $obj = new clsPmieducarAcervoEditora($this->cod_acervo_editora, null, $this->pessoa_logada, $this->ref_idtlog, $this->ref_sigla_uf, $this->nm_editora, $this->cep, $this->cidade, $this->bairro, $this->logradouro, $this->numero, $this->telefone, $this->ddd_telefone, null, null, 1, $this->ref_cod_biblioteca);
    $detalheAntigo = $obj->detalhe();
        $editou = $obj->edita();
        if( $editou )
        {
      $detalheAtual = $obj->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("acervo_editora", $this->pessoa_logada, $this->cod_acervo_editora);
      $auditoria->alteracao($detalheAntigo, $detalheAtual);
            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_acervo_editora_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir( 595, $this->pessoa_logada, 11,  "educar_acervo_editora_lst.php" );


        $obj = new clsPmieducarAcervoEditora($this->cod_acervo_editora, null, $this->pessoa_logada, null,null,null,null,null,null,null,null,null,null,null,null, 0);
    $detalhe = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {

      $auditoria = new clsModulesAuditoriaGeral("acervo_editora", $this->pessoa_logada, $this->cod_acervo_editora);
      $auditoria->exclusao($detalhe);
            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_acervo_editora_lst.php');
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
