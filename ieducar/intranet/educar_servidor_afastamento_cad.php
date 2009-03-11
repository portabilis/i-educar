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
/**
 * @author Adriano Erik Weiguert Nagasava
 */
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Servidor Afastamento" );
		$this->processoAp = "635";
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

	var $ref_cod_servidor;
	var $sequencial;
	var $ref_cod_instituicao;
	var $ref_cod_motivo_afastamento;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $data_cadastro;
	var $data_exclusao;
	var $data_retorno;
	var $data_saida;
	var $ativo;
	var $status;
	var $alocacao_array;
	var $dias_da_semana = array( '' => 'Selecione', 1 => 'Domingo', 2 => 'Segunda', 3 => 'Ter&ccedil;a', 4 => 'Quarta', 5 => 'Quinta', 6 => 'Sexta', 7 => 'S&aacute;bado' );
	var $parametros;

	function Inicializar()
	{
		$retorno = "Novo";
		$this->status = "N";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->ref_cod_instituicao = $_GET["ref_cod_instituicao"];
		$this->ref_cod_servidor	   = $_GET["ref_cod_servidor"];
		$this->sequencial		   = $_GET["sequencial"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 7,  "educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );

		if( is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_cod_instituicao ) )
		{
			$obj = new clsPmieducarServidorAfastamento( $this->ref_cod_servidor, $this->sequencial, null, null, null, null, null, null, null, 1, $this->ref_cod_instituicao );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;
				if ( $this->data_retorno )
					$this->data_retorno = dataFromPgToBr( $this->data_retorno );
				if ( $this->data_saida )
					$this->data_saida   = dataFromPgToBr( $this->data_saida );

			$obj_permissoes = new clsPermissoes();
			if( $obj_permissoes->permissao_excluir( 635, $this->pessoa_logada, 7 ) )
			{
				//$this->fexcluir = true;
			}

				$retorno = "Editar";
				$this->status = "E";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" : "educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "ref_cod_servidor", $this->ref_cod_servidor );
		$this->campoOculto( "sequencial", $this->sequencial );
		$this->campoOculto( "ref_cod_instituicao", $this->ref_cod_instituicao );

		// foreign keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarMotivoAfastamento" ) )
		{
			$objTemp = new clsPmieducarMotivoAfastamento();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_motivo_afastamento']}"] = "{$registro['nm_motivo']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarMotivoAfastamento nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		if ( $this->status == "N" )
			$this->campoLista( "ref_cod_motivo_afastamento", "Motivo Afastamento", $opcoes, $this->ref_cod_motivo_afastamento );
		elseif ( $this->status == "E" )
			$this->campoLista( "ref_cod_motivo_afastamento", "Motivo Afastamento", $opcoes, $this->ref_cod_motivo_afastamento, "", false, "", "", true );

		// text

		// data
		if ( $this->status == "N" )
			$this->campoData( "data_saida", "Data de Afastamento", $this->data_saida, true );
		elseif ( $this->status == "E" )
			$this->campoRotulo( "data_saida", "Data de Afastamento", $this->data_saida );

		if ( $this->status == "E" )
			$this->campoData( "data_retorno", "Data de Retorno", $this->data_retorno, false );
		if ( "clsPmieducarServidor" ) {
			$obj_servidor = new clsPmieducarServidor( $this->ref_cod_servidor, null, null, null, null, null, 1, $this->ref_cod_instituicao );
			$det_servidor = $obj_servidor->detalhe();
			if ( $det_servidor ) {
				if ( "clsPmieducarFuncao" ) {
					$obj_funcao = new clsPmieducarFuncao( $det_servidor["ref_cod_funcao"], null, null, null, null, null, null, null, 1, $this->ref_cod_instituicao );
					$det_funcao = $obj_funcao->detalhe();
					if ( $det_funcao["professor"] == 1 )
					{
						//$obj = new clsPmieducarQuadroHorarioHorarios( null, null, null, null, null, null, null, $this->ref_cod_instituicao, null, $this->ref_cod_servidor, null, null, null, null, 1, null );
						//$obj = new clsPmieducarServidorAlocacao();
						$obj = new clsPmieducarQuadroHorarioHorarios();
						$lista = $obj->lista( null, null, null, null, null, null, null, $this->ref_cod_instituicao, null, $this->ref_cod_servidor, null, null, null, null, null, null, null, null, 1, null );
						//$lista  = $obj->lista( null, $this->ref_cod_instituicao, null, null, null, $this->ref_cod_servidor, null, null, null, null, null, null, null, null, null, 1 );
						if( $lista )
						{
							foreach( $lista AS $campo => $val ){	// passa todos os valores obtidos no registro para atributos do objeto
								$temp = array();
								$temp['hora_inicial'] = $val['hora_inicial'];
								$temp['hora_final'] = $val['hora_final'];
								$temp['dia_semana'] = $val['dia_semana'];
								$temp['ref_cod_escola'] = $val['ref_cod_escola'];
								$temp['ref_cod_substituto'] = $val['ref_servidor_substituto'] ;
								$this->alocacao_array[] = $temp;
							}
							if ( $this->alocacao_array ){
								$tamanho = sizeof( $alocacao );
								$script  = "<script>\nvar num_alocacao = {$tamanho};\n";
								$script .= "var array_servidores = Array();\n";

								foreach ( $this->alocacao_array as $key => $alocacao ) {
									$script .= "array_servidores[{$key}] = new Array();\n";

									$hora_ini = explode(":",$alocacao['hora_inicial']);
									$hora_fim = explode(":",$alocacao['hora_final']);

									$horas_utilizadas = ( $hora_fim[0] -  $hora_ini[0] );
									$minutos_utilizados = ( $hora_fim[1] -  $hora_ini[1] );
									$horas = sprintf("%02d",(int)$horas_utilizadas);
									$minutos = sprintf("%02d",(int)$minutos_utilizados);
									$str_horas_utilizadas =  "{$horas}:{$minutos}";
									$script .= "array_servidores[{$key}][0] = '{$str_horas_utilizadas}'; \n";
									$script .= "array_servidores[{$key}][1] = ''; \n\n";


									$obj_escola = new clsPmieducarEscola($alocacao['ref_cod_escola']);
									$det_escola = $obj_escola->detalhe();
									$det_escola = $det_escola["nome"];
									$nm_dia_semana = $this->dias_da_semana[$alocacao["dia_semana"]];

									$obj_subst = new clsPessoa_( $alocacao["ref_cod_substituto"] );
									$det_subst = $obj_subst->detalhe();

									if($this->status == "N"){
										$this->campoTextoInv( "dia_semana_{$key}_", "", $nm_dia_semana, 8, 8, false, false, true,"","","","","dia_semana" );
										$this->campoTextoInv( "hora_inicial_{$key}_", "", $alocacao['hora_inicial'], 5, 5, false, false, true, "","","","","ds_hora_inicial_" );
										$this->campoTextoInv( "hora_final_{$key}_", "", $alocacao['hora_final'], 5, 5, false, false, true, "", "","","","ds_hora_final_" );
										$this->campoTextoInv( "ref_cod_escola_{$key}", "", $det_escola, 30, 255, false, false, true, "", "","","","ref_cod_escola_" );
										$this->campoTextoInv( "ref_cod_servidor_substituto_{$key}_", "", $det_subst["nome"], 30, 255, false, false, false, "", "<span name=\"ref_cod_servidor_substituto\" id=\"ref_cod_servidor_substituicao_{$key}\"><img border='0'  onclick=\"pesquisa_valores_popless('educar_pesquisa_servidor_lst.php?campo1=ref_cod_servidor_substituto[{$key}]&campo2=ref_cod_servidor_substituto_{$key}_&ref_cod_instituicao={$this->ref_cod_instituicao}&dia_semana={$alocacao["dia_semana"]}&hora_inicial={$alocacao["hora_inicial"]}&hora_final={$alocacao["hora_final"]}&ref_cod_servidor={$this->ref_cod_servidor}&professor=1&ref_cod_escola={$alocacao['ref_cod_escola']}&horario=S', 'nome')\" src=\"imagens/lupa.png\" ></span>","","","ref_cod_servidor_substituto" );
									}
									$this->campoOculto( "dia_semana_{$key}", $alocacao["dia_semana"] );
									$this->campoOculto( "hora_inicial_{$key}", $alocacao['hora_inicial'] );
									$this->campoOculto( "hora_final_{$key}", $alocacao['hora_final'] );
									$this->campoOculto( "ref_cod_escola_{$key}", $alocacao['ref_cod_escola'] );
									$this->campoOculto( "ref_cod_servidor_substituto[{$key}]", $alocacao["ref_cod_substituto"] );

								}

								/*nao esta sendo utilizado em lugar algum =o
								if($this->status == "N"){
									$db = new clsBanco();
									$consulta = "SELECT ref_cod_servidor
												        ,ref_ref_cod_instituicao
												        ,sum( carga_horaria ) as horas
												   FROM pmieducar.servidor_alocacao
												  WHERE ativo = 1
												  GROUP BY ref_cod_servidor,ref_ref_cod_instituicao";
									$registros = $db->Consulta($consulta);
									if($registros)
									{
										$ct = 0;
										$script .="array_horas_utilizadas_servidor = new Array();\n";

										while($db->ProximoRegistro()){
											$registro = $db->Tupla();

											$script .= "array_horas_utilizadas_servidor['{$registro['ref_cod_servidor']}_'] = new Array('{$registro['horas']}'); \n";
											$ct++;
										}
									}


									$consulta = "SELECT DISTINCT cod_servidor
												        ,ref_cod_instituicao
												        ,coalesce(s.carga_horaria , '0') as carga_horaria
												   FROM pmieducar.servidor s
												   		,pmieducar.servidor_alocacao a
												  WHERE s.ativo = 1
												    AND a.ativo = 1
												    AND s.ref_cod_instituicao = a.ref_ref_cod_instituicao";
									$registros = $db->Consulta( $consulta );
									if ( $registros ) {
										$ct = 0;
										$script .="array_horas_servidor = new Array();\n";

										while($db->ProximoRegistro()){
											$registro = $db->Tupla();
											$carga_horaria = $registro['carga_horaria'];
											$total_horas = sprintf("%02d",(int)floor($carga_horaria));
											$total_minutos = sprintf("%02d",(int)(floatval($carga_horaria) - floatval($total_horas)) * 60);
											$carga_horaria = "{$total_horas}:{$total_minutos}";//date("H:i",mktime($total_horas, $total_minutos, 0, 1, 1, 1970));
											$script .= "array_horas_servidor['{$registro['cod_servidor']}_'] = new Array('{$carga_horaria}'); \n";
											$ct++;
										}
									}

									$script .= "\n</script>";
									echo $script;
								}*/
								$script .= "\n</script>";
								echo $script;
							}
						}
					}
				}
			}
		}
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 7,  "educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );
//echo '<pre>';print_r($_POST);die;
		$obj = new clsPmieducarServidorAfastamento( $this->ref_cod_servidor, null, $this->ref_cod_motivo_afastamento, null, $this->pessoa_logada, null, null, $this->data_retorno, $this->data_saida, 1, $this->ref_cod_instituicao );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			if(is_array($_POST['ref_cod_servidor_substituto']))
				foreach ( $_POST['ref_cod_servidor_substituto'] as $key => $valor )
				{
					//if ( substr( $campo, 0, 28 ) == 'ref_cod_servidor_substituto_' )
				//	{
						$ref_cod_servidor_substituto = $valor;
				//	}
					//if ( substr( $campo, 0, 15 ) == 'ref_cod_escola_' )
						$ref_cod_escola = $_POST["ref_cod_escola_{$key}"];
					//if ( substr( $campo, 0, 11 ) == 'dia_semana_' )
						$dia_semana = $_POST["dia_semana_{$key}"];
					//if ( substr( $campo, 0, 13 ) == 'hora_inicial_' )
						$hora_inicial = urldecode( $_POST["hora_inicial_{$key}"] );
				//	if ( substr( $campo, 0, 11 ) == 'hora_final_' )
						$hora_final = urldecode( $_POST["hora_final_{$key}"] );

					if ( is_numeric( $ref_cod_servidor_substituto ) && is_numeric( $ref_cod_escola ) && is_numeric( $dia_semana ) && is_string( $hora_inicial ) && is_string( $hora_final ) )
					{

						//if ( substr( $campo, 0, 28 ) == 'ref_cod_servidor_substituto_' )
						//{die;
							$obj_horarios = new clsPmieducarQuadroHorarioHorarios( null, null, $ref_cod_escola,null, null,null, $this->ref_cod_instituicao,$ref_cod_servidor_substituto, $this->ref_cod_servidor, $hora_inicial, $hora_final, null, null, 1, $dia_semana );
							$det_horarios = $obj_horarios->detalhe($ref_cod_escola);
							//echo " = new clsPmieducarQuadroHorarioHorarios( {$det_horarios["ref_cod_quadro_horario"]}, {$det_horarios["ref_cod_serie"]}, {$det_horarios["ref_cod_escola"]}, {$det_horarios["ref_cod_disciplina"]}, {$det_horarios["ref_ref_cod_turma"]}, {$det_horarios["sequencial"]}, {$det_horarios["ref_cod_instituicao_servidor"]}, null, {$ref_cod_servidor_substituto}, null, null, null, null, null, null, null );";die;
							$obj_horario = new clsPmieducarQuadroHorarioHorarios( $det_horarios["ref_cod_quadro_horario"], $det_horarios["ref_cod_serie"], $det_horarios["ref_cod_escola"], $det_horarios["ref_cod_disciplina"], $det_horarios["sequencial"], $det_horarios["ref_cod_instituicao_servidor"],$det_horarios["ref_cod_instituicao_servidor"], $ref_cod_servidor_substituto, $this->ref_cod_servidor, null, null, null, null, null, null );
							if( !$obj_horario->edita() )
							{
								$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
								return false;
							}
						//}
					}
				}
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );
			die();
			return true;
		}
		else
		{
			$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
			return false;
		}
		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarServidorAfastamento\nvalores obrigatorios\nis_numeric( $this->ref_cod_servidor ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_ref_cod_instituicao ) && is_numeric( $this->ref_cod_motivo_afastamento ) && is_numeric( $this->ref_usuario_cad ) && is_string( $this->data_saida )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 7,  "educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );

		$obj = new clsPmieducarServidorAfastamento( $this->ref_cod_servidor, $this->sequencial, $this->ref_cod_motivo_afastamento, $this->pessoa_logada, null, null, null, $this->data_retorno, unserialize( $this->data_saida ), 1, $this->ref_cod_instituicao );
		$editou = $obj->edita();
		if( $editou )
		{
			if(is_array($_POST['ref_cod_servidor_substituto']))
				foreach ( $_POST['ref_cod_servidor_substituto'] as $key => $valor )
				{
						$ref_cod_servidor_substituto = $valor;

					//if ( substr( $campo, 0, 15 ) == 'ref_cod_escola_' )
						$ref_cod_escola = $_POST["ref_cod_escola_{$key}"];
					//if ( substr( $campo, 0, 11 ) == 'dia_semana_' )
						$dia_semana = $_POST["dia_semana_{$key}"];
					//if ( substr( $campo, 0, 13 ) == 'hora_inicial_' )
						$hora_inicial = urldecode( $_POST["hora_inicial_{$key}"] );
				//	if ( substr( $campo, 0, 11 ) == 'hora_final_' )
						$hora_final = urldecode( $_POST["hora_final_{$key}"] );

					if ( is_numeric( $ref_cod_servidor_substituto ) && is_numeric( $ref_cod_escola ) && is_numeric( $dia_semana ) && is_string( $hora_inicial ) && is_string( $hora_final ) )
					{
						//if ( substr( $campo, 0, 28 ) == 'ref_cod_servidor_substituto_' )
						//{
							$obj_horarios = new clsPmieducarQuadroHorarioHorarios( null, null, $ref_cod_escola,null, null,null, $this->ref_cod_instituicao,$ref_cod_servidor_substituto, $this->ref_cod_servidor, $hora_inicial, $hora_final, null, null, 1, $dia_semana );
							$det_horarios = $obj_horarios->detalhe($ref_cod_escola);
							//if ( is_string( $this->data_retorno ) && $this->data_retorno != '' )
							//{
								//$obj_horario = new clsPmieducarQuadroHorarioHorarios( $det_horarios["ref_cod_quadro_horario"], $det_horarios["ref_ref_cod_serie"], $det_horarios["ref_ref_cod_escola"], $det_horarios["ref_ref_cod_disciplina"], $det_horarios["ref_ref_cod_turma"], $det_horarios["sequencial"], null, null, null, null, null, null, null, null, null, null );
							$obj_horario = new clsPmieducarQuadroHorarioHorarios( $det_horarios["ref_cod_quadro_horario"], $det_horarios["ref_cod_serie"], $det_horarios["ref_cod_escola"], $det_horarios["ref_cod_disciplina"], $det_horarios["sequencial"], null,$det_horarios["ref_cod_instituicao_servidor"], null, $this->ref_cod_servidor, null, null, null, null, null, null );
							//}
						/*	else
							{
	//							$obj_horario = new clsPmieducarQuadroHorarioHorarios( $det_horarios["ref_cod_quadro_horario"], $det_horarios["ref_ref_cod_serie"], $det_horarios["ref_ref_cod_escola"], $det_horarios["ref_ref_cod_disciplina"], $det_horarios["ref_ref_cod_turma"], $det_horarios["sequencial"], $det_horarios["ref_cod_instituicao_servidor"], null, $ref_cod_servidor_substituto, null, null, null, null, null, null, null );
								$obj_horario = new clsPmieducarQuadroHorarioHorarios( $det_horarios["ref_cod_quadro_horario"], $det_horarios["ref_cod_serie"], $det_horarios["ref_cod_escola"], $det_horarios["ref_cod_disciplina"], $det_horarios["sequencial"], $det_horarios["ref_cod_instituicao_servidor"],$det_horarios["ref_cod_instituicao_servidor"], $ref_cod_servidor_substituto, $this->ref_cod_servidor, null, null, null, null, null, null );
							}*/
							if( !$obj_horario->edita() )
							{
								$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
								return false;
							}
						//}
					}
				}
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarServidorAfastamento\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_ref_cod_instituicao ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 635, $this->pessoa_logada, 7,  "educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" );


		$obj = new clsPmieducarServidorAfastamento($this->ref_cod_servidor, $this->sequencial, $this->ref_ref_cod_instituicao, $this->ref_cod_motivo_afastamento, $this->pessoa_logada, $this->pessoa_logada, $this->data_cadastro, $this->data_exclusao, $this->data_retorno, $this->data_saida, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_servidor_afastamento_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarServidorAfastamento\nvalores obrigatorios\nif( is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_ref_cod_instituicao ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
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
	if ( document.getElementById( 'btn_enviar' ) )
	{
		document.getElementById( 'btn_enviar' ).onclick = function() { validaFormulario(); }
	}
	function validaFormulario()
	{
		var c    = 0;
		var loop = true;
		do
		{
			if ( document.getElementById( 'ref_cod_servidor_substituto_' + c + '_' ) && document.getElementById( 'ref_cod_servidor_substituto_' + c ) )
			{
				if ( document.getElementById( 'ref_cod_servidor_substituto_' + c + '_' ).value == '' && document.getElementById( 'ref_cod_servidor_substituto_' + c ).value == '' )
				{
					alert( 'Você deve informar um substituto para cada horário.' );
					return;
				}
			}
			else
			{
				loop = false;
			}
			c++;
		} while ( loop );
		acao();
	}
</script>