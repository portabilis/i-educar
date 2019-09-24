<?php

use Illuminate\Support\Facades\Session;

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Biblioteca" );
        $this->processoAp = "591";
    }
}

class indice extends clsCadastro
{
    var $cod_biblioteca;
    var $ref_cod_instituicao;
    var $ref_cod_escola;
    var $nm_biblioteca;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $biblioteca_usuario;
    var $ref_cod_usuario;
    var $incluir_usuario;
    var $excluir_usuario;

    function Inicializar()
    {
        $retorno = "Novo";

        $this->tipo_biblioteca = Session::get('biblioteca.tipo_biblioteca');

        $this->cod_biblioteca=$_GET["cod_biblioteca"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 591, $this->pessoa_logada, 3,  "educar_biblioteca_lst.php" );

        if( is_numeric( $this->cod_biblioteca ) )
        {

            $obj = new clsPmieducarBiblioteca( $this->cod_biblioteca );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                $obj_permissoes = new clsPermissoes();
                if( $obj_permissoes->permissao_excluir( 591, $this->pessoa_logada, 3 ) )
                {
                    $this->fexcluir = true;
                }
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_biblioteca_det.php?cod_biblioteca={$registro["cod_biblioteca"]}" : "educar_biblioteca_lst.php";
        $this->nome_url_cancelar = "Cancelar";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' biblioteca', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_biblioteca", $this->cod_biblioteca );

        if( $_POST )
            foreach( $_POST AS $campo => $val )
                $this->$campo = ( $this->$campo ) ? $this->$campo : $val;

        // foreign keys
        $instituicao_obrigatorio = true;
        $get_escola = true;

        $this->inputsHelper()->dynamic(array('instituicao', 'escola'));

        // text
        $this->campoTexto( "nm_biblioteca", "Biblioteca", $this->nm_biblioteca, 30, 255, true );
        /*if ($this->tombo_automatico)
            $this->campoBoolLista("tombo_automatico", "Biblioteca possui tombo automático", $this->tombo_automatico);
        else
            $this->campoBoolLista("tombo_automatico", "Biblioteca possui tombo automático", "t");*/
//      $this->campoCheck("tombo_automatico", "Biblioteca possui tombo automático", dbBool($this->tombo_automatico));

    //-----------------------INCLUI USUARIOS------------------------//
        $this->campoQuebra();

        if ( $_POST["biblioteca_usuario"] )
            $this->biblioteca_usuario = unserialize( urldecode( $_POST["biblioteca_usuario"] ) );
        if( is_numeric( $this->cod_biblioteca ) && !$_POST )
        {
            $obj = new clsPmieducarBibliotecaUsuario( $this->cod_biblioteca );
            $registros = $obj->lista( $this->cod_biblioteca );
            if( $registros )
            {
                foreach ( $registros AS $campo )
                {
                    $this->biblioteca_usuario["ref_cod_usuario_"][] = $campo["ref_cod_usuario"];
                }
            }
        }
        if ( $_POST["ref_cod_usuario"] )
        {
            $this->biblioteca_usuario["ref_cod_usuario_"][] = $_POST["ref_cod_usuario"];
            unset( $this->ref_cod_usuario );
        }

        $this->campoOculto( "excluir_usuario", "" );
        unset($aux);

        if ( $this->biblioteca_usuario )
        {
            foreach ( $this->biblioteca_usuario as $key => $campo )
            {
                if($campo)
                {
                    foreach ($campo as $chave => $usuarios)
                    {
                        if ( $this->excluir_usuario == $usuarios )
                        {
                            $this->biblioteca_usuario[$chave] = null;
                            $this->excluir_usuario = null;
                        }
                        else
                        {
                            $obj_cod_usuario = new clsPessoa_( $usuarios );
                            $obj_usuario_det = $obj_cod_usuario->detalhe();
                            $nome_usuario = $obj_usuario_det['nome'];
                            $this->campoTextoInv( "ref_cod_usuario_{$usuarios}", "", $nome_usuario, 30, 255, false, false, false, "", "<a href='#' onclick=\"getElementById('excluir_usuario').value = '{$usuarios}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>" );
                            $aux["ref_cod_usuario_"][] = $usuarios;
                        }
                    }
                }
            }
            unset($this->biblioteca_usuario);
            $this->biblioteca_usuario = $aux;
        }

        $this->campoOculto( "biblioteca_usuario", serialize( $this->biblioteca_usuario ) );


        $opcoes = array( "" => "Selecione" );
        if ($this->ref_cod_instituicao)
        {
            $objTemp = new clsPmieducarUsuario();
            $objTemp->setOrderby("nivel ASC");
            $lista = $objTemp->lista(null,null,$this->ref_cod_instituicao,null,null,null,null,null,null,null,1);
            if ( is_array( $lista ) && count( $lista ) )
            {
                foreach ( $lista as $registro )
                {
                    $obj_cod_usuario = new clsPessoa_($registro["cod_usuario"] );
                    $obj_usuario_det = $obj_cod_usuario->detalhe();
                    $nome_usuario = $obj_usuario_det['nome'];
                    $opcoes["{$registro['cod_usuario']}"] = "{$nome_usuario}";
                }
            }
        }

        $this->campoLista( "ref_cod_usuario", "Usu&aacute;rio", $opcoes, $this->ref_cod_usuario,"",false,"","<a href='#' onclick=\"getElementById('incluir_usuario').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>",false,false);

        $this->campoOculto( "incluir_usuario", "" );

        $this->campoQuebra();
    }

    function Novo()
    {

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 591, $this->pessoa_logada, 3,  "educar_biblioteca_lst.php" );
        /*if ($this->tombo_automatico == "on")
            $this->tombo_automatico = "TRUE";
        else
            $this->tombo_automatico = "FALSE";*/
        $obj = new clsPmieducarBiblioteca( null, $this->ref_cod_instituicao, $this->ref_cod_escola, $this->nm_biblioteca, null, null, null, null, null, null, 1, null);
        $this->cod_biblioteca = $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
      $obj->cod_biblioteca = $this->cod_biblioteca;
      $biblioteca = $obj->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("biblioteca", $this->pessoa_logada, $this->cod_biblioteca);
      $auditoria->inclusao($biblioteca);
          //-----------------------CADASTRA USUARIOS------------------------//
            $this->biblioteca_usuario = unserialize( urldecode( $this->biblioteca_usuario ) );
            if ($this->biblioteca_usuario)
            {
                foreach ( $this->biblioteca_usuario AS $campo )
                {
                    for ($i = 0; $i < sizeof($campo) ; $i++)
                    {
                        $obj = new clsPmieducarBibliotecaUsuario( $cadastrou, $campo[$i] );
                        $cadastrou2  = $obj->cadastra();
                        if ( !$cadastrou2 )
                        {
                            $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

                            return false;
                        }
                    }
                }
            }
        //-----------------------FIM CADASTRA USUARIOS------------------------//

            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_biblioteca_lst.php');
            $this->simpleRedirect('educar_biblioteca_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 591, $this->pessoa_logada, 3,  "educar_biblioteca_lst.php" );
        $obj = new clsPmieducarBiblioteca($this->cod_biblioteca, $this->ref_cod_instituicao, $this->ref_cod_escola, $this->nm_biblioteca, null, null, null, null, null, null, 1, null);
    $detalheAntigo = $obj->detalhe();
        $editou = $obj->edita();
        if( $editou )
        {
      $detalheAtual = $obj->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("biblioteca", $this->pessoa_logada, $this->cod_biblioteca);
      $auditoria->alteracao($detalheAntigo, $detalheAtual);

        //-----------------------EDITA USUARIOS------------------------//
            $this->biblioteca_usuario = unserialize( urldecode( $this->biblioteca_usuario ) );
            $obj  = new clsPmieducarBibliotecaUsuario( $this->cod_biblioteca );
            $excluiu = $obj->excluirTodos();
            if ( $excluiu )
            {
                if ($this->biblioteca_usuario)
                {
                    foreach ( $this->biblioteca_usuario AS $campo )
                    {
                        for ($i = 0; $i < sizeof($campo) ; $i++)
                        {
                            $obj = new clsPmieducarBibliotecaUsuario( $this->cod_biblioteca, $campo[$i]);
                            $cadastrou3  = $obj->cadastra();
                            if ( !$cadastrou3 )
                            {
                                $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

                                return false;
                            }
                        }
                    }
                }
            }
        //-----------------------FIM EDITA USUARIOS------------------------//

            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_biblioteca_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir( 591, $this->pessoa_logada, 3,  "educar_biblioteca_lst.php" );


        $obj = new clsPmieducarBiblioteca($this->cod_biblioteca, null,null,null,null,null,null,null,null,null, 0);
        $detalhe = $obj->detalhe();
    $excluiu = $obj->excluir();
        if( $excluiu )
        {
      $auditoria = new clsModulesAuditoriaGeral("biblioteca", $this->pessoa_logada, $this->cod_biblioteca);
      $auditoria->exclusao($detalhe);
            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_biblioteca_lst.php');
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
/*
function getUsuarios(selecao)
{
    var campoUsuario = document.getElementById('ref_cod_usuario');

    campoUsuario.length = 1;
    if (selecao == 1)
    {
        var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

        for (var j = 0; j < user_escola.length; j++)
        {
            if (user_escola[j][2] == campoInstituicao)
            {
                campoUsuario.options[campoUsuario.options.length] = new Option( user_escola[j][1], user_escola[j][0],false,false);
            }
        }
        for (var j = 0; j < user_biblioteca.length; j++)
        {
            if (user_biblioteca[j][2] == campoInstituicao)
            {
                campoUsuario.options[campoUsuario.options.length] = new Option( user_biblioteca[j][1], user_biblioteca[j][0],false,false);
            }
        }
    }
    else if (selecao == 2)
    {
        var campoEscola = document.getElementById('ref_cod_escola').value;

        for (var j = 0; j < user_escola.length; j++)
        {
            if (user_escola[j][3] == campoEscola)
            {
                campoUsuario.options[campoUsuario.options.length] = new Option( user_escola[j][1], user_escola[j][0],false,false);
            }
        }
        for (var j = 0; j < user_biblioteca.length; j++)
        {
            if (user_biblioteca[j][3] == campoEscola)
            {
                campoUsuario.options[campoUsuario.options.length] = new Option( user_biblioteca[j][1], user_biblioteca[j][0],false,false);
            }
        }
    }
}
*/
function getUsuario(xml_usuario)
{
    var campoUsuario = document.getElementById('ref_cod_usuario');
    var DOM_array = xml_usuario.getElementsByTagName( "usuario" );

    if(DOM_array.length)
    {
        campoUsuario.length = 1;
        campoUsuario.options[0].text = 'Selecione um usuário';
        campoUsuario.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoUsuario.options[campoUsuario.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_usuario"),false,false);
        }
    }
    else
        campoUsuario.options[0].text = 'A instituição não possui nenhum usuário';
}

/*
document.getElementById('ref_cod_instituicao').onchange = function()
{
    getUsuarios();
}
*/
/*
before_getEscola = function()
{
    getUsuarios(1);
}
*/
//document.getElementById('ref_cod_instituicao').onchange = function()
before_getEscola = function()
{
//  getUsuarios(1);
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

    var campoUsuario = document.getElementById('ref_cod_usuario');
    campoUsuario.length = 1;
    campoUsuario.disabled = true;
    campoUsuario.options[0].text = 'Carregando usuário';

    var xml_usuario = new ajax( getUsuario );
    xml_usuario.envia( "educar_usuario_xml.php?ins="+campoInstituicao );
}

document.getElementById('ref_cod_escola').onchange = function()
{
//  getUsuarios(2);
    var campoEscola = document.getElementById('ref_cod_escola').value;

    var campoUsuario = document.getElementById('ref_cod_usuario');
    campoUsuario.length = 1;
    campoUsuario.disabled = true;
    campoUsuario.options[0].text = 'Carregando usuário';

    var xml_usuario = new ajax( getUsuario );
    xml_usuario.envia( "educar_usuario_xml.php?esc="+campoEscola );
}
/*
after_getEscola = function()
{
    getUsuarios(2);
}
*/
</script>
