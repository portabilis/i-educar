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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Turma" );
		$this->processoAp = "586";
		//$this->scripts[] = "ajax";
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

	var $cod_turma;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_ref_cod_serie;
	var $ref_ref_cod_escola;
	var $ref_cod_infra_predio_comodo;
	var $nm_turma;
	var $sgl_turma;
	var $max_aluno;
	var $multiseriada;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_turma_tipo;
	var $hora_inicial;
	var $hora_final;
	var $hora_inicio_intervalo;
	var $hora_fim_intervalo;

	var $ref_cod_instituicao;
	var $ref_cod_curso;
	var $ref_cod_escola;

	var $padrao_ano_escolar;

	var $ref_cod_regente;
  	var $ref_cod_instituicao_regente;

  	var $ref_ref_cod_serie_mult;

//------INCLUI MODULO------//
	var $turma_modulo;
	var $incluir_modulo;
	var $excluir_modulo;

//------INCLUI DIA_SEMANA------//
	var $dia_semana;
	var $ds_hora_inicial;
	var $ds_hora_final;
	var $turma_dia_semana;
	var $incluir_dia_semana;
	var $excluir_dia_semana;
	var $visivel;

	var $dias_da_semana = array( '' => 'Selecione', 1 => 'Domingo', 2 => 'Segunda', 3 => 'Ter&ccedil;a', 4 => 'Quarta', 5 => 'Quinta', 6 => 'Sexta', 7 => 'S&aacute;bado' );

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_turma=$_GET["cod_turma"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 586, $this->pessoa_logada, 7, "educar_turma_lst.php" );

		if ( is_numeric( $this->cod_turma ) ) {
			$obj 	   = new clsPmieducarTurma( $this->cod_turma );
			$registro  = $obj->detalhe();
			$obj_esc   = new clsPmieducarEscola( $registro["ref_ref_cod_escola"] );
			$det_esc   = $obj_esc->detalhe();
			$obj_ser   = new clsPmieducarSerie( $registro["ref_ref_cod_serie"] );
			$det_ser   = $obj_ser->detalhe();
			$this->ref_cod_escola       = $det_esc["cod_escola"];
			$this->ref_cod_instituicao  = $det_esc["ref_cod_instituicao"];
			$this->ref_cod_curso	    = $det_ser["ref_cod_curso"];
			$obj_curso = new clsPmieducarCurso(($this->ref_cod_curso));
			$det_curso = $obj_curso->detalhe();
			$this->padrao_ano_escolar = $det_curso['padrao_ano_escolar'];
			if ( $registro )
			{
				foreach ( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$this->fexcluir 	 = $obj_permissoes->permissao_excluir( 586, $this->pessoa_logada, 7, "educar_turma_lst.php" );
				$retorno 			 = "Editar";
			}
		}
		$this->url_cancelar 	 = ( $retorno == "Editar" ) ? "educar_turma_det.php?cod_turma={$registro["cod_turma"]}" : "educar_turma_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		if( $_POST )
			foreach( $_POST AS $campo => $val ) 
				$this->$campo = ( $this->$campo ) ? $this->$campo : $val;

		//echo "<pre>";print_r($this);

		$this->campoOculto( "cod_turma", $this->cod_turma );

		// foreign keys
		$obrigatorio = false;
		$instituicao_obrigatorio = true;
	    $escola_curso_obrigatorio = true;
	    $curso_obrigatorio = true;
		$get_escola = true;
//		$get_escola_curso = true;
		$get_escola_curso_serie = false;
		$sem_padrao = true;
		$get_curso = true;
		/*if( isset($_GET["cl"]) )
		{*/
			include("include/pmieducar/educar_campo_lista.php");
		/*}
		else
		{
			include("include/pmieducar/educar_campo_lista.php");
		}*/

		if ( $this->ref_cod_escola )
		{
			$this->ref_ref_cod_escola = $this->ref_cod_escola;
		}

		
				/**
				 * *********************************COLOCADO*****************************************/
//			 getEscolaCursoSerie();
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
		$script = "javascript:showExpansivelIframe(520, 550, 'educar_serie_cad_pop.php?ref_ref_cod_serie=sim');";
		if ($this->ref_cod_instituicao && $this->ref_cod_escola	 && $this->ref_cod_curso)
		{
			$script = "<img id='img_colecao' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
//			$this->campoLista( "ref_ref_cod_serie", "Série", $opcoes_serie, $this->ref_cod_serie, "", false, "", $script, true);
		}
		else
		{
			$script = "<img id='img_colecao' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
			
		}
		$this->campoLista( "ref_ref_cod_serie", "Série", $opcoes_serie, $this->ref_ref_cod_serie, "", false, "", $script);
		
		/***********************************COLOCADO****************************************/
		
//		if( class_exists( "clsPmieducarTurma" ) )
//		{
//			$todas_turmas_sala  = "turma_sala = new Array();\n";
//			$objTemp = new clsPmieducarTurma();
//			$lista = $objTemp->lista( null,null,null,null,null,null,null,null,null,null,null,null,null,null,1 );
//			if ( is_array( $lista ) && count( $lista ) )
//			{
//				foreach ( $lista as $registro )
//				{
//					$obj_infra_predio_comodo = new clsPmieducarInfraPredioComodo( $registro['ref_cod_infra_predio_comodo'] );
//					$det_infra_predio_comodo = $obj_infra_predio_comodo->detalhe();
//
//					$obj_infra_predio = new clsPmieducarInfraPredio( $det_infra_predio_comodo["ref_cod_infra_predio"] );
//					$det_infra_prefalsedio = $obj_infra_predio->detalhe();
//
//					$registro['hora_inicial'] = substr($registro['hora_inicial'],0,5);
//					$registro['hora_final'] = substr($registro['hora_final'],0,5);
//
//					$todas_turmas_sala .= "turma_sala[turma_sala.length] = new Array( {$registro["cod_turma"]}, '{$registro['ref_cod_infra_predio_comodo']}', '{$det_infra_predio['ref_cod_escola']}', '{$registro['hora_inicial']}', '{$registro['hora_final']}' );\n";
//				}
//			}
//			echo "<script type='text/javascript'>{$todas_turmas_sala}</script>";
//		}

	//-------------------INFRA PREDIO COMODO----------------------//
		$opcoes = array( "" => "Selecione" );
		//if( class_exists( "clsPmieducarInfraPredio" ) && class_exists( "clsPmieducarInfraPredioComodo" ) )
		//{
//			$todos_comodos_predios  = "comodo = new Array();\n";
//			$obj_infra_predio = new clsPmieducarInfraPredio();
//			$obj_infra_predio->setOrderby("nm_predio ASC");
//			$lst_infra_predio = $obj_infra_predio->lista( null,null,null,null,null,null,null,null,null,null,null,1 );
//			if ( is_array( $lst_infra_predio ) && count( $lst_infra_predio ) )
//			{
//				foreach ( $lst_infra_predio as $predio )
//				{
//					$obj_infra_predio_comodo = new clsPmieducarInfraPredioComodo();
//					$lst_infra_predio_comodo = $obj_infra_predio_comodo->lista( null,null,null,null,$predio["cod_infra_predio"],null,null,null,null,null,null,null,1 );
//					if ( is_array( $lst_infra_predio_comodo ) && count( $lst_infra_predio_comodo ) )
//					{
//						foreach ( $lst_infra_predio_comodo as $comodo )
//						{
//							$todos_comodos_predios .= "comodo[comodo.length] = new Array( {$comodo["cod_infra_predio_comodo"]}, '{$comodo['nm_comodo']}', {$predio["ref_cod_escola"]} );\n";
//						}
//					}
//				}
//			}
//			echo "<script type='text/javascript'>{$todos_comodos_predios}</script>";

			// EDITAR
			if ( $this->ref_ref_cod_escola )
			{
				$obj_infra_predio = new clsPmieducarInfraPredio();
				$obj_infra_predio->setOrderby("nm_predio ASC");
				$lst_infra_predio = $obj_infra_predio->lista( null,null,null,$this->ref_ref_cod_escola,null,null,null,null,null,null,null,1 );
				if ( is_array( $lst_infra_predio ) && count( $lst_infra_predio ) )
				{
					foreach ( $lst_infra_predio as $predio )
					{
						$obj_infra_predio_comodo = new clsPmieducarInfraPredioComodo();
						$lst_infra_predio_comodo = $obj_infra_predio_comodo->lista( null,null,null,null,$predio["cod_infra_predio"],null,null,null,null,null,null,null,1 );
						if ( is_array( $lst_infra_predio_comodo ) && count( $lst_infra_predio_comodo ) )
						{
							foreach ( $lst_infra_predio_comodo as $comodo )
							{
								$opcoes["{$comodo["cod_infra_predio_comodo"]}"] = $comodo['nm_comodo'];
							}
						}
					}
				}
			}
		//}
		////else
		//{
			//echo "<!--\nErro\nClasse(s) clsPmieducarInfraPredio / clsPmieducarInfraPredioComodo n&atilde;o encontrada(s)\n-->";
			//$opcoes_cursos_escola = array( "" => "Erro na gera&ccedil;&atilde;o" );
		//}
		$this->campoLista( "ref_cod_infra_predio_comodo", "Sala", $opcoes, $this->ref_cod_infra_predio_comodo,null,null,null,null,null,false);

		$array_servidor = array( '' => "Selecione um servidor" );
		if($this->ref_cod_regente)
		{
			$obj_pessoa = new clsPessoa_($this->ref_cod_regente);
			$det = $obj_pessoa->detalhe();
			$array_servidor[$this->ref_cod_regente] = $det['nome'];

		}
		$this->campoListaPesq( "ref_cod_regente", "Professor/Regente", $array_servidor, $this->ref_cod_regente, "", "", false, "", "", null, null, "", true, false, false);
	//-------------------TURMA TIPO----------------------//
		$opcoes = array( "" => "Selecione" );
//		$todos_tipos_turma  = "tipo_turma = new Array();\n";
//		$objTemp = new clsPmieducarTurmaTipo();
//		$objTemp->setOrderby("nm_tipo ASC");
//		$lista = $objTemp->lista( null,null,null,null,null,null,null,null,null,1 );
//		if ( is_array( $lista ) && count( $lista ) )
//		{
//			foreach ( $lista as $registro )
//			{
//				$todos_tipos_turma .= "tipo_turma[tipo_turma.length] = new Array( {$registro["cod_turma_tipo"]}, '{$registro['nm_tipo']}', {$registro["ref_cod_instituicao"]} );\n";
//			}
//		}
//		echo "<script type='text/javascript'>{$todos_tipos_turma}</script>";

		// EDITAR
		if ($this->ref_cod_instituicao)
		{
			$objTemp = new clsPmieducarTurmaTipo();
			$objTemp->setOrderby("nm_tipo ASC");
			$lista = $objTemp->lista( null,null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao );
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_turma_tipo']}"] = "{$registro['nm_tipo']}";
				}
			}
		}

		
		$script = "javascript:showExpansivelIframe(520, 170, 'educar_turma_tipo_cad_pop.php');";
		if ($this->ref_cod_instituicao && $this->ref_cod_escola	 && $this->ref_cod_curso)
		{
			$script = "<img id='img_turma' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
//			$this->campoLista( "ref_ref_cod_serie", "Série", $opcoes_serie, $this->ref_cod_serie, "", false, "", $script, true);
		}
		else
		{
			$script = "<img id='img_turma' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
			
		}
		
		$this->campoLista( "ref_cod_turma_tipo", "Tipo de Turma", $opcoes, $this->ref_cod_turma_tipo, "", false, "", $script );

		// text
		$this->campoTexto( "nm_turma", "Turma", $this->nm_turma, 30, 255, true );
		$this->campoTexto( "sgl_turma", "Sigla", $this->sgl_turma, 15, 15, false );

		$this->campoNumero( "max_aluno", "M&aacute;ximo de Alunos", $this->max_aluno, 3, 3, true );
		$this->campoCheck("visivel", "Ativo", dbBool($this->visivel));
		// checkbox
		$this->campoCheck( "multiseriada", "Multi-Seriada", $this->multiseriada, "", false, false );

		$this->campoLista("ref_ref_cod_serie_mult","S&eacute;rie",array('' => 'Selecione'),'',"",false,"","","",false);
		$this->campoOculto("ref_ref_cod_serie_mult_",$this->ref_ref_cod_serie_mult);
		
		
		$this->campoQuebra2();

//-----------------SE EM CURSO, PADRAO_ANO_ESCOLA == 1-------------------//
		// hora
		$this->campoHora( "hora_inicial", "Hora Inicial", $this->hora_inicial,false );
		$this->campoHora( "hora_final", "Hora Final", $this->hora_final,false );
		$this->campoHora( "hora_inicio_intervalo", "Hora In&iacute;cio Intervalo", $this->hora_inicio_intervalo,false );
		$this->campoHora( "hora_fim_intervalo", "Hora Fim Intervalo", $this->hora_fim_intervalo,false );

//-----------------SENAO, PADRAO_ANO_ESCOLA == 0-------------------//

	//---------------------INCLUI MODULO---------------------//
		$this->campoQuebra();

		if ( $_POST["turma_modulo"] )
			$this->turma_modulo = unserialize( urldecode( $_POST["turma_modulo"] ) );
		$qtd_modulo = ( count( $this->turma_modulo ) == 0 ) ? 1 : ( count( $this->turma_modulo ) + 1);
		if( is_numeric( $this->cod_turma) && !$_POST)
		{
			$obj = new clsPmieducarTurmaModulo();
			$registros = $obj->lista( $this->cod_turma );
			if( $registros )
			{
				foreach ( $registros AS $campo )
				{
					$this->turma_modulo[$campo[$qtd_modulo]]["sequencial_"] = $campo["sequencial"];
					$this->turma_modulo[$campo[$qtd_modulo]]["ref_cod_modulo_"] = $campo["ref_cod_modulo"];
					$this->turma_modulo[$campo[$qtd_modulo]]["data_inicio_"]  = dataFromPgToBr($campo["data_inicio"]);
					$this->turma_modulo[$campo[$qtd_modulo]]["data_fim_"]  = dataFromPgToBr($campo["data_fim"]);
					$qtd_modulo++;
				}
			}
		}
		if ( $_POST["ref_cod_modulo"] && $_POST["data_inicio"] && $_POST["data_fim"] )
		{
			$this->turma_modulo[$qtd_modulo]["sequencial_"] = $qtd_modulo;
			$this->turma_modulo[$qtd_modulo]["ref_cod_modulo_"] = $_POST["ref_cod_modulo"];
			$this->turma_modulo[$qtd_modulo]["data_inicio_"] = $_POST["data_inicio"];
			$this->turma_modulo[$qtd_modulo]["data_fim_"] = $_POST["data_fim"];
			$qtd_modulo++;
			unset( $this->ref_cod_modulo );
			unset( $this->data_inicio );
			unset( $this->data_fim );
		}

		$this->campoOculto( "excluir_modulo", "" );
		$qtd_modulo = 1;
		unset($aux);

		if ( $this->turma_modulo )
		{
			foreach ( $this->turma_modulo as $campo )
			{
				if ( $this->excluir_modulo == $campo["sequencial_"] )
				{
					$this->turma_modulo[$campo["sequencial"]] = null;
					$this->excluir_modulo	= null;
				}
				else
				{
					$obj_modulo = new clsPmieducarModulo($campo["ref_cod_modulo_"]);
					$det_modulo = $obj_modulo->detalhe();
					$nm_tipo_modulo = $det_modulo["nm_tipo"];
					$this->campoTextoInv( "ref_cod_modulo_{$campo["sequencial_"]}", "", $nm_tipo_modulo, 30, 255, false, false, true,"","","","","ref_cod_modulo" );
					$this->campoTextoInv( "data_inicio_{$campo["sequencial_"]}", "", $campo["data_inicio_"], 10, 10, false, false, true,"","","","","" );
					$this->campoTextoInv( "data_fim_{$campo["sequencial_"]}", "", $campo["data_fim_"], 10, 10, false, false, false, "", "<a href='#' onclick=\"document.getElementById('excluir_modulo').value = '{$campo["sequencial_"]}'; document.getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>","","","" );
					$aux[$qtd_modulo]["sequencial_"] = $qtd_modulo;
					$aux[$qtd_modulo]["ref_cod_modulo_"] = $campo["ref_cod_modulo_"];
					$aux[$qtd_modulo]["data_inicio_"] = $campo["data_inicio_"];
					$aux[$qtd_modulo]["data_fim_"] = $campo["data_fim_"];
					$qtd_modulo++;
				}

			}
			unset($this->turma_modulo);
			$this->turma_modulo = $aux;
		}
		$this->campoOculto( "turma_modulo", serialize( $this->turma_modulo ) );

	//-------------------MODULO----------------------//
		// foreign keys
		$opcoes = array( "" => "Selecione" );
		//if( class_exists( "clsPmieducarModulo" ) )
		//{
//			$todos_modulos  = "modulo = new Array();\n";
//			$objTemp = new clsPmieducarModulo();
//			$objTemp->setOrderby("nm_tipo ASC");
//			$lista = $objTemp->lista(null,null,null,null,null,null,null,null,null,null,null,1);
//			if ( is_array( $lista ) && count( $lista ) )
//			{
//				foreach ( $lista as $registro )
//				{
//					$todos_modulos .= "modulo[modulo.length] = new Array( {$registro["cod_modulo"]}, '{$registro['nm_tipo']}', '{$registro["ref_cod_instituicao"]}' );\n";
//				}
//			}
//			echo "<script type='text/javascript'>{$todos_modulos}</script>";

			// EDITAR
			if ($this->ref_cod_instituicao)
			{
				$objTemp = new clsPmieducarModulo();
				$objTemp->setOrderby("nm_tipo ASC");
				$lista = $objTemp->lista(null,null,null,null,null,null,null,null,null,null,null,1,$this->ref_cod_instituicao);
				if ( is_array( $lista ) && count( $lista ) )
				{
					foreach ( $lista as $registro )
					{
						$opcoes["{$registro['cod_modulo']}"] = "{$registro['nm_tipo']}";
					}
				}
			}
		//}
		//else
		//{
		//	echo "<!--\nErro\nClasse clsPmieducarModulo nao encontrada\n-->";
		//	$opcoes = array( "" => "Erro na geracao" );
		//}
		$this->campoLista( "ref_cod_modulo", "M&oacute;dulo", $opcoes, $this->ref_cod_modulo,null,null,null,null,null,false );
		$this->campoData( "data_inicio", "Data In&iacute;cio", $this->data_inicio,false );
		$this->campoData( "data_fim", "Data Fim", $this->data_fim,false );

		$this->campoOculto( "incluir_modulo", "" );
		$this->campoRotulo( "bt_incluir_modulo", "M&oacute;dulo", "<a href='#' onclick=\"document.getElementById('incluir_modulo').value = 'S'; document.getElementById('tipoacao').value = ''; acao();\"><img src='imagens/nvp_bot_adiciona.gif' alt='adicionar' title='Incluir' border=0></a>" );

		$this->campoQuebra();
	//---------------------FIM INCLUI MODULO---------------------//


	//-----------------------INCLUI DIA SEMANA------------------------//
		$this->campoQuebra();

		if ( $_POST["turma_dia_semana"] )
			$this->turma_dia_semana = unserialize( urldecode( $_POST["turma_dia_semana"] ) );
		if( is_numeric( $this->cod_turma ) && !$_POST )
		{
			$obj = new clsPmieducarTurmaDiaSemana();
			$registros = $obj->lista( null,$this->cod_turma );
			if( $registros )
			{
				foreach ( $registros AS $campo )
				{
					$aux["dia_semana_"]= $campo["dia_semana"];
					$aux["hora_inicial_"]= $campo["hora_inicial"];
					$aux["hora_final_"]= $campo["hora_final"];
					$this->turma_dia_semana[] = $aux;
				}
			}
		}

		unset($aux);

		if ( $_POST["dia_semana"] && $_POST["ds_hora_inicial"] && $_POST["ds_hora_final"] )
		{
			$aux["dia_semana_"] = $_POST["dia_semana"];
			$aux["hora_inicial_"] = $_POST["ds_hora_inicial"];
			$aux["hora_final_"] = $_POST["ds_hora_final"];
			$this->turma_dia_semana[] = $aux;
			unset( $this->dia_semana );
			unset( $this->ds_hora_inicial );
			unset( $this->ds_hora_final );
		}

		$this->campoOculto( "excluir_dia_semana", "" );
		unset($aux);

		if ( $this->turma_dia_semana )
		{
			foreach ( $this->turma_dia_semana as $key => $dias_semana)
			{
				if ( $this->excluir_dia_semana == $dias_semana["dia_semana_"] )
				{
					unset($this->turma_dia_semana[$key]);
					unset($this->excluir_dia_semana);
				}
				else
				{
					$nm_dia_semana = $this->dias_da_semana[$dias_semana["dia_semana_"]];

					$this->campoTextoInv( "dia_semana_{$dias_semana["dia_semana_"]}", "", $nm_dia_semana, 8, 8, false, false, true,"","","","","dia_semana" );
					$this->campoTextoInv( "hora_inicial_{$dias_semana["dia_semana_"]}", "", $dias_semana['hora_inicial_'], 5, 5, false, false, true, "","","","","ds_hora_inicial_" );
					$this->campoTextoInv( "hora_final_{$dias_semana["dia_semana_"]}", "", $dias_semana['hora_final_'], 5, 5, false, false, false, "", "<a href='#' onclick=\"document.getElementById('excluir_dia_semana').value = '{$dias_semana["dia_semana_"]}'; document.getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>","","","ds_hora_final_" );
					$aux["dia_semana_"] = $dias_semana["dia_semana_"];
					$aux["hora_inicial_"] = $dias_semana['hora_inicial_'];
					$aux["hora_final_"] = $dias_semana['hora_final_'];
				}
			}
		}
		$this->campoOculto( "turma_dia_semana", serialize( $this->turma_dia_semana ) );

		if( class_exists( "clsPmieducarTurmaDiaSemana" ) )
		{
			$opcoes = $this->dias_da_semana;
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarTurmaDiaSemana n&atilde;o encontrada\n-->";
			$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
		}
		$this->campoLista( "dia_semana", "Dia Semana", $opcoes, $this->dia_semana,null,false,"","",false,false);
		$this->campoHora( "ds_hora_inicial", "Hora Inicial", $this->ds_hora_inicial,false );
		$this->campoHora( "ds_hora_final", "Hora Final", $this->ds_hora_final,false );

		$this->campoOculto( "incluir_dia_semana", "" );
		$this->campoRotulo( "bt_incluir_dia_semana", "Dia Semana", "<a href='#' onclick=\"document.getElementById('incluir_dia_semana').value = 'S'; document.getElementById('tipoacao').value = ''; acao();\"><img src='imagens/nvp_bot_adiciona.gif' alt='adicionar' title='Incluir' border=0></a>" );

		$this->campoQuebra();
	//-----------------------FIM DIA SEMANA------------------------//

		$this->campoOculto( "padrao_ano_escolar", $this->padrao_ano_escolar );

		$this->acao_enviar = "valida()";
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		$this->ref_cod_instituicao_regente = $this->ref_cod_instituicao;
		if ( $this->multiseriada == "on" )
			$this->multiseriada = 1;
		else
			$this->multiseriada = 0;
		
		if ($this->visivel == "on")
		{
			$this->visivel = true;
		}
		else 
		{
			$this->visivel = false;
		}

		// não segue o padrao do curso
		if ($this->padrao_ano_escolar == 0)
		{
			$this->turma_modulo = unserialize( urldecode( $this->turma_modulo ) );
			$this->turma_dia_semana = unserialize( urldecode( $this->turma_dia_semana ) );
			if ($this->turma_modulo && $this->turma_dia_semana)
			{
				$obj = new clsPmieducarTurma( null, null, $this->pessoa_logada, $this->ref_ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_infra_predio_comodo, $this->nm_turma, $this->sgl_turma, $this->max_aluno, $this->multiseriada, null, null, 1, $this->ref_cod_turma_tipo,null,null,null,null,$this->ref_cod_regente,$this->ref_cod_instituicao_regente,$this->ref_cod_instituicao,$this->ref_cod_curso,$this->ref_ref_cod_serie_mult,$this->ref_cod_escola, $this->visivel);
				$cadastrou = $obj->cadastra();
				if( $cadastrou )
				{
				//--------------CADASTRA MODULO--------------//
					foreach ( $this->turma_modulo AS $campo )
					{
						$campo["data_inicio_"] = dataToBanco($campo["data_inicio_"]);
						$campo["data_fim_"] = dataToBanco($campo["data_fim_"]);

						$obj = new clsPmieducarTurmaModulo( $cadastrou, $campo["ref_cod_modulo_"], $campo["sequencial_"], $campo["data_inicio_"], $campo["data_fim_"] );
						$cadastrou1 = $obj->cadastra();
						if( !$cadastrou1 )
						{
							$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
							echo "<!--\nErro ao cadastrar clsPmieducarTurmaModulo\nvalores obrigatorios\nis_numeric( $cadastrou ) && is_numeric( {$campo["ref_cod_modulo_"]} ) && is_numeric( {$campo["sequencial_"]} ) && is_string( {$campo["data_inicio_"]} ) && is_string( {$campo["data_fim_"]} )\n-->";
							return false;
						}
					}
				//--------------FIM CADASTRA MODULO--------------//

				//-----------------------CADASTRA DIA SEMANA------------------------//
					foreach ( $this->turma_dia_semana AS $campo )
					{
						$obj = new clsPmieducarTurmaDiaSemana( $campo["dia_semana_"], $cadastrou, $campo["hora_inicial_"], $campo["hora_final_"] );
						$cadastrou2  = $obj->cadastra();
						if ( !$cadastrou2 )
						{
							$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
							echo "<!--\nErro ao cadastrar clsPmieducarTurmaDiaSemana\nvalores obrigat&oacute;rios\nis_numeric( $cadastrou ) && is_numeric( {$campo["dia_semana_"]} ) && is_string( {$campo["hora_inicial_"]} ) && is_string( {$campo["hora_final_"]} )\n-->";
							return false;
						}
					}
					$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
					header( "Location: educar_turma_lst.php" );
					die();
					return true;
				//-----------------------FIM CADASTRA DIA SEMANA------------------------//
				}
				$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
				echo "<!--\nErro ao cadastrar clsPmieducarTurma\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_infra_predio_comodo ) && is_string( $this->nm_turma ) && is_numeric( $this->max_aluno ) && is_numeric( $this->multiseriada ) && is_numeric( $this->ref_cod_turma_tipo )\n-->";
				return false;
			}
			echo "<script type='text/javascript'> alert('É necessário adicionar pelo menos 1 Módulo e 1 Dia da Semana!') </script>";
			$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
			return false;
		} // segue o padrao do curso
		else if ($this->padrao_ano_escolar == 1)
		{
			$obj = new clsPmieducarTurma( null, null, $this->pessoa_logada, $this->ref_ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_infra_predio_comodo, $this->nm_turma, $this->sgl_turma, $this->max_aluno, $this->multiseriada, null, null, 1, $this->ref_cod_turma_tipo, $this->hora_inicial, $this->hora_final, $this->hora_inicio_intervalo, $this->hora_fim_intervalo,$this->ref_cod_regente,$this->ref_cod_instituicao_regente,$this->ref_cod_instituicao,$this->ref_cod_curso, $this->ref_ref_cod_serie_mult,$this->ref_cod_escola, $this->visivel );
			$cadastrou = $obj->cadastra();
			if( $cadastrou )
			{
				$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
				header( "Location: educar_turma_lst.php" );
				die();
				return true;
			}
			$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
			echo "<!--\nErro ao cadastrar clsPmieducarTurma\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_infra_predio_comodo ) && is_string( $this->nm_turma ) && is_numeric( $this->max_aluno ) && is_numeric( $this->multiseriada ) && is_numeric( $this->ref_cod_turma_tipo )\n-->";
			return false;
		}
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		$this->ref_cod_instituicao_regente = $this->ref_cod_instituicao;

		if ( $this->multiseriada == "on" )
			$this->multiseriada = 1;
		else
			$this->multiseriada = 0;

		if ($this->visivel == "on")
		{
			$this->visivel = true;
		}
		else 
		{
			$this->visivel = false;
		}

		// não segue o padrao do curso
		if ($this->padrao_ano_escolar == 0)
		{
			$this->turma_modulo = unserialize( urldecode( $this->turma_modulo ) );
			$this->turma_dia_semana = unserialize( urldecode( $this->turma_dia_semana ) );
			if ($this->turma_modulo && $this->turma_dia_semana)
			{
				$obj = new clsPmieducarTurma( $this->cod_turma, $this->pessoa_logada, null, $this->ref_ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_infra_predio_comodo, $this->nm_turma, $this->sgl_turma, $this->max_aluno, $this->multiseriada, null, null, 1, $this->ref_cod_turma_tipo,null,null,null,null,$this->ref_cod_regente,$this->ref_cod_instituicao_regente,$this->ref_cod_instituicao,$this->ref_cod_curso, $this->ref_ref_cod_serie_mult, $this->ref_cod_escola, $this->visivel );
				$editou = $obj->edita();
				if( $editou )
				{
				//--------------EDITA MODULO--------------//
					$obj  = new clsPmieducarTurmaModulo();
					$excluiu = $obj->excluirTodos($this->cod_turma);
					if ( $excluiu )
					{
						foreach ( $this->turma_modulo AS $campo )
						{
							$campo["data_inicio_"] = dataToBanco($campo["data_inicio_"]);
							$campo["data_fim_"] = dataToBanco($campo["data_fim_"]);

							$obj = new clsPmieducarTurmaModulo( $this->cod_turma, $campo["ref_cod_modulo_"], $campo["sequencial_"], $campo["data_inicio_"], $campo["data_fim_"] );
							$cadastrou1 = $obj->cadastra();
							if( !$cadastrou1 )
							{
								$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
								echo "<!--\nErro ao editar clsPmieducarTurmaModulo\nvalores obrigatorios\nis_numeric( $this->cod_turma ) && is_numeric( {$campo["ref_cod_modulo_"]} ) \n-->";
								return false;
							}
						}
					}
				//--------------FIM EDITA MODULO--------------//

				//-----------------------EDITA DIA SEMANA------------------------//
					$obj  = new clsPmieducarTurmaDiaSemana( null, $this->cod_turma );
					$excluiu = $obj->excluirTodos();
					if ( $excluiu )
					{
						foreach ( $this->turma_dia_semana AS $campo )
						{
							$obj = new clsPmieducarTurmaDiaSemana( $campo["dia_semana_"], $this->cod_turma, $campo["hora_inicial_"], $campo["hora_final_"] );
							$cadastrou2  = $obj->cadastra();
							if ( !$cadastrou2 )
							{
								$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
								echo "<!--\nErro ao editar clsPmieducarTurmaDiaSemana\nvalores obrigat&oacute;rios\nis_numeric( $this->cod_turma ) && is_numeric( {$campo["dia_semana_"]} ) \n-->";
								return false;
							}
						}
					}
				//-----------------------FIM EDITA DIA SEMANA------------------------//
				}
				else
				{
					$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
					echo "<!--\nErro ao editar clsPmieducarTurma\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_infra_predio_comodo ) && is_string( $this->nm_turma ) && is_numeric( $this->max_aluno ) && is_numeric( $this->multiseriada ) && is_numeric( $this->ref_cod_turma_tipo )\n-->";
					return false;
				}
			}
			else
			{
				echo "<script type='text/javascript'> alert('É necessário adicionar pelo menos 1 Módulo e 1 Dia da Semana!') </script>";
				$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
				return false;
			}
		} // segue o padrao do curso
		else if ($this->padrao_ano_escolar == 1)
		{
			$obj = new clsPmieducarTurma( $this->cod_turma, $this->pessoa_logada, null, $this->ref_ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_infra_predio_comodo, $this->nm_turma, $this->sgl_turma, $this->max_aluno, $this->multiseriada, null, null, 1, $this->ref_cod_turma_tipo, $this->hora_inicial, $this->hora_final, $this->hora_inicio_intervalo, $this->hora_fim_intervalo,$this->ref_cod_regente,$this->ref_cod_instituicao_regente,$this->ref_cod_instituicao,$this->ref_cod_curso, $this->ref_ref_cod_serie_mult,$this->ref_cod_escola, $this->visivel );
			$editou = $obj->edita();
		}

		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_turma_lst.php" );
			die();
			return true;
		}
		else
		{
			$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
			echo "<!--\nErro ao editar clsPmieducarTurma\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_infra_predio_comodo ) && is_string( $this->nm_turma ) && is_numeric( $this->max_aluno ) && is_numeric( $this->multiseriada ) && is_numeric( $this->ref_cod_turma_tipo )\n-->";
			return false;
		}
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarTurma( $this->cod_turma, $this->pessoa_logada, null,null,null,null,null,null,null,null,null,null, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$obj  = new clsPmieducarTurmaModulo();
			$excluiu1 = $obj->excluirTodos($this->cod_turma);
			if ( $excluiu1 )
			{
				$obj  = new clsPmieducarTurmaDiaSemana( null, $this->cod_turma );
				$excluiu2 = $obj->excluirTodos();
				if ( $excluiu2 )
				{
					$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
					header( "Location: educar_turma_lst.php" );
					die();
					return true;
				}
				else
				{
					$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
					echo "<!--\nErro ao excluir clsPmieducarTurma\nvalores obrigatorios\nif( is_numeric( $this->cod_turma ) && is_numeric( $this->pessoa_logada ) )\n-->";
					return false;
				}
			}
			else
			{
				$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
				echo "<!--\nErro ao excluir clsPmieducarTurma\nvalores obrigatorios\nif( is_numeric( $this->cod_turma ) && is_numeric( $this->pessoa_logada ) )\n-->";
				return false;
			}
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarTurma\nvalores obrigatorios\nif( is_numeric( $this->cod_turma ) && is_numeric( $this->pessoa_logada ) )\n-->";
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

<script type='text/javascript'>

function getComodo()
{
	//setTimeout( function() {
		var campoEscola  = document.getElementById('ref_cod_escola').value;
		var campoComodo	= document.getElementById('ref_cod_infra_predio_comodo');
		campoComodo.disabled = true;

		campoComodo.length = 1;
		campoComodo.options[0] = new Option( 'Selecione uma sala', '', false, false );

		var xml1 = new ajax(atualizaTurmaCad_TipoComodo);
		strURL = "educar_escola_comodo_xml.php?esc="+campoEscola;
		xml1.envia(strURL);
//		DOM_execute_when_xmlhttpChange = function() {  atualizaTurmaCad_TipoComodo(); };
//		strURL = "educar_escola_comodo_xml.php?esc="+campoEscola;
//		DOM_loadXMLDoc( strURL );
	//}, 1500 );

	//	var campoEscola  = document.getElementById('ref_cod_escola').value;
//	var campoComodo	= document.getElementById('ref_cod_infra_predio_comodo');
//
//	campoComodo.length = 1;
//	campoComodo.options[0] = new Option( 'Selecione uma sala', '', false, false );
//	for (var j = 0; j < comodo.length; j++)
//	{
//		if (comodo[j][2] == campoEscola)
//		{
//			campoComodo.options[campoComodo.options.length] = new Option( comodo[j][1], comodo[j][0],false,false);
//		}
//	}
//	if ( campoComodo.length == 1 && campoEscola != '' )
//	{
//		campoComodo.options[0] = new Option( 'A escola não possui nenhuma sala', '', false, false );
//	}
}

function atualizaTurmaCad_TipoComodo(xml)
{
	var campoComodo	= document.getElementById('ref_cod_infra_predio_comodo');
	campoComodo.disabled = false;

	var tipo_comodo = xml.getElementsByTagName( "item" );

	if(tipo_comodo.length)
	{
		for(var i = 0; i < tipo_comodo.length; i+=2 )
		{
			campoComodo.options[campoComodo.options.length] = new Option( tipo_comodo[i+1].firstChild.data, tipo_comodo[i].firstChild.data,false,false);
		}
	}
	else
	{
		campoComodo.length = 1;
		campoComodo.options[0] = new Option( 'A escola não possui nenhuma Sala', '', false, false );
	}
}

function getTipoTurma()
{
	//setTimeout( function() {
		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
		var campoTipoTurma = document.getElementById('ref_cod_turma_tipo');
		campoTipoTurma.disabled = true;

		campoTipoTurma.length = 1;
		campoTipoTurma.options[0] = new Option( 'Selecione um tipo de turma', '', false, false );

		var xml1 = new ajax(atualizaTurmaCad_TipoTurma);
		strURL = "educar_tipo_turma_xml.php?ins="+campoInstituicao;
		xml1.envia(strURL);

//		DOM_execute_when_xmlhttpChange = function() { atualizaTurmaCad_TipoTurma(); };
//		strURL = "educar_tipo_turma_xml.php?ins="+campoInstituicao;
//		DOM_loadXMLDoc( strURL );
	//}, 1500 );

//	for (var j = 0; j < tipo_turma.length; j++)
//	{
//		if (tipo_turma[j][2] == campoEscola)
//		{
//			campoTipoTurma.options[campoTipoTurma.options.length] = new Option( tipo_turma[j][1], tipo_turma[j][0],false,false);
//		}
//	}
//	if ( campoTipoTurma.length == 1 && campoEscola != '' )
//	{
//			campoTipoTurma.options[0] = new Option( 'A instituição não possui nenhum tipo de turma', '', false, false );
//	}
}

function atualizaTurmaCad_TipoTurma(xml)
{
	var tipo_turma = xml.getElementsByTagName( "item" );
	var campoTipoTurma = document.getElementById('ref_cod_turma_tipo');
	campoTipoTurma.disabled = false;

	if(tipo_turma.length)
	{
		for(var i = 0; i < tipo_turma.length; i+=2 )
		{
			campoTipoTurma.options[campoTipoTurma.options.length] = new Option( tipo_turma[i+1].firstChild.data, tipo_turma[i].firstChild.data,false,false);
		}
	}
	else
	{
		campoTipoTurma.length = 1;
		campoTipoTurma.options[0] = new Option( 'A instituição não possui nenhum Tipo de Turma', '', false, false );
	}
}

function getModulo()
{
	var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
	var campoEscola = document.getElementById('ref_cod_instituicao').value;
	var campoModulo = document.getElementById('ref_cod_modulo');

	/*campoModulo.length = 1;
	campoModulo.options[0] = new Option( 'Selecione um módulo', '', false, false );
	for (var j = 0; j < modulo.length; j++)
	{
		if (modulo[j][2] == campoInstituicao)
		{
			campoModulo.options[campoModulo.options.length] = new Option( modulo[j][1], modulo[j][0],false,false);
		}
	}
	if ( campoModulo.length == 1 && campoInstituicao != '' )
	{
			campoModulo.options[0] = new Option( 'A Instituição não possui nenhum módulo', '', false, false );
	}*/

	//var campoInstituicao = $('ref_cod_instituicao').value;

	var url = "educar_modulo_instituicao_xml.php";
	var pars = '?inst=' + campoInstituicao;

	var xml1 = new ajax(getModulo_xml);
	strURL = url+pars;
	xml1.envia(strURL);

//	var myAjax = new Ajax.Request(
//		url,
//		{
//			method: 'get',
//			parameters: pars,
//			onComplete: getModulo_xml
//		});
	//setTimeout( function() {
//	DOM_execute_when_xmlhttpChange = function() { getModulo_xml(); };
//	strURL = "educar_modulo_instituicao_xml.php?inst="+campoInstituicao;
//	DOM_loadXMLDoc( strURL );
	//}
	//,1500);
}

function getModulo_xml(xml)
{

	var campoModulo = document.getElementById('ref_cod_modulo');
	var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
	campoModulo.length = 1;
	campoModulo.options[0] = new Option( 'Selecione um módulo', '', false, false );

	var DOM_modulos = xml.getElementsByTagName( "item" );

	for (var j = 0; j < DOM_modulos.length; j+=2)
	{

		campoModulo.options[campoModulo.options.length] = new Option( DOM_modulos[j+1].firstChild.nodeValue, DOM_modulos[j].firstChild.nodeValue,false,false);

	}
	if ( campoModulo.length == 1 && campoInstituicao != '' )
	{
			campoModulo.options[0] = new Option( 'A Instituição não possui nenhum módulo', '', false, false );
	}
}
// SE PADRAO_ANO_ESCOLAR == 1

var evtOnLoad =function()
{

setVisibility('tr_hora_inicial',false);
setVisibility('tr_hora_final',false);
setVisibility('tr_hora_inicio_intervalo',false);
setVisibility('tr_hora_fim_intervalo',false);

// SE PADRAO_ANO_ESCOLAR == 0
	// INCLUI MODULO
setVisibility('tr_ref_cod_modulo',false);
setVisibility('ref_cod_modulo',false);
setVisibility('tr_data_inicio',false);
setVisibility('tr_data_fim',false);
setVisibility('tr_bt_incluir_modulo',false);

// SE PADRAO_ANO_ESCOLAR == 0
	// INCLUI DIA SEMANA
setVisibility('tr_dia_semana',false);
setVisibility('tr_ds_hora_inicial',false);
setVisibility('tr_ds_hora_final',false);
setVisibility('tr_bt_incluir_dia_semana',false);

if(!document.getElementById('ref_ref_cod_serie').value){
	setVisibility('tr_multiseriada',false);
	setVisibility('tr_ref_ref_cod_serie_mult',document.getElementById('multiseriada').checked ? true : false);
	setVisibility('ref_ref_cod_serie_mult',document.getElementById('multiseriada').checked ? true : false);

}else
{

	if(document.getElementById('multiseriada').checked){
		changeMultiSerie();
		document.getElementById('ref_ref_cod_serie_mult').value = document.getElementById('ref_ref_cod_serie_mult_').value;
	}else
	{

		setVisibility('tr_ref_ref_cod_serie_mult',document.getElementById('multiseriada').checked ? true : false);
		setVisibility('ref_ref_cod_serie_mult',document.getElementById('multiseriada').checked ? true : false);
	}

}

// HIDE quebra de linha

var hr_tag = document.getElementsByTagName('hr');
for(var ct = 0;ct <hr_tag.length;ct++)
{
	setVisibility(hr_tag[ct].parentNode.parentNode,false);
}


if ( document.getElementById('ref_cod_curso').value )
{
	/*var aux;
	var inc = 0;
	if(document.getElementById('ref_cod_escola').value)
	{

		aux = escola_curso;
	}
	else
	{
		aux = curso;
		inc = 1;
	}*/


//	for (var j = 0; j < aux.length; j++)
	///{
		//if (aux[j][0] == document.getElementById('ref_cod_curso').value)
		//{
			if (document.getElementById('padrao_ano_escolar').value /*aux[j][3 + inc]*/ == 1)
			{
				setVisibility('tr_hora_inicial',true);
				setVisibility('tr_hora_final',true);
				setVisibility('tr_hora_inicio_intervalo',true);
				setVisibility('tr_hora_fim_intervalo',true);
			}
			else if (document.getElementById('padrao_ano_escolar').value /*aux[j][3 + inc]*/ == 0)
			{
				setVisibility('tr_ref_cod_modulo',true);
				setVisibility('ref_cod_modulo',true);
				setVisibility('tr_data_inicio',true);
				setVisibility('tr_data_fim',true);
				setVisibility('tr_bt_incluir_modulo',true);

				setVisibility('tr_dia_semana',true);
				setVisibility('tr_ds_hora_inicial',true);
				setVisibility('tr_ds_hora_final',true);
				setVisibility('tr_bt_incluir_dia_semana',true);

				// SHOW quebra de linha
				var hr_tag = document.getElementsByTagName('hr');
				for(var ct = 0;ct <hr_tag.length;ct++)
				{
					setVisibility(hr_tag[ct].parentNode.parentNode,true);
				}
			}
		//}
	//}
}

}

if( window.addEventListener ) {
	//mozilla
  window.addEventListener('load',evtOnLoad,false);
} else if ( window.attachEvent ) {
	//ie
  window.attachEvent('onload',evtOnLoad);
}
before_getEscola = function()
{

	getModulo();
	getTipoTurma();
	//hideMultiSerie();
	//PadraoAnoEscolar(null);
	document.getElementById('ref_cod_escola').onchange();
}

document.getElementById('ref_cod_escola').onchange = function()
{
	getEscolaCurso();
	getComodo();
	changeMultiSerie();
	getEscolaCursoSerie();
	PadraoAnoEscolar(null);
	changeMultiSerie();
	hideMultiSerie();
	if(document.getElementById('ref_cod_escola').value == '')
	{
		getCurso();
	}
	$('img_colecao').style.display = 'none;';
	if ($F('ref_cod_instituicao') == '')
	{
		$('img_turma').style.display = 'none;';
	}
	else
	{
		$('img_turma').style.display = '';
	}
//	getModulo();
}

//before_getEscola = function() { getTipoTurma(); }

document.getElementById('ref_cod_curso').onchange = function()
{
	setVisibility('tr_multiseriada',document.getElementById('ref_ref_cod_serie').value ? true : false);
	setVisibility('tr_ref_ref_cod_serie_mult',document.getElementById('multiseriada').checked ? true : false);
	setVisibility('ref_ref_cod_serie_mult',document.getElementById('multiseriada').checked ? true : false);

	hideMultiSerie();
	getEscolaCursoSerie();
	//if(this.value)
	//{

	PadraoAnoEscolar_xml();
		//PadraoAnoEscolar();
	//}
	if (this.value == '')
	{
		$('img_colecao').style.display = 'none;';
	}
	else
	{
		$('img_colecao').style.display = '';
	}
}

function PadraoAnoEscolar_xml()
{
	//if(document.getElementById('ref_cod_escola').value)
	//{
		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
		var xml1 = new ajax(PadraoAnoEscolar);
		strURL = "educar_curso_xml.php?ins="+campoInstituicao;
		xml1.envia(strURL);
	//}
	//else{
		//PadraoAnoEscolar(null);
	//}
}
function changeMultiSerie()
{

		var campoCurso = document.getElementById('ref_cod_curso').value;
		var campoSerie = document.getElementById('ref_ref_cod_serie').value;

		var xml1 = new ajax(atualizaMultiSerie);
		strURL = "educar_sequencia_serie_xml.php?cur="+campoCurso+"&ser_dif="+campoSerie;
		xml1.envia(strURL);

//		DOM_execute_when_xmlhttpChange = function() { atualizaMultiSerie(); };
//		strURL = "educar_sequencia_serie_xml.php?cur="+campoCurso+"&ser_dif="+campoSerie;
//		DOM_loadXMLDoc( strURL );
}

function atualizaMultiSerie(xml)
{

	var campoMultiSeriada = document.getElementById('multiseriada');
	var checked = campoMultiSeriada.checked;

	setVisibility('tr_ref_ref_cod_serie_mult', (document.getElementById('multiseriada').checked == true && document.getElementById('ref_ref_cod_serie').value != "")   ? true : false);
	setVisibility('ref_ref_cod_serie_mult', (document.getElementById('multiseriada').checked == true && document.getElementById('ref_ref_cod_serie').value != "")   ? true : false);

	if(!checked){
		document.getElementById('ref_ref_cod_serie_mult').value = '';
		return;
	}


	var campoEscola = document.getElementById('ref_cod_escola').value;
	var campoCurso  = document.getElementById('ref_cod_curso').value;
	var campoSerieMult	= document.getElementById('ref_ref_cod_serie_mult');
	var campoSerie	= document.getElementById('ref_ref_cod_serie');

	campoSerieMult.length = 1;
	campoSerieMult.options[0] = new Option( 'Selecione uma série', '', false, false );
	/*for (var j = 0; j < serie.length; j++)
	{
		if ((serie[j][2] == campoEscola) && (serie[j][3] == campoCurso) && (serie[j][0] != campoSerie.value))
		{
			campoSerieMult.options[campoSerieMult.options.length] = new Option( serie[j][1], serie[j][0],false,false);
		}
	}	*/
	var multi_serie = xml.getElementsByTagName( "serie" );
	if(multi_serie.length)
	{
		for(var i = 0; i < multi_serie.length; i++ )
		{
			campoSerieMult.options[campoSerieMult.options.length] = new Option( multi_serie[i].firstChild.data, multi_serie[i].getAttribute("cod_serie"),false,false);
		}
	}


	if ( campoSerieMult.length == 1 && campoCurso != '' ) {
		campoSerieMult.options[0] = new Option( 'O curso não possui nenhuma série', '', false, false );
	}

	document.getElementById('ref_ref_cod_serie_mult').value = document.getElementById('ref_ref_cod_serie_mult_').value;


}

document.getElementById('multiseriada').onclick = function()
{
	changeMultiSerie();
}

document.getElementById('ref_ref_cod_serie').onchange = function()
{


	if(this.value)
	{
		getHoraEscolaSerie();
	}
	if(document.getElementById('multiseriada').checked == true)
	{
		changeMultiSerie();
	}
	hideMultiSerie();
}

function hideMultiSerie()
{
	setVisibility('tr_multiseriada',document.getElementById('ref_ref_cod_serie').value != "" ? true : false);
	setVisibility('ref_ref_cod_serie_mult',   (document.getElementById('multiseriada').checked == true && document.getElementById('ref_ref_cod_serie').value != "")  ? true : false);
	setVisibility('tr_ref_ref_cod_serie_mult',(document.getElementById('multiseriada').checked == true && document.getElementById('ref_ref_cod_serie').value != "")  ? true : false);
}
function PadraoAnoEscolar(xml)
{
//	esconde tudo

	var escola_curso_ = new Array();
	if(xml != null)
		escola_curso_ = xml.getElementsByTagName( "curso" );

	campoCurso = document.getElementById('ref_cod_curso').value;
	for (var j = 0; j < escola_curso_.length; j++)
	{
		if (escola_curso_[j].getAttribute('cod_curso')  == campoCurso)
		{
			document.getElementById('padrao_ano_escolar').value = escola_curso_[j].getAttribute('padrao_ano_escolar') ;
		}
	}
	setVisibility('tr_ref_cod_modulo',false);
	setVisibility('ref_cod_modulo',false);
	setVisibility('tr_data_inicio',false);
	setVisibility('tr_data_fim',false);
	setVisibility('tr_bt_incluir_modulo',false);

	var modulos = document.getElementsByName('tr_ref_cod_modulo');
	for (var i = 0; i < modulos.length; i++)
	{
		setVisibility(modulos[i].id,false);
	}

	setVisibility('tr_dia_semana',false);
	setVisibility('tr_ds_hora_inicial',false);
	setVisibility('tr_ds_hora_final',false);
	setVisibility('tr_bt_incluir_dia_semana',false);

	if ( document.getElementById('tr_dia_semana_1') )
		setVisibility('tr_dia_semana_1',false);
	if ( document.getElementById('tr_dia_semana_2') )
		setVisibility('tr_dia_semana_2',false);
	if ( document.getElementById('tr_dia_semana_3') )
		setVisibility('tr_dia_semana_3',false);
	if ( document.getElementById('tr_dia_semana_4') )
		setVisibility('tr_dia_semana_4',false);
	if ( document.getElementById('tr_dia_semana_5') )
		setVisibility('tr_dia_semana_5',false);
	if ( document.getElementById('tr_dia_semana_6') )
		setVisibility('tr_dia_semana_6',false);
	if ( document.getElementById('tr_dia_semana_7') )
		setVisibility('tr_dia_semana_7',false);

	setVisibility('tr_hora_inicial',false);
	setVisibility('tr_hora_final',false);
	setVisibility('tr_hora_inicio_intervalo',false);
	setVisibility('tr_hora_fim_intervalo',false);

	if(campoCurso == ""){
		return;
	}

	var campoCurso = document.getElementById('ref_cod_curso').value;
	if(document.getElementById('padrao_ano_escolar').value == 1 )
	{
		setVisibility('tr_hora_inicial',true);
		setVisibility('tr_hora_final',true);
		setVisibility('tr_hora_inicio_intervalo',true);
		setVisibility('tr_hora_fim_intervalo',true);
	}
	else if(document.getElementById('padrao_ano_escolar').value == 0 )
	{
		setVisibility('tr_ref_cod_modulo',true);
		setVisibility('ref_cod_modulo',true);
		setVisibility('tr_data_inicio',true);
		setVisibility('tr_data_fim',true);
		setVisibility('tr_bt_incluir_modulo',true);

		var modulos = document.getElementsByName('tr_ref_cod_modulo');
		for (var i = 0; i < modulos.length; i++)
		{
			setVisibility(modulos[i].id,true);
		}

		setVisibility('tr_dia_semana',true);
		setVisibility('tr_ds_hora_inicial',true);
		setVisibility('tr_ds_hora_final',true);
		setVisibility('tr_bt_incluir_dia_semana',true);

		if ( document.getElementById('tr_dia_semana_1') )
			setVisibility('tr_dia_semana_1',true);
		if ( document.getElementById('tr_dia_semana_2') )
			setVisibility('tr_dia_semana_2',true);
		if ( document.getElementById('tr_dia_semana_3') )
			setVisibility('tr_dia_semana_3',true);
		if ( document.getElementById('tr_dia_semana_4') )
			setVisibility('tr_dia_semana_4',true);
		if ( document.getElementById('tr_dia_semana_5') )
			setVisibility('tr_dia_semana_5',true);
		if ( document.getElementById('tr_dia_semana_6') )
			setVisibility('tr_dia_semana_6',true);
		if ( document.getElementById('tr_dia_semana_7') )
			setVisibility('tr_dia_semana_7',true);
	}
}

function getHoraEscolaSerie()
{
	var campoEscola = document.getElementById('ref_cod_escola').value;
	var campoSerie	= document.getElementById('ref_ref_cod_serie').value;

	var xml1 = new ajax(atualizaTurmaCad_EscolaSerie);
	strURL = "educar_escola_serie_hora_xml.php?esc="+campoEscola+"&ser="+campoSerie;
	xml1.envia(strURL);

//	DOM_execute_when_xmlhttpChange = function() { atualizaTurmaCad_EscolaSerie(); };
//	strURL = "educar_escola_serie_hora_xml.php?esc="+campoEscola+"&ser="+campoSerie;
//	DOM_loadXMLDoc( strURL );

//	for (var j = 0; j < serie.length; j++)
//	{
//		if ( (serie[j][2] == campoEscola) && (serie[j][0] == campoSerie) )
//		{
//			campoHoraInicial.value = serie[j][4];
//			campoHoraFinal.value = serie[j][5];
//			campoHoraInicioIntervalo.value = serie[j][6];
//			campoHoraFimIntervalo.value = serie[j][7];
//		}
//	}
}

function atualizaTurmaCad_EscolaSerie(xml)
{
	var campoHoraInicial = document.getElementById('hora_inicial');
	var campoHoraFinal = document.getElementById('hora_final');
	var campoHoraInicioIntervalo = document.getElementById('hora_inicio_intervalo');
	var campoHoraFimIntervalo = document.getElementById('hora_fim_intervalo');

	var DOM_escola_serie_hora = xml.getElementsByTagName( "item" );

	if(DOM_escola_serie_hora.length)
	{
		campoHoraInicial.value 			= DOM_escola_serie_hora[0].firstChild.data;
		campoHoraFinal.value 			= DOM_escola_serie_hora[1].firstChild.data;
		campoHoraInicioIntervalo.value 	= DOM_escola_serie_hora[2].firstChild.data;
		campoHoraFimIntervalo.value 	= DOM_escola_serie_hora[3].firstChild.data;
	}
}

function valida()
{
	if( document.getElementById('padrao_ano_escolar').value == 1 )
	{
		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
		var campoEscola = document.getElementById('ref_cod_escola').value;
		var campoTurma = document.getElementById('cod_turma').value;
		var campoComodo = document.getElementById('ref_cod_infra_predio_comodo').value;
		var campoCurso = document.getElementById('ref_cod_curso').value;
		var campoSerie = document.getElementById('ref_ref_cod_serie').value;

		var url = "educar_turma_sala_xml.php";
		var pars = '?inst=' + campoInstituicao + '&esc=' + campoEscola + '&not_tur=' + campoTurma + '&com=' + campoComodo + '&cur=' + campoCurso+ '&ser=' + campoSerie;
//
//		var myAjax = new Ajax.Request(
//			url,
//			{
//				method: 'get',
//				parameters: pars,
//				onComplete: valida_xml
//			});

		var xml1 = new ajax(valida_xml);
		strURL = url+pars;
		xml1.envia(strURL);

	}else{
	valida_xml(null);
	}

}

function valida_xml(xml)
{
	var DOM_turma_sala = new Array();
	if(xml != null)
		DOM_turma_sala = xml.getElementsByTagName( "item" );

	var campoCurso = document.getElementById('ref_cod_curso').value;

	if(document.getElementById('ref_cod_escola').value)
	{

		if(!document.getElementById('ref_ref_cod_serie').value)
		{
			alert("Preencha o campo 'Série' corretamente!");
			document.getElementById('ref_ref_cod_serie').focus();
			return false;
		}
		if(!document.getElementById('ref_cod_infra_predio_comodo').value)
		{
			alert("Preencha o campo 'Sala' corretamente!");
			document.getElementById('ref_cod_infra_predio_comodo').focus();
			return false;
		}
	}
	if(document.getElementById('multiseriada').checked )
	{
		if(!document.getElementById('ref_ref_cod_serie_mult')){
			alert("Preencha o campo 'Série Multi-seriada' corretamente!");
			document.getElementById('ref_ref_cod_serie_mult').focus();
			return false;
		}
	}
	//for (var j = 0; j < escola_curso.length; j++)
	//{
		//if (escola_curso[j][0] == campoCurso)
		//{

			if (document.getElementById('padrao_ano_escolar').value == 1 /*escola_curso[j][3] == 1*/)
			{
				var campoHoraInicial = document.getElementById('hora_inicial').value;
				var campoHoraFinal = document.getElementById('hora_final').value;
				var campoHoraInicioIntervalo = document.getElementById('hora_inicio_intervalo').value;
				var campoHoraFimIntervalo = document.getElementById('hora_fim_intervalo').value;

				if ( campoHoraInicial == '' )
				{
					alert("Preencha o campo 'Hora Inicial' corretamente!");
					document.getElementById('hora_inicial').focus();
					return false;
				}
				else if ( campoHoraFinal == '' )
				{
					alert("Preencha o campo 'Hora Final' corretamente!");
					document.getElementById('hora_final').focus();
					return false;
				}
				else if ( campoHoraInicioIntervalo == '' )
				{
					alert("Preencha o campo 'Hora Início Intervalo' corretamente!");
					document.getElementById('hora_inicio_intervalo').focus();
					return false;
				}
				else if ( campoHoraFimIntervalo == '' )
				{
					alert("Preencha o campo 'Hora Fim Intervalo' corretamente!");
					document.getElementById('hora_fim_intervalo').focus();
					return false;
				}
				////document.getElementById('padrao_ano_escolar').value = 1;
				//break;
			}
			else if (document.getElementById('padrao_ano_escolar').value == 0 /*escola_curso[j][3] == 0*/)
			{
				var qtdModulo = document.getElementsByName('ref_cod_modulo').length;
				var qtdDiaSemana = document.getElementsByName('dia_semana').length;

				if ( qtdModulo == 1 )
				{
					alert("ATENÇÂO! \n É necessário incluir um 'Módulo'!");
					document.getElementById('ref_cod_modulo').focus();
					return false;
				}
				if ( qtdDiaSemana == 1 )
				{
					alert("ATENÇÂO! \n É necessário incluir um 'Dia da Semana'!");
					document.getElementById('dia_semana').focus();
					return false;
				}
				//document.getElementById('padrao_ano_escolar').value = 0;
				//break;
			}
		//}
	//}

	if( document.getElementById('padrao_ano_escolar') == 1 )
		//alert('Erro ao verificar informações!!\nContate o administrador');
	{//ref_cod_infra_predio_comodo
		for (var j = 0; j < DOM_turma_sala.length; j+=2)
		{

				if( (DOM_turma_sala[j].firstChild.nodeValue <= document.getElementById('hora_inicial').value) &&
					(document.getElementById('hora_inicial').value <= DOM_turma_sala[j+1].firstChild.nodeValue)
					||
					(DOM_turma_sala[j].firstChild.nodeValue <= document.getElementById('hora_final').value) &&
					(document.getElementById('hora_final').value <= DOM_turma_sala[j+1].firstChild.nodeValue) )
				{
					alert("ATENÇÂO! \n A 'sala' já está alocada nesse horário! \n Por favor, escolha outro horário ou sala.");
					return false;
				}

		}
	}
/*		for (var j = 0; j < DOM_turma_sala.length; j++)
		{
			if( (document.getElementById('ref_cod_escola').value == turma_sala[j][2])
				&&
				(document.getElementById('ref_cod_infra_predio_comodo').value == turma_sala[j][1])
				&& document.getElementById('cod_turma').value != turma_sala[j][0]
				)
			{
				if( (turma_sala[j][3] <= document.getElementById('hora_inicial').value) &&
					(document.getElementById('hora_inicial').value <= turma_sala[j][4])
					||
					(turma_sala[j][3] <= document.getElementById('hora_final').value) &&
					(document.getElementById('hora_final').value <= turma_sala[j][4]) )
				{
					alert("ATENÇÂO! \n A 'sala' já está alocada nesse horário! \n Por favor, escolha outro horário ou sala.");
					return false;
				}
			}
		}*/
//	}

	if( !acao() )
		return false;

	document.forms[0].submit();
}



function validaCampoServidor()
{
	if ( document.getElementById( "ref_cod_instituicao" ).value )
		ref_cod_instituicao = document.getElementById( "ref_cod_instituicao" ).value;
	else
	{
		alert('Selecione uma instituição');
		return false;
	}
	if ( document.getElementById( "ref_cod_escola" ).value )
		ref_cod_escola = document.getElementById( "ref_cod_escola" ).value;
	else
	{
		alert('Selecione uma escola');
		return false;
	}

	pesquisa_valores_popless( 'educar_pesquisa_servidor_lst.php?campo1=ref_cod_regente&professor=1&ref_cod_servidor=0&ref_cod_instituicao=' + ref_cod_instituicao + '&ref_cod_escola=' + ref_cod_escola , 'ref_cod_servidor' );
}

document.getElementById( 'ref_cod_regente_lupa' ).onclick = function()
{
	validaCampoServidor();
}


/**************************************COLOCADO*****************************************/
function getEscolaCursoSerie()
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
		var campoSerie	= document.getElementById('ref_ref_cod_serie');
		campoSerie.length = 1;

		limpaCampos(4);
		if( campoEscola && campoCurso )
		{
			campoSerie.disabled = true;
			campoSerie.options[0].text = 'Carregando séries';
			var xml = new ajax(atualizaLstEscolaCursoSerie);
			xml.envia("educar_escola_curso_serie_xml.php?esc="+campoEscola+"&cur="+campoCurso);
		}
		else
		{
			campoSerie.options[0].text = 'Selecione';
		}
	}

	function atualizaLstEscolaCursoSerie(xml)
	{
		var campoSerie = document.getElementById('ref_ref_cod_serie');
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
			campoSerie.options[0].text = 'A escola/curso não possui nenhuma série';
		}
	}
		
/**************************************************************************************/

</script>