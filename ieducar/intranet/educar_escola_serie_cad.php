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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Escola S&eacute;rie" );
		$this->processoAp = "585";
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

	var $ref_cod_escola;
	var $ref_cod_escola_;
	var $ref_cod_serie;
	var $ref_cod_serie_;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $hora_inicial;
	var $hora_final;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $hora_inicio_intervalo;
	var $hora_fim_intervalo;
	var $hora_fim_intervalo_;

	var $ref_cod_instituicao;
	var $ref_cod_curso;
	var $intervalo;

	var $escola_serie_disciplina;
	var $ref_cod_disciplina;
	var $incluir_disciplina;
	var $excluir_disciplina;

	var $disciplinas;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->ref_cod_serie=$_GET["ref_cod_serie"];
		$this->ref_cod_escola=$_GET["ref_cod_escola"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 585, $this->pessoa_logada, 7, "educar_escola_serie_lst.php" );

		if( is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_serie ) )
		{

//			$obj = new clsPmieducarEscolaSerie( $this->ref_cod_escola, $this->ref_cod_serie );
//			$registro  = $obj->detalhe();
			$tmp_obj = new clsPmieducarEscolaSerie();
			$lst_obj = $tmp_obj->lista($this->ref_cod_escola, $this->ref_cod_serie);
			$registro = array_shift($lst_obj);

			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$this->fexcluir = $obj_permissoes->permissao_excluir( 585, $this->pessoa_logada, 7 );
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_escola_serie_det.php?ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_serie={$registro["ref_cod_serie"]}" : "educar_escola_serie_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		if( $_POST )
			foreach( $_POST AS $campo => $val )
				$this->$campo = ( $this->$campo ) ? $this->$campo : $val;

		if ( is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_serie) )
		{
			$instituicao_desabilitado = true;
			$escola_desabilitado = true;
			$escola_curso_desabilitado = true;
			$serie_desabilitado = true;
			$escola_serie_desabilitado = true;
			$this->campoOculto( "ref_cod_escola_", $this->ref_cod_escola );
			$this->campoOculto( "ref_cod_serie_", $this->ref_cod_serie );


		}
		$obrigatorio = true;
		$get_escola = true;
//		$get_escola_curso = true;
		$get_curso = true;
		$get_serie = false;
		$get_escola_serie = true;

		include("include/pmieducar/educar_campo_lista.php");

		if ($this->ref_cod_escola_)
			$this->ref_cod_escola = $this->ref_cod_escola_;
		if ($this->ref_cod_serie_)
			$this->ref_cod_serie = $this->ref_cod_serie_;
			
		
		/***********************************COLOCADO*****************************************/
		$opcoes_serie = array( "" => "Selecione" );
		// EDITAR
		if ( $this->ref_cod_curso )
		{
			$obj_serie = new clsPmieducarSerie();
			$obj_serie->setOrderby("nm_serie ASC");
			$lst_serie = $obj_serie->lista( null,null,null,$this->ref_cod_curso,null,null,null,null,null,null,null,null,1);
			if ( is_array( $lst_serie ) && count( $lst_serie ) )
			{
				foreach ( $lst_serie as $serie )
				{
					$opcoes_serie["{$serie["cod_serie"]}"] = $serie['nm_serie'];
				}
			}
		}
		$script = "javascript:showExpansivelIframe(520, 550, 'educar_serie_cad_pop.php');";
		if ($this->ref_cod_serie)
		{
			$script = "<img id='img_colecao' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
			$this->campoLista( "ref_cod_serie", "Série", $opcoes_serie, $this->ref_cod_serie, "", false, "", $script, true);
		}
		else
		{
			$script = "<img id='img_colecao' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
			$this->campoLista( "ref_cod_serie", "Série", $opcoes_serie, $this->ref_cod_serie, "", false, "", $script);
		}		
		/***********************************COLOCADO****************************************/
			
		/*$obj_serie = new clsPmieducarSerie( $this->ref_cod_serie );
		$det_serie = $obj_serie->detalhe();
		$this->intervalo = $det_serie["intervalo"];
		$this->campoOculto( "intervalo", $this->intervalo );*/
 
		$this->hora_inicial =substr($this->hora_inicial,0,5);
		$this->hora_final =substr($this->hora_final,0,5);
		$this->hora_inicio_intervalo =substr($this->hora_inicio_intervalo,0,5);
		$this->hora_fim_intervalo =substr($this->hora_fim_intervalo,0,5);

		// hora
		$this->campoHora( "hora_inicial", "Hora Inicial", $this->hora_inicial, true );
		$this->campoHora( "hora_final", "Hora Final", $this->hora_final, true );
		$this->campoHora( "hora_inicio_intervalo", "Hora In&iacute;cio Intervalo", $this->hora_inicio_intervalo, true );
		$this->campoHora( "hora_fim_intervalo", "Hora Fim Intervalo", $this->hora_fim_intervalo, true );
//		$this->campoTextoInv( "hora_fim_intervalo", "Hora Fim Intervalo", $this->hora_fim_intervalo, 6, 5, true );

		//$this->campoOculto( "hora_fim_intervalo_", $this->hora_fim_intervalo_ );

	//-----------------------INCLUI DISCIPLINA------------------------//
		$this->campoQuebra();

		//if ( $_POST["escola_serie_disciplina"] )
			//$this->escola_serie_disciplina = unserialize( urldecode( $_POST["escola_serie_disciplina"] ) );

		if( is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_serie ) /*&& !$_POST*/ )
		{
			$obj = new clsPmieducarEscolaSerieDisciplina();
			$registros = $obj->lista( $this->ref_cod_serie, $this->ref_cod_escola,null,1 );
			if( $registros )
			{
				foreach ( $registros AS $campo )
					$this->escola_serie_disciplina[$campo["ref_cod_disciplina"]] = $campo["ref_cod_disciplina"];
			}
		}
		/*if ( $_POST["ref_cod_disciplina"] )
		{
			$this->escola_serie_disciplina["ref_cod_disciplina_"][] = $_POST["ref_cod_disciplina"];
			unset( $this->ref_cod_disciplina );
		}*/

		//$this->campoOculto( "excluir_disciplina", "" );
		//unset($aux);

		/*if ( $this->escola_serie_disciplina )
		{
			foreach ( $this->escola_serie_disciplina as $key => $campo )
			{
				foreach ($campo as $chave => $disciplinas)
				{
					if ( $this->excluir_disciplina == $disciplinas )
					{
						$this->escola_serie_disciplina[$chave] = null;
						$this->excluir_disciplina = null;
					}
					else
					{
						$obj_disciplina = new clsPmieducarDisciplina($disciplinas);
						$obj_disciplina_det = $obj_disciplina->detalhe();
						$nm_disciplina = $obj_disciplina_det["nm_disciplina"];
						//$this->campoTextoInv( "ref_cod_disciplina_{$disciplinas}", "", $nm_disciplina, 30, 255, false, false, false, "", "<a href='#' onclick=\"getElementById('excluir_disciplina').value = '{$disciplinas}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>" );
						$this->campoCheck( "ref_cod_disciplina[]", "Disciplina", $nm_disciplina,"");
						$aux["ref_cod_disciplina_"][$disciplinas] = $disciplinas;
					//}
				}
			}
			//unset($this->escola_serie_disciplina);
			//$this->escola_serie_disciplina = $aux;
		}*/

		//$this->campoOculto( "escola_serie_disciplina", serialize( $this->escola_serie_disciplina ) );

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarDisciplina" ) )
		{
			/*$todas_disciplinas = "disciplina = new Array();\n";
			$objTemp = new clsPmieducarDisciplinaSerie();
			$lista = $objTemp->lista(null,null,1);
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$obj_disciplina = new clsPmieducarDisciplina($registro["ref_cod_disciplina"]);
					$obj_disciplina_det = $obj_disciplina->detalhe();
					$nm_disciplina = $obj_disciplina_det["nm_disciplina"];

					$todas_disciplinas .= "disciplina[disciplina.length] = new Array({$registro["ref_cod_disciplina"]},'{$nm_disciplina}', {$registro["ref_cod_serie"]});\n";
				}
			}
			echo "<script>{$todas_disciplinas}</script>";*/

			// EDITAR
			$disciplinas = "Nenhuma série selecionada";
			if($this->ref_cod_serie)
			{
				$disciplinas = "";
				$conteudo = "";

				$objTemp = new clsPmieducarDisciplinaSerie();
				$lista = $objTemp->lista(null,$this->ref_cod_serie,1);

				if ( is_array( $lista ) && count( $lista ) )
				{
					foreach ( $lista as $registro )
					{
						$obj_disciplina = new clsPmieducarDisciplina($registro["ref_cod_disciplina"]);
						$obj_disciplina_det = $obj_disciplina->detalhe();
						$nm_disciplina = $obj_disciplina_det["nm_disciplina"];

						//$opcoes["{$registro['ref_cod_disciplina']}"] = "{$nm_disciplina}";
						$checked = "";
						if($this->escola_serie_disciplina[$registro["ref_cod_disciplina"]] == $registro["ref_cod_disciplina"])
							$checked = "checked=\"checked\"";
						$conteudo .= "<input type=\"checkbox\" $checked name=\"disciplinas[]\" id=\"disciplinas[]\" value=\"{$registro["ref_cod_disciplina"]}\"><label for=\"disciplinas[]\">{$obj_disciplina_det["nm_disciplina"]}</label> <br />";
						//$this->campoCheck("ref_cod_disciplina[{$registro['ref_cod_disciplina']}]","{$nm_disciplina}",$this->escola_serie_disciplina[$registro['ref_cod_disciplina']] == $registro['ref_cod_disciplina']);
					}
				}
				$disciplinas = '<table cellspacing="0" cellpadding="0" border="0">';
				$disciplinas .= "<tr align=\"left\"><td> $conteudo </td></tr>";
				$disciplinas .= '</table>';
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarDisciplinaSerie n&atilde;o encontrada\n-->";
			$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
		}

		$this->campoRotulo("disciplinas_","Disciplinas","<div id='disciplinas'>$disciplinas</div>");

		/*if ( $aux )
			$this->campoLista( "ref_cod_disciplina", "Disciplina", $opcoes, $this->ref_cod_disciplina,"",false,"","<a href='#' onclick=\"getElementById('incluir_disciplina').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>",false,false);
		else
			$this->campoLista( "ref_cod_disciplina", "Disciplina", $opcoes, $this->ref_cod_disciplina,"",false,"","<a href='#' onclick=\"getElementById('incluir_disciplina').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>");
*/
		//$this->campoOculto( "incluir_disciplina", "" );

		$this->campoQuebra();
	//-----------------------FIM INCLUI DISCIPLINA------------------------//
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		//$this->escola_serie_disciplina = unserialize( urldecode( $this->escola_serie_disciplina ) );

		if ($this->disciplinas)
		{
//			echo "clsPmieducarEscolaSerie( $this->ref_cod_escola, $this->ref_cod_serie, null, $this->pessoa_logada, $this->hora_inicial, $this->hora_final, null, null, 1, $this->hora_inicio_intervalo, $this->hora_fim_intervalo )";
//			echo "<pre>"; print_r($this->disciplinas);
//			die;
			$obj = new clsPmieducarEscolaSerie( $this->ref_cod_escola, $this->ref_cod_serie, $this->pessoa_logada, $this->pessoa_logada, $this->hora_inicial, $this->hora_final, null, null, 1, $this->hora_inicio_intervalo, $this->hora_fim_intervalo );
			if ($obj->existe())
				$cadastrou = $obj->edita();
			else
				$cadastrou = $obj->cadastra();

			if( $cadastrou )
			{
			//-----------------------CADASTRA DISCIPLINA------------------------//
				foreach ( $this->disciplinas AS $campo )
				{
					//for ($i = 0; $i < sizeof($campo) ; $i++)
					//{
						$obj = new clsPmieducarEscolaSerieDisciplina( $this->ref_cod_serie, $this->ref_cod_escola, $campo, 1 );
						if ($obj->existe())
							$cadastrou1  = $obj->edita();
						else
							$cadastrou1  = $obj->cadastra();

						if ( !$cadastrou1 )
						{
							$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
							echo "<!--\nErro ao cadastrar clsPmieducarEscolaSerieDisciplina\nvalores obrigat&oacute;rios\nis_numeric( $this->ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( {$campo[$i]} ) \n-->";
							return false;
						}
					//}
				}
				$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
				header( "Location: educar_escola_serie_lst.php" );
				die();
				return true;
			//-----------------------FIM CADASTRA DISCIPLINA------------------------//
			}
			$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
			echo "<!--\nErro ao cadastrar clsPmieducarEscolaSerie\nvalores obrigatorios\nis_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_serie ) && is_numeric( $this->pessoa_logada ) && ( $this->hora_inicial ) && ( $this->hora_final ) && ( $this->hora_inicio_intervalo ) && ( $this->hora_fim_intervalo )\n-->";
			return false;
		}
		echo "<script> alert('É necessário adicionar pelo menos 1 Disciplina') </script>";
		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		//echo '<pre>';print_r($this);die;
		//$this->escola_serie_disciplina = unserialize( urldecode( $this->escola_serie_disciplina ) );

		if ($this->disciplinas)
		{
			$obj = new clsPmieducarEscolaSerie($this->ref_cod_escola_, $this->ref_cod_serie_, $this->pessoa_logada, null, $this->hora_inicial, $this->hora_final, null, null, 1, $this->hora_inicio_intervalo, $this->hora_fim_intervalo);
			$editou = $obj->edita();
			$obj = new clsPmieducarEscolaSerieDisciplina( $this->ref_cod_serie_, $this->ref_cod_escola_, $campo,1 );
			$obj->excluirTodos();
			if( $editou )
			{
			//-----------------------EDITA DISCIPLINA------------------------//
				foreach ( $this->disciplinas AS $campo )
				{
					//for ($i = 0; $i < sizeof($campo) ; $i++)
					//{
						$obj = new clsPmieducarEscolaSerieDisciplina( $this->ref_cod_serie_, $this->ref_cod_escola_, $campo,1 );
						$existe = $obj->existe();
						if ($existe)
						{
							$editou1 = $obj->edita();
							if ( !$editou1 )
							{
								$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
								echo "<!--\nErro ao editar clsPmieducarEscolaSerieDisciplina\nvalores obrigat&oacute;rios\nis_numeric( $this->ref_cod_serie_ ) && is_numeric( $this->ref_cod_escola ) && is_numeric( {$campo[$i]} ) \n-->";
								return false;
							}
						}
						else
						{
							$cadastrou = $obj->cadastra();
							if ( !$cadastrou )
							{
								$this->mensagem = "Cadastro n&atilde;o realizada.<br>";
								echo "<!--\nErro ao editar clsPmieducarEscolaSerieDisciplina\nvalores obrigat&oacute;rios\nis_numeric( $this->ref_cod_serie_ ) && is_numeric( $this->ref_cod_escola ) && is_numeric( {$campo[$i]} ) \n-->";
								return false;
							}
						}
					//}
				}
//				$obj_esc_ser_disc = new clsPmieducarEscolaSerieDisciplina( $this->ref_cod_serie,$this->ref_cod_escola );
//				$diferente = $obj_esc_ser_disc->diferente( $this->escola_serie_disciplina );
//				if ( is_array($diferente) && (count($diferente) > 0) )
//				{
//					foreach ( $diferente AS $key => $disciplina )
//					{
//						$eh_usado = $obj_esc_ser_disc->eh_usado($disciplina['ref_cod_disciplina']);
//						if ($eh_usado)
//						{
//							$obj_disciplina = new clsPmieducarDisciplina( $disciplina['ref_cod_disciplina'] );
//							$det_disciplina = $obj_disciplina->detalhe();
//							$msg[] = $det_disciplina["nm_disciplina"];
//						}
//						else
//						{
//							$obj_esd = new clsPmieducarEscolaSerieDisciplina( $this->ref_cod_serie,$this->ref_cod_escola,$disciplina['ref_cod_disciplina'] );
//							$exclui = $obj_esd->excluir();
//							if (!$exclui)
//							{
//								$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
//								echo "<!--\nErro ao excluir clsPmieducarEscolaSerieDisciplina\nvalores obrigat&oacute;rios\nis_numeric( $this->ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( {$disciplina['ref_cod_disciplina']} ) \n-->";
//								return false;
//							}
//						}
//					}
//
//				}
//
//				$obj_esd = new clsPmieducarEscolaSerieDisciplina();
//				$lst_esd = $obj_esd->lista( $this->ref_cod_serie,$this->ref_cod_escola );
//				if (is_array($lst_esd))
//				{
//					echo "<pre>";print_r($lst_esd);die;
//					foreach ($lst_esd as $key => $campo)
//					{
//						$obj_turma_disciplina = new clsPmieducarTurmaDisciplina( null, $campo["ref_cod_disciplina"], $this->ref_cod_escola, $this->ref_cod_serie_ );
//						$existe = $obj_turma_disciplina->jah_existe();
//						if (!$existe)
//						{
//							$cadastrou3  = $obj_turma_disciplina->cadastra();
//							if( !$cadastrou3 )
//							{
//								$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
//								echo "<!--\nErro ao editar clsPmieducarTurmaDisciplina\nvalores obrigatorios\nis_numeric( $this->cod_turma ) && is_numeric( {$escola_serie_disciplina["ref_cod_disciplina"]} ) && is_numeric( {$this->ref_cod_escola} ) && is_numeric( {$this->ref_ref_cod_serie} )\n-->";
//								return false;
//							}
//						}
//					}
//
//				}
//				if ( is_array($msg) )
//				{
//					$msg_js = "Não é possível excluir a disciplina: \\n";
//					foreach ($msg AS $disciplina)
//					{
//						$msg_js .= " - ".$disciplina."\\n";
//					}
//					$msg_js .= "Está sendo utilizada no Sistema!";
//					echo "<script> alert('$msg_js'); window.location = 'educar_escola_serie_lst.php';</script>";
//					die();
//				}
				$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
				header( "Location: educar_escola_serie_lst.php" );
				die();
				return true;
			//-----------------------FIM EDITA DISCIPLINA------------------------//
			}
			$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
			echo "<!--\nErro ao editar clsPmieducarEscolaSerie\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_escola_ ) && is_numeric( $this->ref_cod_serie_ ) && is_numeric( $this->pessoa_logada ) )\n-->";
			return false;
		}
		echo "<script> alert('É necessário adicionar pelo menos 1 Disciplina') </script>";
		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarEscolaSerie($this->ref_cod_escola_, $this->ref_cod_serie_, $this->pessoa_logada, null,null,null,null,null, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$obj  = new clsPmieducarEscolaSerieDisciplina( $this->ref_cod_serie_, $this->ref_cod_escola_, null, 0 );
			$excluiu1 = $obj->excluirTodos();
			if ( $excluiu1 )
			{
				$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
				header( "Location: educar_escola_serie_lst.php" );
				die();
				return true;
			}
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarEscolaSerie\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_escola_ ) && is_numeric( $this->ref_cod_serie_ ) && is_numeric( $this->pessoa_logada ) )\n-->";
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

document.getElementById('ref_cod_instituicao').onchange = function()
{
	getDuploEscolaCurso();
	$('img_colecao').style.display = 'none;'
}

document.getElementById('ref_cod_escola').onchange = function()
{
	getEscolaCurso();
	$('img_colecao').style.display = 'none;'
}

document.getElementById('ref_cod_curso').onchange = function()
{
	getSerie();
//	changeDisciplinas();
	var campoDisciplinas = document.getElementById('disciplinas');
	campoDisciplinas.innerHTML = "Nenhuma série selecionada";
	if ($F('ref_cod_curso') != '')
	{
		$('img_colecao').style.display = '';
	}
	else
	{
		$('img_colecao').style.display = 'none;'
	}
}


/*
function Disciplinas()
{
	var campoSerie = document.getElementById('ref_cod_serie').value;
	var campoDisciplina = document.getElementById('ref_cod_disciplina');

	campoDisciplina.length = 1;
	for (var j = 0; j < disciplina.length; j++)
	{
		if (disciplina[j][2] == campoSerie)
		{
			campoDisciplina.options[campoDisciplina.options.length] = new Option( disciplina[j][1], disciplina[j][0],false,false);
		}
	}
}*/

function getDisciplina( xml_disciplina )
{
	/*
	var escola = document.getElementById('ref_cod_escola');
	var campoSerie = document.getElementById('ref_cod_serie').value;
	var disciplinas = document.getElementById('disciplinas');
	var conteudo = '';
	disciplinas.innerHTML = 'Selecione uma série';

	if(escola.value == '')
		return;

	for (var j = 0; j < disciplina.length; j++)
	{
		if(disciplina[j][2] == campoSerie){
			conteudo += '<input type="checkbox" checked="checked" name="disciplinas[]" id="disciplinas[]" value="'+ disciplina[j][0] +'"><label for="disciplinas[]">' + disciplina[j][1] +'</label> <br />';
		}
	}
	if(conteudo)
	{
		disciplinas.innerHTML = '<table cellspacing="0" cellpadding="0" border="0">';
		disciplinas.innerHTML += '<tr align="left"><td>'+ conteudo +'</td></tr>';
		disciplinas.innerHTML += '</table>';
	}
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

document.getElementById('ref_cod_serie').onchange = function()
{
//	getDisciplina();
	var campoSerie = document.getElementById('ref_cod_serie').value;

	var campoDisciplinas = document.getElementById('disciplinas');
	campoDisciplinas.innerHTML = "Carregando disciplina";

	var xml_disciplina = new ajax( getDisciplina );
	xml_disciplina.envia( "educar_disciplina_xml.php?ser="+campoSerie );
}

after_getEscola = function()
{
//	getDisciplina();
	getEscolaCurso();
	getSerie();

	var campoDisciplinas = document.getElementById('disciplinas');
	campoDisciplinas.innerHTML = "Nenhuma série selecionada";
};
/*
document.getElementById('hora_inicio_intervalo').addEventListener("keyup",
function(evt){
	var horaInicioIntervalo = document.getElementById("hora_inicio_intervalo").value;
	var horaFimIntervalo = document.getElementById("hora_fim_intervalo");

	if((/2[0-3]|[0-1][0-9][:][0-5][0-9]/.test( horaInicioIntervalo )))
	{
		var t1 = horaInicioIntervalo , t2 = document.getElementById('intervalo').value;

		var soma = (t1.substring(0,t1.indexOf(':'))-0) * 60 +
		        (t1.substring(t1.indexOf(':')+1,t1.length)-0) +
		        (t2 - 0);
		var hora = soma / 60;
		var hr = Math.floor(hora);
		hora = hora - hr;
		var min = hora * 60;
		min = Math.round(min);

		hr = ""+hr;
		if( hr.length == 1 )
			hr = "0"+hr;

		min = ""+min;
		if( min.length == 1 )
		{
			horaFimIntervalo.value = hr+":0"+min;
			document.getElementById("hora_fim_intervalo_").value = horaFimIntervalo.value;
		}
		else
		{
			horaFimIntervalo.value = hr+":"+min;
			document.getElementById("hora_fim_intervalo_").value = horaFimIntervalo.value;
		}

	}
}
,false);
*/





/***********************************COLOCADO****************************************/
function getSerie()
	{
		var campoCurso = document.getElementById('ref_cod_curso').value;
		if ( document.getElementById('ref_cod_escola') )
		{
			var campoEscola = document.getElementById('ref_cod_escola').value;
		}
		else if ( document.getElementById('ref_ref_cod_escola') )
		{
			var campoEscola = document.getElementById('ref_ref_cod_escola').value;
		}
		var campoSerie	= document.getElementById('ref_cod_serie');

		campoSerie.length = 1;

		limpaCampos(4);
		if( campoEscola && campoCurso )
		{
			campoSerie.disabled = true;
			campoSerie.options[0].text = 'Carregando séries';

			var xml = new ajax(atualizaLstSerie);
			xml.envia("educar_serie_not_escola_xml.php?esc="+campoEscola+"&cur="+campoCurso);
		}
		else
		{
			campoSerie.options[0].text = 'Selecione';
		}
	}

	function atualizaLstSerie(xml)
	{

		var campoSerie = document.getElementById('ref_cod_serie');
		campoSerie.length = 1;
		campoSerie.options[0].text = 'Selecione uma série';
		campoSerie.disabled = false;

		series = xml.getElementsByTagName('serie');
		if(series.length)
		{
			for( var i = 0; i < series.length; i++ )
			{
				campoSerie.options[campoSerie.options.length] = new Option( series[i].firstChild.data, series[i].getAttribute('cod_serie'),false,false);
			}
		}
		else
		{
			campoSerie.options[0].text = 'O curso não possui nenhuma série ou todas as séries já estã associadas a essa escola';
			campoSerie.disabled = true;
		}
	}
	/***********************************COLOCADO****************************************/

</script>