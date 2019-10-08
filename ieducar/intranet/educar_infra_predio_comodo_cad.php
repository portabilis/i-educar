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
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Ambiente" );
        $this->processoAp = "574";
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

    var $cod_infra_predio_comodo;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $ref_cod_infra_comodo_funcao;
    var $ref_cod_infra_predio;
    var $nm_comodo;
    var $desc_comodo;
    var $area;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;

    var $ref_cod_escola;
    var $ref_cod_instituicao;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_infra_predio_comodo=$_GET["cod_infra_predio_comodo"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 574, $this->pessoa_logada,7, "educar_infra_predio_comodo_lst.php" );

        if( is_numeric( $this->cod_infra_predio_comodo ) )
        {

            $obj = new clsPmieducarInfraPredioComodo( $this->cod_infra_predio_comodo );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                $obj_infra_comodo = new clsPmieducarInfraPredio($registro["ref_cod_infra_predio"]);
                $det_comodo = $obj_infra_comodo->detalhe();
                $registro["ref_cod_escola"] = $det_comodo["ref_cod_escola"];

                $obj_escola = new clsPmieducarEscola($det_comodo["ref_cod_escola"]);
                $det_escola = $obj_escola->detalhe();
                $registro["ref_cod_instituicao"] = $det_escola["ref_cod_instituicao"];
                //echo "<pre>";print_r($registro);die;
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                $this->fexcluir = true;
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_infra_predio_comodo_det.php?cod_infra_predio_comodo={$registro["cod_infra_predio_comodo"]}" : "educar_infra_predio_comodo_lst.php";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' ambiente', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = "Cancelar";

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_infra_predio_comodo", $this->cod_infra_predio_comodo );

        $obrigatorio = true;
        $get_escola  = true;
        $this->inputsHelper()->dynamic(array('instituicao','escola'));

        $opcoes_predio = array( "" => "Selecione" );

        // EDITAR
        if ($this->ref_cod_escola)
        {
            $objTemp = new clsPmieducarInfraPredio();
            $lista = $objTemp->lista( null,null,null,$this->ref_cod_escola,null,null,null,null,null,null,null,1 );
            if ( is_array( $lista ) && count( $lista ) )
            {
                foreach ( $lista as $registro )
                {
                    $opcoes_predio["{$registro['cod_infra_predio']}"] = "{$registro['nm_predio']}";
                }
            }
        }

        $script = "javascript:showExpansivelIframe(520, 400, 'educar_infra_predio_cad_pop.php');";
        if ($this->ref_cod_escola && $this->ref_cod_instituicao)
        {
            $script = "<img id='img_colecao' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        }
        else
        {
            $script = "<img id='img_colecao' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";

        }
        $this->campoLista( "ref_cod_infra_predio", "Pr&eacute;dio", $opcoes_predio, $this->ref_cod_infra_predio, "", false, "", $script );


        $opcoes_funcao = array( "" => "Selecione" );

        // EDITAR
        if ($this->ref_cod_escola)
        {
            $objTemp = new clsPmieducarInfraComodoFuncao();
            $lista = $objTemp->lista( null,null,null,null,null,null,null,null,null,1,$this->ref_cod_escola );
            if ( is_array( $lista ) && count( $lista ) )
            {
                foreach ( $lista as $registro )
                {
                    $opcoes_funcao["{$registro['cod_infra_comodo_funcao']}"] = "{$registro['nm_funcao']}";
                }
            }
        }

        $script = "javascript:showExpansivelIframe(520, 250, 'educar_infra_comodo_funcao_cad_pop.php');";
        if ($this->ref_cod_escola && $this->ref_cod_instituicao)
        {
            $script = "<img id='img_colecao2' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        }
        else
        {
            $script = "<img id='img_colecao2' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        }
        $this->campoLista( "ref_cod_infra_comodo_funcao", "Tipo de ambiente", $opcoes_funcao, $this->ref_cod_infra_comodo_funcao,"", false, "", $script );



        // text
        $this->campoTexto( "nm_comodo", "Ambiente", $this->nm_comodo, 43, 255, true );
        $this->campoMonetario("area", "&Aacute;rea m²", $this->area, 10, 255, true );
        $this->campoMemo( "desc_comodo", "Descrição do ambiente", $this->desc_comodo, 60, 5, false );
    }

    function Novo()
    {

        $this->area = str_replace(".","",$this->area);
        $this->area = str_replace(",",".",$this->area);
        $obj = new clsPmieducarInfraPredioComodo( null, null, $this->pessoa_logada, $this->ref_cod_infra_comodo_funcao, $this->ref_cod_infra_predio, $this->nm_comodo, $this->desc_comodo, $this->area, null, null, 1 );
        $cod_infra_predio_comodo = $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
      $infra_predio_comodo = new clsPmieducarInfraPredioComodo($cod_infra_predio_comodo);
      $infra_predio_comodo = $infra_predio_comodo->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("infra_predio_comodo", $this->pessoa_logada, $cod_infra_predio_comodo);
      $auditoria->inclusao($infra_predio_comodo);
            $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
            $this->simpleRedirect('educar_infra_predio_comodo_lst.php');
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return false;
    }

    function Editar()
    {


        $this->area = str_replace(".","",$this->area);
        $this->area = str_replace(",",".",$this->area);

        $obj = new clsPmieducarInfraPredioComodo( $this->cod_infra_predio_comodo, $this->pessoa_logada, null, $this->ref_cod_infra_comodo_funcao, $this->ref_cod_infra_predio, $this->nm_comodo, $this->desc_comodo, $this->area, null, null, 1 );
    $detalheAntigo = $obj->detalhe();
        $editou = $obj->edita();
        if( $editou )
        {
      $detalheAtual = $obj->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("infra_predio_comodo", $this->pessoa_logada, $this->cod_infra_predio_comodo);
      $auditoria->alteracao($detalheAntigo, $detalheAtual);
            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_infra_predio_comodo_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj = new clsPmieducarInfraPredioComodo( $this->cod_infra_predio_comodo, $this->pessoa_logada, null,null,null,null,null,null,null,null, 0);
    $detalhe = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
      $auditoria = new clsModulesAuditoriaGeral("infra_predio_comodo", $this->pessoa_logada, $this->cod_infra_predio_comodo);
      $auditoria->exclusao($detalhe);
            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_infra_predio_comodo_lst.php');
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

function getInfraPredio(xml_infra_predio)
{
    /*
    var campoEscola  = document.getElementById('ref_cod_escola').value;
    var campoPredio = document.getElementById('ref_cod_infra_predio');

    campoPredio.length = 1;
    campoPredio.options[0] = new Option( 'Selecione', '', false, false );
    for (var j = 0; j < predio.length; j++)
    {
        if (predio[j][2] == campoEscola)
        {
            campoPredio.options[campoPredio.options.length] = new Option( predio[j][1], predio[j][0],false,false);
        }
    }
    */
    var campoPredio = document.getElementById('ref_cod_infra_predio');
    var DOM_array = xml_infra_predio.getElementsByTagName( "infra_predio" );

    if(DOM_array.length)
    {
        campoPredio.length = 1;
        campoPredio.options[0].text = 'Selecione um prédio';
        campoPredio.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoPredio.options[campoPredio.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_infra_predio"),false,false);
        }
    }
    else
        campoPredio.options[0].text = 'A escola não possui nenhum prédio';

}

function getInfraPredioFuncao(xml_infra_comodo_funcao)
{
    /*
    var campoEscola  = document.getElementById('ref_cod_escola').value;
    var campoFuncao = document.getElementById('ref_cod_infra_comodo_funcao');

    campoFuncao.length = 1;
    campoFuncao.options[0] = new Option( 'Selecione', '', false, false );
    for (var j = 0; j < funcao.length; j++)
    {
        if (funcao[j][2] == campoEscola)
        {
            campoFuncao.options[campoFuncao.options.length] = new Option( funcao[j][1], funcao[j][0],false,false);
        }
    }
    */
    var campoFuncao = document.getElementById('ref_cod_infra_comodo_funcao');
    var DOM_array = xml_infra_comodo_funcao.getElementsByTagName( "infra_comodo_funcao" );

    if(DOM_array.length)
    {
        campoFuncao.length = 1;
        campoFuncao.options[0].text = 'Selecione uma função cômodo';
        campoFuncao.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoFuncao.options[campoFuncao.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_infra_comodo_funcao"),false,false);
        }
    }
    else
        campoFuncao.options[0].text = 'A escola não possui nenhuma função cômodo';
}

document.getElementById('ref_cod_escola').onchange = function()
{
    /*
    getPredio();
    getFuncao();
    */
    var campoEscola  = document.getElementById('ref_cod_escola').value;

    var campoPredio = document.getElementById('ref_cod_infra_predio');
    campoPredio.length = 1;
    campoPredio.disabled = true;
    campoPredio.options[0].text = 'Carregando prédio';

    var campoFuncao = document.getElementById('ref_cod_infra_comodo_funcao');
    campoFuncao.length = 1;
    campoFuncao.disabled = true;
    campoFuncao.options[0].text = 'Carregando função cômodo';

    var xml_infra_predio = new ajax( getInfraPredio );
    xml_infra_predio.envia( "educar_infra_predio_xml.php?esc="+campoEscola );

    var xml_infra_comodo_funcao = new ajax( getInfraPredioFuncao );
    xml_infra_comodo_funcao.envia( "educar_infra_comodo_funcao_xml.php?esc="+campoEscola );

    if ($F('ref_cod_escola') != '')
    {
        $('img_colecao').style.display = '';
        $('img_colecao2').style.display = '';
    }
    else
    {
        $('img_colecao').style.display = 'none;'
        $('img_colecao2').style.display = 'none;'
    }

}

document.getElementById('ref_cod_instituicao').onchange = function()
{
    getEscola();
    $('img_colecao').style.display = 'none;'
    $('img_colecao2').style.display = 'none;'
}

before_getEscola = function()
{
    var campoPredio = document.getElementById('ref_cod_infra_predio');
    campoPredio.length = 1;
    campoPredio.options[0].text = 'Selecione';
    campoPredio.disabled = false;

    var campoFuncao = document.getElementById('ref_cod_infra_comodo_funcao');
    campoFuncao.length = 1;
    campoFuncao.options[0].text = 'Selecione';
    campoFuncao.disabled = false;
}

</script>
