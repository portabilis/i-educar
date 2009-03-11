<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itajaí								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software Público Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itajaí			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  é  software livre, você pode redistribuí-lo e/ou	 *
	*	modificá-lo sob os termos da Licença Pública Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a versão 2 da	 *
	*	Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.	 *
	*																		 *
	*	Este programa  é distribuído na expectativa de ser útil, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-	 *
	*	ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU	 *
	*	junto  com  este  programa. Se não, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - C&ocirc;modo Pr&eacute;dio" );
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
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

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
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$this->fexcluir = true;
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_infra_predio_comodo_det.php?cod_infra_predio_comodo={$registro["cod_infra_predio_comodo"]}" : "educar_infra_predio_comodo_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_infra_predio_comodo", $this->cod_infra_predio_comodo );

		$obrigatorio = true;
		$get_escola	 = true;
		include("include/pmieducar/educar_campo_lista.php");

		$opcoes_predio = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarInfraPredio" ) )
		{
			/*$todos_predios  = "predio = new Array();\n";
			$objTemp = new clsPmieducarInfraPredio();
			$lista = $objTemp->lista( null,null,null,null,null,null,null,null,null,null,null,1 );
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$todos_predios .= "predio[predio.length] = new Array( {$registro["cod_infra_predio"]}, '{$registro['nm_predio']}', {$registro["ref_cod_escola"]} );\n";
				}
			}
			echo "<script>{$todos_predios}</script>";*/

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
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarInfraPredio nao encontrada\n-->";
			$opcoes_predio = array( "" => "Erro na geracao" );
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
		if( class_exists( "clsPmieducarInfraComodoFuncao" ) )
		{
			/*$todas_funcoes  = "funcao = new Array();\n";
			$objTemp = new clsPmieducarInfraComodoFuncao();
			$lista = $objTemp->lista( null,null,null,null,null,null,null,null,null,1 );
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$todas_funcoes .= "funcao[funcao.length] = new Array( {$registro["cod_infra_comodo_funcao"]}, '{$registro['nm_funcao']}', {$registro["ref_cod_escola"]} );\n";
				}
			}
			echo "<script>{$todas_funcoes}</script>";*/

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
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarInfraComodoFuncao nao encontrada\n-->";
			$opcoes_funcao = array( "" => "Erro na geracao" );
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
		$this->campoLista( "ref_cod_infra_comodo_funcao", "Func&atilde;o C&ocirc;modo", $opcoes_funcao, $this->ref_cod_infra_comodo_funcao,"", false, "", $script );



		// text
		$this->campoTexto( "nm_comodo", "C&ocirc;modo", $this->nm_comodo, 43, 255, true );
		$this->campoMonetario("area", "&Aacute;rea", $this->area, 10, 255, true );
		$this->campoMemo( "desc_comodo", "Descrição C&ocirc;modo", $this->desc_comodo, 60, 5, false );
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		$this->area = str_replace(".","",$this->area);
		$this->area = str_replace(",",".",$this->area);
		$obj = new clsPmieducarInfraPredioComodo( null, null, $this->pessoa_logada, $this->ref_cod_infra_comodo_funcao, $this->ref_cod_infra_predio, $this->nm_comodo, $this->desc_comodo, $this->area, null, null, 1 );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_infra_predio_comodo_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarInfraPredioComodo\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_cod_infra_comodo_funcao ) && is_numeric( $this->ref_cod_infra_predio ) && is_string( $this->nm_comodo ) && is_numeric( $this->area )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->area = str_replace(".","",$this->area);
		$this->area = str_replace(",",".",$this->area);

		$obj = new clsPmieducarInfraPredioComodo( $this->cod_infra_predio_comodo, $this->pessoa_logada, null, $this->ref_cod_infra_comodo_funcao, $this->ref_cod_infra_predio, $this->nm_comodo, $this->desc_comodo, $this->area, null, null, 1 );
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_infra_predio_comodo_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarInfraPredioComodo\nvalores obrigatorios\nif( is_numeric( $this->cod_infra_predio_comodo ) && is_numeric( $this->pessoa_logada ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarInfraPredioComodo( $this->cod_infra_predio_comodo, $this->pessoa_logada, null,null,null,null,null,null,null,null, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_infra_predio_comodo_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarInfraPredioComodo\nvalores obrigatorios\nif( is_numeric( $this->cod_infra_predio_comodo ) && is_numeric( $this->pessoa_logada ) )\n-->";
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
	var campoPredio	= document.getElementById('ref_cod_infra_predio');

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
	var campoPredio	= document.getElementById('ref_cod_infra_predio');
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
	var campoFuncao	= document.getElementById('ref_cod_infra_comodo_funcao');

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
	var campoFuncao	= document.getElementById('ref_cod_infra_comodo_funcao');
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

	var campoPredio	= document.getElementById('ref_cod_infra_predio');
	campoPredio.length = 1;
	campoPredio.disabled = true;
	campoPredio.options[0].text = 'Carregando prédio';

	var campoFuncao	= document.getElementById('ref_cod_infra_comodo_funcao');
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
	var campoPredio	= document.getElementById('ref_cod_infra_predio');
	campoPredio.length = 1;
	campoPredio.options[0].text = 'Selecione';
	campoPredio.disabled = false;

	var campoFuncao	= document.getElementById('ref_cod_infra_comodo_funcao');
	campoFuncao.length = 1;
	campoFuncao.options[0].text = 'Selecione';
	campoFuncao.disabled = false;
}

</script>