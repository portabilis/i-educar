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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Exemplar" );
        $this->processoAp = "597";
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

    var $cod_exemplar_tipo;
    var $ref_cod_biblioteca;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $nm_tipo;
    var $descricao;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    var $ref_cod_instituicao;
    var $ref_cod_escola;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_exemplar_tipo=$_GET["cod_exemplar_tipo"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 597, $this->pessoa_logada, 11,  "educar_exemplar_tipo_lst.php" );

        if( is_numeric( $this->cod_exemplar_tipo ) )
        {

            $obj = new clsPmieducarExemplarTipo( $this->cod_exemplar_tipo );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                if ($this->cod_exemplar_tipo)
                {
                    $obj_biblioteca = new clsPmieducarBiblioteca($this->ref_cod_biblioteca);
                    $det_biblioteca = $obj_biblioteca->detalhe();
                    $this->ref_cod_instituicao = $det_biblioteca["ref_cod_instituicao"];
                    $this->ref_cod_escola = $det_biblioteca["ref_cod_escola"];
                }

                if( $obj_permissoes->permissao_excluir( 597, $this->pessoa_logada, 11 ) )
                {
                    $this->fexcluir = true;
                }
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_exemplar_tipo_det.php?cod_exemplar_tipo={$registro["cod_exemplar_tipo"]}" : "educar_exemplar_tipo_lst.php";
        $this->nome_url_cancelar = "Cancelar";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' tipo de exemplar', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_exemplar_tipo", $this->cod_exemplar_tipo );

        if ($this->cod_exemplar_tipo)
        {
            $instituicao_desabilitado = true;
            $escola_desabilitado = true;
            $biblioteca_desabilitado = true;
        }

        // foreign keys
        $get_escola     = 1;
        $escola_obrigatorio = false;
        $get_biblioteca = 1;
        $instituicao_obrigatorio = true;
        $biblioteca_obrigatorio = true;
        include("include/pmieducar/educar_campo_lista.php");

        // text
        $this->campoTexto( "nm_tipo", "Tipo Exemplar", $this->nm_tipo, 30, 255, true );
        $this->campoMemo( "descricao", "Descri&ccedil;&atilde;o", $this->descricao, 60, 5, false );

        //-----------------------INICIO CLIENTE TIPO------------------------//

        $opcoes = array( "" => "Selecione" );
        $todos_tipos_clientes .= "var editar_ = 0;\n";
        if($_GET['cod_exemplar_tipo'])
        {
            $todos_tipos_clientes .= "editar_ = {$_GET['cod_exemplar_tipo']};\n";
        }

        echo "<script>{$todos_tipos_clientes}{$script}</script>";

        // se o caso é EDITAR
        if ($this->ref_cod_biblioteca)
        {
            $objTemp = new clsPmieducarClienteTipo();
            $objTemp->setOrderby("nm_tipo ASC");
            $lista = $objTemp->lista(null,$this->ref_cod_biblioteca,null,null,null,null,null,null,null,null,1);
            if ( is_array( $lista ) && count( $lista ) )
            {
                foreach ( $lista as $registro )
                {
                    $opcoes["{$registro['cod_cliente_tipo']}"] = "{$registro['nm_tipo']}";
                }
            }
        }

        $this->campoRotulo( "div_clientes", "Tipo Cliente", "<div id='clientes'></div>" );
        $this->acao_enviar = "Valida();";
        //-----------------------FIM CLIENTE TIPO------------------------
    }

    function Novo()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 597, $this->pessoa_logada, 11,  "educar_exemplar_tipo_lst.php" );

        $array_tipos = array();
        foreach ( $_POST AS $key => $cliente_tipo )
        {
            if(substr($key, 0, 5) == "tipo_")
            {
                $array_tipos[substr($key, 5)] = $cliente_tipo;
            }
        }

        $obj = new clsPmieducarExemplarTipo( null, $this->ref_cod_biblioteca, null, $this->pessoa_logada, $this->nm_tipo, $this->descricao, null, null, 1 );
        $this->cod_exemplar_tipo = $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
      $obj->cod_exemplar_tipo = $this->cod_exemplar_tipo;
      $exemplar_tipo = $obj->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("exemplar_tipo", $this->pessoa_logada, $this->cod_exemplar_tipo);
      $auditoria->inclusao($exemplar_tipo);

        //-----------------------CADASTRA CLIENTE TIPO------------------------//
            if ($array_tipos)
            {
                foreach ( $array_tipos AS $cliente_tipo => $dias_emprestimo )
                {
                    $obj = new clsPmieducarClienteTipoExemplarTipo( $cliente_tipo, $cadastrou, $dias_emprestimo );
                    $cadastrou2  = $obj->cadastra();
                    if ( !$cadastrou2 )
                    {
                        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

                        return false;
                    }
                }
            }
        //-----------------------FIM CADASTRA CLIENTE TIPO------------------------//

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
             $this->simpleRedirect('educar_exemplar_tipo_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 597, $this->pessoa_logada, 11,  "educar_exemplar_tipo_lst.php" );

        $array_tipos = array();
        foreach ( $_POST AS $key => $cliente_tipo )
        {
            if(substr($key, 0, 5) == "tipo_")
            {
                $array_tipos[substr($key, 5)] = $cliente_tipo;
            }
        }

        $obj = new clsPmieducarExemplarTipo($this->cod_exemplar_tipo, $this->ref_cod_biblioteca, $this->pessoa_logada, null, $this->nm_tipo, $this->descricao, null, null, 1);
        $detalheAntigo = $obj->detalhe();
    $editou = $obj->edita();
        if( $editou )
        {
      $detalheAtual = $obj->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("exemplar_tipo", $this->pessoa_logada, $this->cod_exemplar_tipo);
      $auditoria->alteracao($detalheAntigo, $detalheAtual);

        //-----------------------EDITA CLIENTE TIPO------------------------//
            if ($array_tipos)
            {
                foreach ( $array_tipos AS $cliente_tipo => $dias_emprestimo )
                {
                    $obj = new clsPmieducarClienteTipoExemplarTipo( $cliente_tipo, $this->cod_exemplar_tipo, $dias_emprestimo );
                    $editou2  = $obj->edita();
                    if ( !$editou2 )
                    {
                        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

                        return false;
                    }
                }
            }
        //-----------------------FIM EDITA CLIENTE TIPO------------------------//

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
             $this->simpleRedirect('educar_exemplar_tipo_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir( 597, $this->pessoa_logada, 11,  "educar_exemplar_tipo_lst.php" );


        $obj = new clsPmieducarExemplarTipo($this->cod_exemplar_tipo, null, $this->pessoa_logada, null, null, null, null, null, 0);
    $detalhe = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
      $auditoria = new clsModulesAuditoriaGeral("exemplar_tipo", $this->pessoa_logada, $this->cod_exemplar_tipo);
      $auditoria->exclusao($detalhe);
            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
             $this->simpleRedirect('educar_exemplar_tipo_lst.php');
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

<script>

var scriptValida = "";
var retorno = 1;
var divClientes = document.getElementById( "tr_div_clientes" );
setVisibility ('tr_div_clientes', false);

function getClienteTipo()
{
    var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;
  var exemplarTipoId  = document.getElementById('cod_exemplar_tipo').value;

    var xml1 = new ajax(getClienteTipo_XML);

    strURL = "educar_cliente_tipo_xml.php?bib="+campoBiblioteca+"&exemplar_tipo_id="+exemplarTipoId;
    xml1.envia(strURL);
}

function getClienteTipo_XML(xml)
{
    var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;
    var clientes = document.getElementById('clientes');
    var nm_tipo_cliente = document.createElement( "input" );
    var span = document.createElement( "span" );
    var dias_tipo_cliente = document.createElement( "input" );
    var br = document.createElement( "br" );
    var tipos = false;

    clientes.innerHTML = "";
    scriptValida = "";

    var tipo_cliente = xml.getElementsByTagName( "cliente_tipo" );

    var aux = clientes.innerHTML;

    if(tipo_cliente.length)
        setVisibility ('tr_div_clientes', true);

    for (var j = 0; j < tipo_cliente.length; j++)
    {
//      if (tipo_cliente[j][2] == campoBiblioteca)
//      {
//          setVisibility ('tr_div_clientes', true);
            tipos = true;
            clientes.appendChild(nm_tipo_cliente);
            clientes.appendChild(span);
            clientes.appendChild(dias_tipo_cliente);
            clientes.appendChild(br);
            span.innerHTML = "Dias de Empréstimo";
            span.setAttribute( "class", "dias" );
            nm_tipo_cliente.setAttribute( "id", "teste"+j );
            nm_tipo_cliente.setAttribute( 'type', 'text' );
            nm_tipo_cliente.setAttribute( 'disabled', 'true' );
            nm_tipo_cliente.setAttribute( 'class', 'obrigatorio' );
            nm_tipo_cliente.setAttribute( 'style', 'margin: 2px;' );
//          nm_tipo_cliente.setAttribute( 'value', tipo_cliente[j][1] );
            nm_tipo_cliente.setAttribute( 'value', tipo_cliente[j].firstChild.data );

            dias_tipo_cliente.setAttribute( "id", "tipo_"+tipo_cliente[j].getAttribute("cod_cliente_tipo") );
            dias_tipo_cliente.setAttribute( 'type', 'text' );
            dias_tipo_cliente.setAttribute( 'size', '3' );
            dias_tipo_cliente.setAttribute( 'autocomplete', 'off' );
            dias_tipo_cliente.setAttribute( 'style', 'margin: 2px;' );
            dias_tipo_cliente.setAttribute( 'maxlength', '3' );
            if(tipo_cliente[j].getAttribute("dias_emprestimo"))
                dias_tipo_cliente.setAttribute( 'value', tipo_cliente[j].getAttribute("dias_emprestimo") );
            dias_tipo_cliente.setAttribute( 'class', 'obrigatorio' );

            clientes.innerHTML += aux;

            scriptValida += "if (!(/[^ ]/.test( document.getElementById('tipo_"+tipo_cliente[j].getAttribute("cod_cliente_tipo")+"').value )) || !((/^[0-9]+$/).test( document.getElementById('tipo_"+tipo_cliente[j].getAttribute("cod_cliente_tipo")+"').value )))\n";
            scriptValida += "{\n";
            scriptValida += "retorno = 0;\n";
            scriptValida += "mudaClassName( 'formdestaque', 'formlttd' );\n";
            scriptValida += "document.getElementById('tipo_"+tipo_cliente[j].getAttribute("cod_cliente_tipo")+"').className = \"formdestaque\";\n";
            scriptValida += "alert( 'Preencha o campo \""+tipo_cliente[j].firstChild.data+"\" corretamente!' );\n";
            scriptValida += "document.getElementById('tipo_"+tipo_cliente[j].getAttribute("cod_cliente_tipo")+"').focus();\n";
            scriptValida += "}\n\n";
            document.getElementById("tipo_"+tipo_cliente[j].getAttribute("cod_cliente_tipo")).name = dias_tipo_cliente.id;
//      }
    }
    if(!tipos)
    {
        setVisibility ('tr_div_clientes', false);
    }
}

function Valida()
{
    eval(scriptValida);
    if (retorno == 0)
    {
        retorno = 1;
        return false;
    }
    acao();
}

if(document.getElementById('ref_cod_biblioteca').type == 'hidden')
{
    getClienteTipo();

}
else
{
    document.getElementById('ref_cod_biblioteca').onchange = function()
    {
        getClienteTipo();
    }

}

if(editar_)
{
    getClienteTipo();
}

</script>
<style>
.dias
{
    padding: 6px;
}
</style>
