<?php


use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Sequ&ecirc;ncia Enturma&ccedil;&atilde;o" );
        $this->processoAp = "587";
    }
}

$nivel_usuario_fora = 0;

class indice extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

    var $ref_serie_origem;
    var $ref_serie_destino;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    var $ref_cod_instituicao;
    var $ref_curso_origem;
    var $ref_curso_destino;

    var $serie_origem_old;
    var $serie_destino_old;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->ref_serie_origem=$_GET["ref_serie_origem"];
        $this->ref_serie_destino=$_GET["ref_serie_destino"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 587, $this->pessoa_logada, 3,  "educar_sequencia_serie_lst.php" );

        if( is_numeric( $this->ref_serie_origem ) && is_numeric( $this->ref_serie_destino ) )
        {

            $obj = new clsPmieducarSequenciaSerie( $this->ref_serie_origem, $this->ref_serie_destino );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                $obj_ref_serie_origem = new clsPmieducarSerie( $this->ref_serie_origem );
                $det_ref_serie_origem = $obj_ref_serie_origem->detalhe();
                $this->ref_curso_origem = $det_ref_serie_origem["ref_cod_curso"];
                    $obj_ref_curso_origem = new clsPmieducarCurso( $this->ref_curso_origem );
                    $det_ref_curso_origem = $obj_ref_curso_origem->detalhe();
                    $this->ref_cod_instituicao = $det_ref_curso_origem["ref_cod_instituicao"];
                $obj_ref_serie_destino = new clsPmieducarSerie( $this->ref_serie_destino );
                $det_ref_serie_destino = $obj_ref_serie_destino->detalhe();
                $this->ref_curso_destino = $det_ref_serie_destino["ref_cod_curso"];

                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                if( $obj_permissoes->permissao_excluir( 587, $this->pessoa_logada, 3 ) )
                {
                    $this->fexcluir = true;
                }

                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_sequencia_serie_det.php?ref_serie_origem={$registro["ref_serie_origem"]}&ref_serie_destino={$registro["ref_serie_destino"]}" : "educar_sequencia_serie_lst.php";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' sequência de enturmação', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = "Cancelar";

        return $retorno;
    }

    function Gerar()
    {
        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

        $this->campoOculto( "serie_origem_old", $this->ref_serie_origem );
        $this->campoOculto( "serie_destino_old", $this->ref_serie_destino );
        // foreign keys
        if( $nivel_usuario == 1 )
        {
            $GLOBALS["nivel_usuario_fora"] = 1;
            $objInstituicao = new clsPmieducarInstituicao();
            $opcoes = array( "" => "Selecione" );
            $objInstituicao->setOrderby( "nm_instituicao ASC" );
            $lista = $objInstituicao->lista();
            if( is_array( $lista ) )
            {
                foreach ( $lista AS $linha )
                {
                    $opcoes[$linha["cod_instituicao"]] = $linha["nm_instituicao"];
                }
            }
            $this->campoLista( "ref_cod_instituicao", "Institui&ccedil;&atilde;o", $opcoes, $this->ref_cod_instituicao );
        }
        else
        {
            $obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
            $obj_usuario_det = $obj_usuario->detalhe();
            $this->ref_cod_instituicao = $obj_usuario_det["ref_cod_instituicao"];
        }


        $opcoes = array( "" => "Selecione" );
        $opcoes_ = array( "" => "Selecione" );

            // EDITAR
            if ($this->ref_cod_instituicao)
            {
                $objTemp = new clsPmieducarCurso();
                $objTemp->setOrderby("nm_curso");
                $lista = $objTemp->lista( null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,1,null,$this->ref_cod_instituicao );
                if ( is_array( $lista ) && count( $lista ) )
                {
                    foreach ( $lista as $registro )
                    {
                        $opcoes[$registro["cod_curso"]] = $registro["nm_curso"];
                        $opcoes_[$registro["cod_curso"]] = $registro["nm_curso"];
                    }
                }
            }

        $this->campoLista( "ref_curso_origem", "Curso Origem", $opcoes, $this->ref_curso_origem,"",true );
        $this->campoLista( "ref_curso_destino", " Curso Destino", $opcoes_, $this->ref_curso_destino );

        // primary keys

        $opcoes = array( "" => "Selecione" );
        $opcoes_ = array( "" => "Selecione" );

            if ($this->ref_curso_origem)
            {
                $objTemp = new clsPmieducarSerie();
                $objTemp->setOrderby( "nm_serie ASC" );
                $lista = $objTemp->lista( null,null,null,$this->ref_curso_origem,null,null,null,null,null,null,null,null,1 );
                if ( is_array( $lista ) && count( $lista ) )
                {
                    foreach ( $lista as $registro )
                    {
                        $opcoes[$registro["cod_serie"]] = $registro["nm_serie"];
                    }
                }
            }
            if ($this->ref_curso_destino)
            {
                $objTemp = new clsPmieducarSerie();
                $objTemp->setOrderby( "nm_serie ASC" );
                $lista = $objTemp->lista( null,null,null,$this->ref_curso_destino,null,null,null,null,null,null,null,null,1 );
                if ( is_array( $lista ) && count( $lista ) )
                {
                    foreach ( $lista as $registro )
                    {
                        $opcoes_[$registro["cod_serie"]] = $registro["nm_serie"];
                    }
                }
            }

        $this->campoLista( "ref_serie_origem", "S&eacute;rie Origem", $opcoes, $this->ref_serie_origem,null,true);
        $this->campoLista( "ref_serie_destino", " S&eacute;rie Destino", $opcoes_, $this->ref_serie_destino);


        $this->campoOculto("nivel_usuario", $nivel_usuario);

    }

    function Novo()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 587, $this->pessoa_logada, 3,  "educar_sequencia_serie_lst.php" );

        $obj_sequencia = new clsPmieducarSequenciaSerie( $this->ref_serie_origem, $this->ref_serie_destino );
        $det_sequencia = $obj_sequencia->detalhe();
        if (!$det_sequencia)
        {
            $obj = new clsPmieducarSequenciaSerie( $this->ref_serie_origem, $this->ref_serie_destino, null, $this->pessoa_logada, null, null, 1 );
            $cadastrou = $obj->cadastra();
            if( $cadastrou )
            {
                $this->mensagem .= "Cadastro efetuado com sucesso.<br>";

                throw new HttpResponseException(
                    new RedirectResponse('educar_sequencia_serie_lst.php')
                );
            }
        }
        else
        {
            $obj = new clsPmieducarSequenciaSerie($this->ref_serie_origem, $this->ref_serie_destino, $this->pessoa_logada, null, null, null, 1);
            $editou = $obj->edita();
            if( $editou )
            {
                $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";

                throw new HttpResponseException(
                    new RedirectResponse('educar_sequencia_serie_lst.php')
                );
            }
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 587, $this->pessoa_logada, 3,  "educar_sequencia_serie_lst.php" );

//echo "clsPmieducarSequenciaSerie($this->ref_serie_origem, $this->ref_serie_destino, $this->pessoa_logada, null, null, null, 1);";
        $obj = new clsPmieducarSequenciaSerie($this->ref_serie_origem, $this->ref_serie_destino, $this->pessoa_logada, null, null, null, 1);
        $existe = $obj->existe();
        if (!$existe)
        {
            $editou = $obj->editar( $this->serie_origem_old, $this->serie_destino_old );
            if( $editou )
            {
                $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";

                throw new HttpResponseException(
                    new RedirectResponse('educar_sequencia_serie_lst.php')
                );
            }
            $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

            return false;
        }
        echo "<script> alert('Edição não realizada! \\n Já existe essa sequência.') </script>";
        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
        return false;
    }

    function Excluir()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir( 587, $this->pessoa_logada, 3,  "educar_sequencia_serie_lst.php" );


        $obj = new clsPmieducarSequenciaSerie($this->ref_serie_origem, $this->ref_serie_destino, $this->pessoa_logada, null, null, null, 0);
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";

            throw new HttpResponseException(
                new RedirectResponse('educar_sequencia_serie_lst.php')
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

<script>

function getCurso(xml_curso)
{
    /*
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
    var campoCurso = document.getElementById('ref_curso_origem');
    var campoCurso_ = document.getElementById('ref_curso_destino');

    campoCurso.length = 1;
    campoCurso_.length = 1;
    for (var j = 0; j < curso.length; j++)
    {
        if (curso[j][2] == campoInstituicao)
        {
            campoCurso.options[campoCurso.options.length] = new Option( curso[j][1], curso[j][0],false,false);
            campoCurso_.options[campoCurso_.options.length] = new Option( curso[j][1], curso[j][0],false,false);
        }
    }
    */
    var campoCurso = document.getElementById('ref_curso_origem');
    var campoCurso_ = document.getElementById('ref_curso_destino');
    var DOM_array = xml_curso.getElementsByTagName( "curso" );

    if(DOM_array.length)
    {
        campoCurso.length = 1;
        campoCurso.options[0].text = 'Selecione um curso origem';
        campoCurso.disabled = false;

        campoCurso_.length = 1;
        campoCurso_.options[0].text = 'Selecione um curso destino';
        campoCurso_.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoCurso.options[campoCurso.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_curso"),false,false);
            campoCurso_.options[campoCurso_.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_curso"),false,false);
        }
    }
    else
    {
        campoCurso.options[0].text = 'A instituição não possui nenhum curso';
        campoCurso_.options[0].text = 'A instituição não possui nenhum curso';
    }
}

function getSerie(xml_serie)
{
    var campoSerie = document.getElementById('ref_serie_origem');
    var DOM_array = xml_serie.getElementsByTagName( "serie" );

    if(DOM_array.length)
    {
        campoSerie.length = 1;
        campoSerie.options[0].text = 'Selecione uma série origem';
        campoSerie.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoSerie.options[campoSerie.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_serie"),false,false);
        }
    }
    else
        campoSerie.options[0].text = 'O curso origem não possui nenhuma série';
}

function getSerie_(xml_serie_)
{
    var campoSerie_ = document.getElementById('ref_serie_destino');
    var DOM_array = xml_serie_.getElementsByTagName( "serie" );

    if(DOM_array.length)
    {
        campoSerie_.length = 1;
        campoSerie_.options[0].text = 'Selecione uma série destino';
        campoSerie_.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoSerie_.options[campoSerie_.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_serie"),false,false);
        }
    }
    else
        campoSerie_.options[0].text = 'O curso origem não possui nenhuma série';
}
/*
function getSerie( tipo )
{
    var campoCurso = document.getElementById('ref_curso_origem').value;
    var campoCurso_ = document.getElementById('ref_curso_destino').value;
    var campoSerie = document.getElementById('ref_serie_origem');
    var campoSerie_ = document.getElementById('ref_serie_destino');


    if (tipo == 1)
    {
        campoSerie.length = 1;
    }
    else if (tipo == 2)
    {
        campoSerie_.length = 1;
    }

    for (var j = 0; j < serie.length; j++)
    {
        if (tipo == 1)
        {
            if (serie[j][2] == campoCurso)
            {
                campoSerie.options[campoSerie.options.length] = new Option( serie[j][1], serie[j][0],false,false);
            }
        }
        else if (tipo == 2)
        {
            if (serie[j][2] == campoCurso_)
            {
                campoSerie_.options[campoSerie_.options.length] = new Option( serie[j][1], serie[j][0],false,false);
            }
        }
    }
}
*/


/*window.onload = function() {
    var nivel_usuario = $F('nivel_usuario');
    if (nivel_usuario == 1)
    {
        var campo_inst = $('ref_cod_instituicao');
        campo_inst.setAttribute('onchange', mudaInstituicao());
    }
}*/

/*function mudaInstituicao()
{
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

    var campoCurso = document.getElementById('ref_curso_origem');
    campoCurso.length = 1;
    campoCurso.disabled = true;
    campoCurso.options[0].text = 'Carregando curso origem';

    var campoCurso_ = document.getElementById('ref_curso_destino');
    campoCurso_.length = 1;
    campoCurso_.disabled = true;
    campoCurso_.options[0].text = 'Carregando curso destino';

    var xml_curso = new ajax( getCurso );
    xml_curso.envia( "educar_curso_xml2.php?ins="+campoInstituicao );

    $('img_serie_origem').style.display = 'none;';
    $('img_serie_destino').style.display = 'none;';
}*/

document.getElementById('ref_cod_instituicao').onchange = function()
{
    var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

    var campoCurso = document.getElementById('ref_curso_origem');
    campoCurso.length = 1;
    campoCurso.disabled = true;
    campoCurso.options[0].text = 'Carregando curso origem';

    var campoCurso_ = document.getElementById('ref_curso_destino');
    campoCurso_.length = 1;
    campoCurso_.disabled = true;
    campoCurso_.options[0].text = 'Carregando curso destino';

    var xml_curso = new ajax( getCurso );
    xml_curso.envia( "educar_curso_xml2.php?ins="+campoInstituicao );

};

document.getElementById('ref_curso_origem').onchange = function()
{
    var campoCurso = document.getElementById('ref_curso_origem').value;

    var campoSerie = document.getElementById('ref_serie_origem');
    campoSerie.length = 1;
    campoSerie.disabled = true;
    campoSerie.options[0].text = 'Carregando série origem';

    var xml_serie = new ajax( getSerie );
    xml_serie.envia( "educar_serie_xml.php?cur="+campoCurso )

};

document.getElementById('ref_curso_destino').onchange = function()
{
    var campoCurso_ = document.getElementById('ref_curso_destino').value;

    var campoSerie_ = document.getElementById('ref_serie_destino');
    campoSerie_.length = 1;
    campoSerie_.disabled = true;
    campoSerie_.options[0].text = 'Carregando série destino';

    var xml_serie_ = new ajax( getSerie_ );
    xml_serie_.envia( "educar_serie_xml.php?cur="+campoCurso_ )

};

</script>
