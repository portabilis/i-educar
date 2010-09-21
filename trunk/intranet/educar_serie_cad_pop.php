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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - S&eacute;rie" );
		$this->processoAp = "583";
		$this->renderBanner = false;
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

	var $cod_serie;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_curso;
	var $nm_serie;
	var $etapa_curso;
	var $concluinte;
	var $carga_horaria;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $intervalo;

	var $ref_cod_instituicao;

	var $disciplina_serie;
	var $ref_cod_disciplina;
	var $incluir_disciplina;
	var $excluir_disciplina;

	var $idade_inicial;
	var $idade_final;

	var $media_especial;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_serie=$_GET["cod_serie"];
//die();
		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 583, $this->pessoa_logada, 3, "educar_serie_lst.php" );

		/*if( is_numeric( $this->cod_serie ) )
		{

			$obj = new clsPmieducarSerie( $this->cod_serie );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$obj_curso = new clsPmieducarCurso($registro["ref_cod_curso"]);
				$obj_curso_det = $obj_curso->detalhe();
				$this->ref_cod_instituicao = $obj_curso_det["ref_cod_instituicao"];
				$this->fexcluir = $obj_permissoes->permissao_excluir( 583, $this->pessoa_logada,3 );
				$retorno = "Editar";
			}
		}*/
//		$this->url_cancelar = ($retorno == "Editar") ? "educar_serie_det.php?cod_serie={$registro["cod_serie"]}" : "educar_serie_lst.php";
		$this->script_cancelar = "window.parent.fechaExpansivel(\"div_dinamico_\"+(parent.DOM_divs.length-1));";
		$this->nome_url_cancelar = "Cancelar";

		
		$this->campoOculto("ref_cod_instituicao" ,$this->ref_cod_instituicao);
		$this->campoOculto("ref_cod_curso", $this->ref_cod_curso);
		
		return $retorno;
	}

	function Gerar()
	{
		if( $_POST )
			foreach( $_POST AS $campo => $val )
				$this->$campo = ( $this->$campo ) ? $this->$campo : $val;

		// primary keys
		$this->campoOculto( "cod_serie", $this->cod_serie );

		if ($_GET['precisa_lista'])
		{
		
			$obrigatorio = true;
			$get_curso = true;
			include("include/pmieducar/educar_campo_lista.php");	
		}
		// text
	
		$this->campoTexto( "nm_serie", "S&eacute;rie", $this->nm_serie, 30, 255, true );

		$opcoes = array( "" => "Selecione" );
		if( $this->ref_cod_curso )
		{
			$objTemp = new clsPmieducarCurso();
			$lista = $objTemp->lista( $this->ref_cod_curso,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,1);
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes_["{$registro['cod_curso']}"] = "{$registro['qtd_etapas']}";
				}
			}
			for ($i=1; $i <= $opcoes_["{$registro['cod_curso']}"]; $i++)
			{
				$opcoes[$i] = "Etapa {$i}";
			}

		}
		$this->campoLista( "etapa_curso", "Etapa Curso", $opcoes, $this->etapa_curso);

		$opcoes = array( "" => "Selecione", 1 => "n&atilde;o", 2 => "sim");
		$this->campoLista( "concluinte", "Concluinte", $opcoes, $this->concluinte);
		$this->campoMonetario( "carga_horaria", "Carga Hor&aacute;ria", $this->carga_horaria, 7, 7, true );
		$this->campoNumero( "intervalo", "Intervalo", $this->intervalo, 2, 2, true );
		$this->media_especial = dbBool($this->media_especial) ? 'true' : '';
		$this->campoCheck('media_especial','M&eacute;dia Especial',$this->media_especial);

		$this->campoNumero( "idade_inicial", "Faixa et&aacute;ria", $this->idade_inicial, 2, 2, false,"","",false,false,true );
		$this->campoNumero( "idade_final", "&nbsp;até", $this->idade_final, 2, 2, false );

		//-----------------------INCLUI DISCIPLINA------------------------//

		$this->campoQuebra();
		/*
		if ( $_POST["disciplina_serie"] )
			$this->disciplina_serie = unserialize( urldecode( $_POST["disciplina_serie"] ) );
		*/
		if( is_numeric( $this->cod_serie ) /*&& !$_POST*/ )
		{
			$obj = new clsPmieducarDisciplinaSerie();
			$registros = $obj->lista( null,$this->cod_serie,1 );
			if( $registros )
			{
				foreach ( $registros AS $campo )
				{
					$this->disciplina_serie[$campo["ref_cod_disciplina"]] = $campo["ref_cod_disciplina"];
				}
			}
		}



		$disciplinas = "Nenhum curso selecionado";
		if($this->ref_cod_curso)
		{
			$disciplinas = "";
			$conteudo = "";

			$objTemp = new clsPmieducarDisciplina();
			$objTemp->setOrderby("nm_disciplina");
			$lista = $objTemp->lista(null,null,null,null,null,null,null,null,null,null,null,null,1, null, $this->ref_cod_curso);
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
//					$opcoes["{$registro['cod_disciplina']}"] = "{$registro['nm_disciplina']}";
					$checked = "";
					if($this->disciplina_serie[$registro["cod_disciplina"]] == $registro["cod_disciplina"])
						$checked = "checked=\"checked\"";
					$conteudo .= "<input type=\"checkbox\" $checked name=\"disciplinas[]\" id=\"disciplinas[]\" value=\"{$registro["cod_disciplina"]}\"><label for=\"disciplinas[]\">{$registro["nm_disciplina"]}</label> <br />";
				}
			}
			$disciplinas = '<table cellspacing="0" cellpadding="0" border="0">';
			$disciplinas .= "<tr align=\"left\"><td> $conteudo </td></tr>";
			$disciplinas .= '</table>';
		}

		$this->campoRotulo("disciplinas_","Disciplinas","<div id='disciplinas'>$disciplinas</div>");

		$this->campoQuebra();
		//-----------------------FIM INCLUI DISCIPLINA------------------------//
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->carga_horaria = str_replace(".","",$this->carga_horaria);
		$this->carga_horaria = str_replace(",",".",$this->carga_horaria);

		$this->media_especial = $this->media_especial ? "true" : "false";
//		$this->disciplina_serie = unserialize( urldecode( $this->disciplina_serie ) );
		if ($this->disciplinas)
		{
			$obj = new clsPmieducarSerie( null, null, $this->pessoa_logada, $this->ref_cod_curso, $this->nm_serie, $this->etapa_curso, $this->concluinte, $this->carga_horaria, null, null, 1, $this->intervalo, $this->idade_inicial, $this->idade_final, $this->media_especial );
			$cadastrou = $obj->cadastra();
			if( $cadastrou )
			{
			$elemento = ($_GET['ref_ref_cod_serie']) ? 'ref_ref_cod_serie' : 'ref_cod_serie'; 	
			//-----------------------CADASTRA DISCIPLINA------------------------//
				foreach ( $this->disciplinas AS $disciplina )
				{
					$obj = new clsPmieducarDisciplinaSerie( $disciplina, $cadastrou );
					$cadastrou1  = $obj->cadastra();
					if ( !$cadastrou1 )
					{
						$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
						echo "<!--\nErro ao cadastrar clsPmieducarDisciplinaSerie\nvalores obrigat&oacute;rios\nis_numeric( $cadastrou ) && is_numeric( {$disciplina} ) \n-->";
						return false;
					}
				}
				echo "<script>
						if (parent.document.getElementById('{$elemento}').disabled)
							parent.document.getElementById('{$elemento}').options[0] = new Option('Selecione uma série', '', false, false);
						parent.document.getElementById('{$elemento}').options[parent.document.getElementById('{$elemento}').options.length] = new Option('$this->nm_serie', '$cadastrou', false, false);
						parent.document.getElementById('{$elemento}').value = '$cadastrou';
						parent.document.getElementById('{$elemento}').disabled = false;
						window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
			     	</script>";
//				$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
//				header( "Location: educar_serie_lst.php" );
				die();
				return true;
			//-----------------------FIM CADASTRA DISCIPLINA------------------------//
			}
			$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
			echo "<!--\nErro ao cadastrar clsPmieducarSerie\nvalores obrigat&oacute;rios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_cod_curso ) && is_string( $this->nm_serie ) && is_numeric( $this->etapa_curso ) && is_numeric( $this->concluinte ) && is_numeric( $this->carga_horaria ) && is_numeric( $this->intervalo )\n-->";
			return false;
		}
		echo "<script> alert('É necessário adicionar pelo menos 1 Disciplina!') </script>";
		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		return false;
	}

	function Editar()
	{
		/*@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->media_especial = $this->media_especial ? "true" : "false";

		$this->carga_horaria = str_replace(".","",$this->carga_horaria);
		$this->carga_horaria = str_replace(",",".",$this->carga_horaria);

//		$this->disciplina_serie = unserialize( urldecode( $this->disciplina_serie ) );
		if ($this->disciplinas)
		{
			$obj = new clsPmieducarSerie($this->cod_serie, $this->pessoa_logada, null, $this->ref_cod_curso, $this->nm_serie, $this->etapa_curso, $this->concluinte, $this->carga_horaria, null,null, 1, $this->intervalo, $this->idade_inicial, $this->idade_final, $this->media_especial);
			$editou = $obj->edita();
			if( $editou )
			{
			//-----------------------EDITA DISCIPLINA------------------------//
				$obj  = new clsPmieducarDisciplinaSerie( null, $this->cod_serie );
				$excluiu = $obj->desativarDisciplinasSerie(0);
				if ( $excluiu )
				{
					foreach ( $this->disciplinas AS $disciplina )
					{
						$obj = new clsPmieducarDisciplinaSerie( $disciplina, $this->cod_serie,1);
						$existe  = $obj->existe();
						if ($existe)
						{
							$editou1 = $obj->edita();
							if (!$editou1)
							{
								$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
								echo "<!--\nErro ao editar clsPmieducarDisciplinaSerie\nvalores obrigat&oacute;rios\nis_numeric( $this->cod_serie ) && is_numeric( {$disciplina} ) \n-->";
								return false;
							}
						}
						else
						{
							$cadastrou1  = $obj->cadastra();
							if ( !$cadastrou1 )
							{
								$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
								echo "<!--\nErro ao editar clsPmieducarDisciplinaSerie\nvalores obrigat&oacute;rios\nis_numeric( $this->cod_serie ) && is_numeric( {$disciplina} ) \n-->";
								return false;
							}
						}
					}
				}
				$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
				header( "Location: educar_serie_lst.php" );
				die();
				return true;
			//-----------------------FIM EDITA DISCIPLINA------------------------//
			}
			$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
			echo "<!--\nErro ao editar clsPmieducarSerie\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_serie ) && is_numeric( $this->pessoa_logada ) )\n-->";
			return false;
		}
		echo "<script> alert('É necessário adicionar pelo menos 1 Disciplina!') </script>";
		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		return false;*/
	}

	function Excluir()
	{
		/*@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarSerie($this->cod_serie, $this->pessoa_logada,null,null,null,null,null,null,null,null, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$obj  = new clsPmieducarDisciplinaSerie( null, $this->cod_serie );
			$excluiu = $obj->excluirTodos();
			if ( $excluiu )
			{
				$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
				header( "Location: educar_serie_lst.php" );
				die();
				return true;
			}
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarSerie\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_serie ) && is_numeric( $this->pessoa_logada ) )\n-->";
		return false;*/
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
<?
if (!$_GET['precisa_lista']) 
{
?>
	Event.observe(window, 'load', Init, false);
	
	function Init()
	{
		
	//	$this->campoOculto("ref_cod_instituicao" ,$this->ref_cod_instituicao);
	//		$this->campoOculto("ref_cod_curso", $this->ref_cod_curso);
		
		$('ref_cod_instituicao').value = parent.document.getElementById('ref_cod_instituicao').value;
		$('ref_cod_curso').value =  parent.document.getElementById('ref_cod_curso').value; 
	
		var campoCurso = document.getElementById('ref_cod_curso').value;
	
		var campoEtapas = document.getElementById('etapa_curso');
		campoEtapas.length = 1;
		campoEtapas.disabled = true;
		campoEtapas.options[0].text = 'Carregando etapas';
	/*
		var campoDisciplina = document.getElementById('ref_cod_disciplina');
		campoDisciplina.length = 1;
		campoDisciplina.disabled = true;
		campoDisciplina.options[0].text = 'Carregando disciplina';
	*/
		var xml_qtd_etapas = new ajax( EtapasCurso );
		xml_qtd_etapas.envia( "educar_curso_xml2.php?cur="+campoCurso );
	/*
		var xml_disciplina = ajax( getDisciplina );
		xml_disciplina.envia( "educar_disciplina_xml.php?cur="+campoCurso );
	*/
	
		var campoDisciplinas = document.getElementById('disciplinas');
		campoDisciplinas.innerHTML = "Carregando disciplina";
	
		var xml_disciplina = new ajax( getDisciplina );
		xml_disciplina.envia( "educar_disciplina_xml.php?cur="+campoCurso );
	}
<?
}
else
{
?>
	document.getElementById('ref_cod_curso').onchange = function()
	{
	//	EtapasCurso();
	//	getDisciplinas();
		var campoCurso = document.getElementById('ref_cod_curso').value;
	
		var campoEtapas = document.getElementById('etapa_curso');
		campoEtapas.length = 1;
		campoEtapas.disabled = true;
		campoEtapas.options[0].text = 'Carregando etapas';
	/*
		var campoDisciplina = document.getElementById('ref_cod_disciplina');
		campoDisciplina.length = 1;
		campoDisciplina.disabled = true;
		campoDisciplina.options[0].text = 'Carregando disciplina';
	*/
		var xml_qtd_etapas = new ajax( EtapasCurso );
		xml_qtd_etapas.envia( "educar_curso_xml2.php?cur="+campoCurso );
	/*
		var xml_disciplina = ajax( getDisciplina );
		xml_disciplina.envia( "educar_disciplina_xml.php?cur="+campoCurso );
	*/
	
		var campoDisciplinas = document.getElementById('disciplinas');
		campoDisciplinas.innerHTML = "Carregando disciplina";
	
		var xml_disciplina = new ajax( getDisciplina );
		xml_disciplina.envia( "educar_disciplina_xml.php?cur="+campoCurso );
	}
<?}?>

function EtapasCurso(xml_qtd_etapas)
{
	/*
	var campoCurso = document.getElementById('ref_cod_curso').value;
	var campoEtapas = document.getElementById('etapa_curso');
	var etapas;

	campoEtapas.length = 1;
	for (var j = 0; j < curso.length;j++)
	{
		if(curso[j][0] == campoCurso)
		{
			etapas = curso[j][2];
		}
	}

	for (var i = 1; i<=etapas;i++)
	{
		campoEtapas.options[i] = new Option( "Etapa "+i ,i,false,false);
	}
	*/
	var campoEtapas = document.getElementById('etapa_curso');
	var DOM_array = xml_qtd_etapas.getElementsByTagName( "curso" );

	if(DOM_array.length)
	{
		campoEtapas.length = 1;
		campoEtapas.options[0].text = 'Selecione uma etapa';
		campoEtapas.disabled = false;

		var etapas;
		etapas = DOM_array[0].getAttribute("qtd_etapas");

		for (var i = 1; i<=etapas;i++)
		{
			campoEtapas.options[i] = new Option( "Etapa "+i ,i,false,false);
		}
	}
	else
		campoEtapas.options[0].text = 'O curso não possui nenhuma etapa';
}

function getDisciplina( xml_disciplina )
{
	/*
	var campoCurso = document.getElementById('ref_cod_curso').value;
	var campoDisciplina = document.getElementById('ref_cod_disciplina');

	campoDisciplina.length = 1;
	for (var j = 0; j < disciplina.length; j++)
	{
		if (disciplina[j][2] == campoCurso)
		{
			campoDisciplina.options[campoDisciplina.options.length] = new Option( disciplina[j][1], disciplina[j][0],false,false);
		}
	}
	*/

	/*
	var campoDisciplina = document.getElementById('ref_cod_disciplina');
	var DOM_array = xml_disciplina.getElementsByTagName( "disciplina" );

	if(DOM_array.length)
	{
		campoDisciplina.length = 1;
		campoDisciplina.options[0].text = 'Selecione uma disciplina';
		campoDisciplina.disabled = false;

		for( var i = 0; i < DOM_array.length; i++ )
		{
			campoDisciplina.options[campoDisciplina.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_disciplina"),false,false);
		}
	}
	else
		campoDisciplina.options[0].text = 'O curso não possui nenhuma disciplina';
	*/


	var campoDisciplinas = document.getElementById('disciplinas');
	var DOM_array = xml_disciplina.getElementsByTagName( "disciplina" );
	var conteudo = '';

	if(DOM_array.length)
	{
		for( var i = 0; i < DOM_array.length; i++ )
		{
			conteudo += '<input type="checkbox" checked="checked" name="disciplinas[]" id="disciplinas[]" value="'+ DOM_array[i].getAttribute("cod_disciplina") +'"><label for="disciplinas[]">' + DOM_array[i].firstChild.data +'</label> <br />';
		}
	}
	else
		campoDisciplinas.innerHTML = 'A série não possui nenhuma disciplina';

	if(conteudo)
	{
		campoDisciplinas.innerHTML = '<table cellspacing="0" cellpadding="0" border="0">';
		campoDisciplinas.innerHTML += '<tr align="left"><td>'+ conteudo +'</td></tr>';
		campoDisciplinas.innerHTML += '</table>';
	}
}



</script>