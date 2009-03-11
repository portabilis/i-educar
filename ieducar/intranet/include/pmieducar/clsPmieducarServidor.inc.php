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
* @author Prefeitura Municipal de Itajaí
*
* Criado em 26/06/2006 16:19 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarServidor
{
	var $cod_servidor;
	var $ref_cod_deficiencia;
	var $ref_idesco;
	var $carga_horaria;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_instituicao;
	var $ref_cod_subnivel;

	// propriedades padrao

	/**
	 * Armazena o total de resultados obtidos na ultima chamada ao metodo lista
	 *
	 * @var int
	 */
	var $_total;

	/**
	 * Nome do schema
	 *
	 * @var string
	 */
	var $_schema;

	/**
	 * Nome da tabela
	 *
	 * @var string
	 */
	var $_tabela;

	/**
	 * Lista separada por virgula, com os campos que devem ser selecionados na proxima chamado ao metodo lista
	 *
	 * @var string
	 */
	var $_campos_lista;
	var $_campos_lista2;

	/**
	 * Lista com todos os campos da tabela separados por virgula, padrao para selecao no metodo lista
	 *
	 * @var string
	 */
	var $_todos_campos;
	var $_todos_campos2;

	/**
	 * Valor que define a quantidade de registros a ser retornada pelo metodo lista
	 *
	 * @var int
	 */
	var $_limite_quantidade;

	/**
	 * Define o valor de offset no retorno dos registros no metodo lista
	 *
	 * @var int
	 */
	var $_limite_offset;

	/**
	 * Define o campo padrao para ser usado como padrao de ordenacao no metodo lista
	 *
	 * @var string
	 */
	var $_campo_order_by;


	/**ord
	 * Construtor (PHP 4)
	 *
	 * @return object
	 */
	function clsPmieducarServidor( $cod_servidor = null, $ref_cod_deficiencia = null, $ref_idesco = null, $carga_horaria = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $ref_cod_instituicao = null, $ref_cod_subnivel = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}servidor";

		$this->_campos_lista = $this->_todos_campos = "cod_servidor, ref_cod_deficiencia, ref_idesco, carga_horaria, data_cadastro, data_exclusao, ativo, ref_cod_instituicao,ref_cod_subnivel";
		$this->_campos_lista2 = $this->_todos_campos2 = "s.cod_servidor, s.ref_cod_deficiencia, s.ref_idesco, s.carga_horaria, s.data_cadastro, s.data_exclusao, s.ativo, s.ref_cod_instituicao,s.ref_cod_subnivel";

		if( is_numeric( $ref_cod_deficiencia ) )
		{
			if( class_exists( "clsCadastroDeficiencia" ) )
			{
				$tmp_obj = new clsCadastroDeficiencia( $ref_cod_deficiencia );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_deficiencia = $ref_cod_deficiencia;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_deficiencia = $ref_cod_deficiencia;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM cadastro.deficiencia WHERE cod_deficiencia = '{$ref_cod_deficiencia}'" ) )
				{
					$this->ref_cod_deficiencia = $ref_cod_deficiencia;
				}
			}
		}
		if( is_numeric( $ref_idesco ) )
		{
			if( class_exists( "clsCadastroEscolaridade" ) )
			{
				$tmp_obj = new clsCadastroEscolaridade( $ref_idesco );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_idesco = $ref_idesco;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_idesco = $ref_idesco;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM cadastro.escolaridade WHERE idesco = '{$ref_idesco}'" ) )
				{
					$this->ref_idesco = $ref_idesco;
				}
			}
		}
		if( is_numeric( $cod_servidor ) )
		{
			if( class_exists( "clsFuncionario" ) )
			{
				$tmp_obj = new clsFuncionario( $cod_servidor );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->cod_servidor = $cod_servidor;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->cod_servidor = $cod_servidor;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM funcionario WHERE ref_cod_pessoa_fj = '{$cod_servidor}'" ) )
				{
					$this->cod_servidor = $cod_servidor;
				}
			}
		}


		if( is_numeric( $carga_horaria ) )
		{
			$this->carga_horaria = $carga_horaria;
		}
		if( is_string( $data_cadastro ) )
		{
			$this->data_cadastro = $data_cadastro;
		}
		if( is_string( $data_exclusao ) )
		{
			$this->data_exclusao = $data_exclusao;
		}
		if( is_numeric( $ativo ) )
		{
			$this->ativo = $ativo;
		}

		if( is_numeric( $ref_cod_instituicao ) )
		{
			if( class_exists( "clsPmieducarInstituicao" ) )
			{
				$tmp_obj = new clsPmieducarInstituicao( $ref_cod_instituicao );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_instituicao = $ref_cod_instituicao;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_instituicao = $ref_cod_instituicao;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.instituicao WHERE cod_instituicao = '{$ref_cod_instituicao}'" ) )
				{
					$this->ref_cod_instituicao = $ref_cod_instituicao;
				}
			}
		}

		if( is_numeric( $ref_cod_subnivel) )
		{
			if( class_exists( "clsPmieducarSubnivel" ) )
			{
				$tmp_obj = new clsPmieducarSubnivel( $ref_cod_subnivel );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_subnivel = $ref_cod_subnivel;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_subnivel = $ref_cod_subnivel;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.subnivel WHERE cod_subnivel = '{$ref_cod_subnivel}'" ) )
				{
					$this->ref_cod_subnivel = $ref_cod_subnivel;
				}
			}
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->cod_servidor ) && is_numeric( $this->carga_horaria ) && is_numeric( $this->ref_cod_instituicao ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";
			if( is_numeric( $this->cod_servidor ) )
			{
				$campos .= "{$gruda}cod_servidor";
				$valores .= "{$gruda}'{$this->cod_servidor}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_deficiencia ) )
			{
				$campos .= "{$gruda}ref_cod_deficiencia";
				$valores .= "{$gruda}'{$this->ref_cod_deficiencia}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_idesco ) )
			{
				$campos .= "{$gruda}ref_idesco";
				$valores .= "{$gruda}'{$this->ref_idesco}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->carga_horaria ) )
			{
				$campos .= "{$gruda}carga_horaria";
				$valores .= "{$gruda}'{$this->carga_horaria}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_subnivel ) )
			{
				$campos .= "{$gruda}ref_cod_subnivel";
				$valores .= "{$gruda}'{$this->ref_cod_subnivel}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";
			if( is_numeric( $this->ref_cod_instituicao ) )
			{
				$campos .= "{$gruda}ref_cod_instituicao";
				$valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
				$gruda = ", ";
			}

			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $this->cod_servidor;
		}
		return false;
	}

	/**
	 * Edita os dados de um registro
	 *
	 * @return bool
	 */
	function edita()
	{

		if( is_numeric( $this->cod_servidor ) && is_numeric( $this->ref_cod_instituicao ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_cod_deficiencia ) )
			{
				$set .= "{$gruda}ref_cod_deficiencia = '{$this->ref_cod_deficiencia}'";
				$gruda = ", ";
			}
			else {
				$set .= "{$gruda}ref_cod_deficiencia = NULL";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_idesco ) )
			{
				$set .= "{$gruda}ref_idesco = '{$this->ref_idesco}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->carga_horaria ) )
			{
				$set .= "{$gruda}carga_horaria = '{$this->carga_horaria}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_cadastro ) )
			{
				$set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
				$gruda = ", ";
			}
			$set .= "{$gruda}data_exclusao = NOW()";
			$gruda = ", ";
			if( is_numeric( $this->ativo ) )
			{
				$set .= "{$gruda}ativo = '{$this->ativo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_subnivel ) )
			{
				$set .= "{$gruda}ref_cod_subnivel = '{$this->ref_cod_subnivel}'";
				$gruda = ", ";
			}
			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_servidor = '{$this->cod_servidor}' AND ref_cod_instituicao = '{$this->ref_cod_instituicao}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @return array
	 */
	function lista( $int_cod_servidor = null, $int_ref_cod_deficiencia = null, $int_ref_idesco = null, $int_carga_horaria = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_cod_instituicao = null, $str_tipo = null, $array_horario = null, $str_not_in_servidor = null, $str_nome_servidor = null, $boo_professor = false, $str_horario = null, $bool_ordena_por_nome = false, $lst_matriculas = null, $matutino = false, $vespertino = false, $noturno = false, $int_ref_cod_escola = null, $str_hr_mat = nul, $str_hr_ves = null, $str_hr_not = null, $int_dia_semana = null,$alocacao_escola_instituicao = null, $int_identificador = null, $int_ref_cod_curso = null, $int_ref_cod_disciplina = null, $int_ref_cod_subnivel = null )
	{

		$whereAnd 	  = " WHERE ";
		$filtros 	  = "";
		$tabela_compl = "";

		/*if ( $boo_professor )
		{
			$tabela_compl .= ", {$this->_schema}funcao f";
		}*/

		if ( is_bool( $bool_ordena_por_nome ) )
		{
			$tabela_compl 		  .= ", cadastro.pessoa p";
			$this->_campos_lista2 .= ",p.nome";
			$filtros 			  .= "{$whereAnd} cod_servidor = idpes ";
			$whereAnd 			   = " AND ";
			$this->setOrderby( "nome" );
		}
		else
		{
			$this->_campos_lista2 = $this->_todos_campos2;
			$this->setOrderby( " 1 " );
		}

		$sql = "SELECT {$this->_campos_lista2} FROM {$this->_schema}servidor s{$tabela_compl}";

		if ( is_numeric( $int_cod_servidor ) )
		{
			$filtros .= "{$whereAnd} s.cod_servidor = '{$int_cod_servidor}'";
			$whereAnd = " AND ";
		}

		if ( is_numeric( $int_ref_cod_deficiencia ) )
		{
			$filtros .= "{$whereAnd} s.ref_cod_deficiencia = '{$int_ref_cod_deficiencia}'";
			$whereAnd = " AND ";
		}

		if ( is_numeric( $int_ref_idesco ) )
		{
			$filtros .= "{$whereAnd} s.ref_idesco = '{$int_ref_idesco}'";
			$whereAnd = " AND ";
		}

		if ( is_numeric( $int_carga_horaria ) )
		{
			$filtros .= "{$whereAnd} s.carga_horaria = '{$int_carga_horaria}'";
			$whereAnd = " AND ";
		}

		if ( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} s.data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}

		if ( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} s.data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}

		if ( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} s.data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}

		if ( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} s.data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}

		if ( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} s.ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} s.ativo = '0'";
			$whereAnd = " AND ";
		}

		if ( is_numeric( $int_ref_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} s.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
			$whereAnd = " AND ";
		}

		if (is_string( $str_nome_servidor ) )
		{
			$filtros .= "{$whereAnd} EXISTS ( SELECT 1
												FROM cadastro.pessoa p
											   WHERE cod_servidor = p.idpes
											     AND to_ascii( p.nome ) like to_ascii( '%$str_nome_servidor%' ) ) ";
			$whereAnd = " AND ";
		}

		if ( is_string( $str_tipo ) )
		{
			switch ( $str_tipo )
			{
				case "livre":
					if ( is_numeric( $int_ref_cod_instituicao ) )
					{
						$where  = " AND s.ref_cod_instituicao 	   = '{$int_ref_cod_instituicao}' ";
						$where2 = " AND sa.ref_ref_cod_instituicao = '{$int_ref_cod_instituicao}' ";

					}
					$filtros .= "{$whereAnd} NOT EXISTS ( SELECT 1
															FROM pmieducar.servidor_alocacao sa
														   WHERE sa.ref_cod_servidor = s.cod_servidor $where2 )";
					$filtros .= "{$whereAnd} s.carga_horaria >= coalesce(( SELECT sum( carga_horaria)
																	FROM pmieducar.servidor_alocacao saa
																   WHERE saa.ref_cod_servidor = {$str_not_in_servidor}  ),'00:00')$where";
					$whereAnd = " AND ";
					break;
			}

			$whereAnd = " AND ";
		}


		if( is_numeric($alocacao_escola_instituicao))
		{
			$filtros .= "{$whereAnd} s.cod_servidor IN ( SELECT a.ref_cod_servidor
															   FROM pmieducar.servidor_alocacao a
														      WHERE a.ref_ref_cod_instituicao = '{$int_ref_cod_instituicao}'
														        and ref_cod_escola = '{$int_ref_cod_escola}' )";

		}

		if ( is_array( $array_horario ) )
		{
			$cond = "";
			if ( is_numeric( $int_ref_cod_instituicao ) )
			{
				$where .= " {$cond} a.ref_ref_cod_instituicao = '{$int_ref_cod_instituicao}' ";
				$cond 	= "AND";
			}

			if ( is_numeric( $int_ref_cod_escola ) )
			{
				$where .= " {$cond} a.ref_cod_escola = '{$int_ref_cod_escola}' ";
				$cond 	= "AND";
			}


			$where .= " {$cond} a.ativo = '1'";
			$cond 	= "AND";

			$hora_ini = explode( ":", $array_horario[1] );
			$hora_fim = explode( ":", $array_horario[2] );
			$horas 	  = sprintf( "%02d", ( int ) abs( $hora_fim[0] ) - abs( $hora_ini[0] ) );
			$minutos  = sprintf( "%02d", ( int ) abs( $hora_fim[1] ) - abs( $hora_ini[1] ) );

			if ( $matutino )
			{
				if ( is_string( $str_horario ) && $str_horario == "S" )
				{
//					A somatória retorna nulo
					$filtros .= "{$whereAnd} s.cod_servidor IN ( SELECT a.ref_cod_servidor
																   FROM pmieducar.servidor_alocacao a
															      WHERE $where
															     	AND a.periodo = 1
															     	AND a.carga_horaria >= coalesce( ( SELECT SUM( qhh.hora_final - qhh.hora_inicial )
																										 FROM pmieducar.quadro_horario_horarios qhh
																										WHERE qhh.ref_cod_instituicao_servidor = '$int_ref_cod_instituicao'
																										  AND qhh.ref_cod_escola = '$int_ref_cod_escola'
																										  AND hora_inicial >= '08:00'
																										  AND hora_inicial <= '12:00'
																										  AND qhh.ativo 					   = '1'
																										  AND qhh.dia_semana 				   <> '$int_dia_semana'
																										  AND qhh.ref_servidor                 = a.ref_cod_servidor
																									 GROUP BY qhh.ref_servidor ) ,'00:00')  + '$str_hr_mat' +  	COALESCE((SELECT SUM( qhha.hora_final - qhha.hora_inicial )
																																								      FROM pmieducar.quadro_horario_horarios_aux qhha
																																								     WHERE qhha.ref_cod_instituicao_servidor = '$int_ref_cod_instituicao'
																																								       AND qhha.ref_cod_escola = $int_ref_cod_escola
																																								       AND hora_inicial >= '08:00'
																										  															   AND hora_inicial <= '12:00'
																																								       AND qhha.ref_servidor = a.ref_cod_servidor
																																								       AND identificador     = $int_identificador
																																								     GROUP BY qhha.ref_servidor),'00:00'))";
				}
				else
				{
					$filtros .= "{$whereAnd} s.cod_servidor NOT IN ( SELECT a.ref_cod_servidor
																	   FROM pmieducar.servidor_alocacao a
																      WHERE $where
																     	AND a.periodo = 1 )";
				}
			}

			if ( $vespertino )
			{
				if ( is_string( $str_horario ) && $str_horario == "S" )
				{
					$filtros .= "{$whereAnd} s.cod_servidor IN ( SELECT a.ref_cod_servidor
																   FROM pmieducar.servidor_alocacao a
															      WHERE $where
															     	AND a.periodo = 2
															     	AND a.carga_horaria >= coalesce(( SELECT SUM( qhh.hora_final - qhh.hora_inicial )
																							 FROM pmieducar.quadro_horario_horarios qhh
																							WHERE qhh.ref_cod_instituicao_servidor = '$int_ref_cod_instituicao'
																							  AND qhh.ref_cod_escola = '$int_ref_cod_escola'
																							  AND qhh.ativo 					   = '1'
																							  AND hora_inicial >= '12:00'
																							  AND hora_inicial <= '18:00'
																							  AND qhh.dia_semana 				   <> '$int_dia_semana'
																							  AND qhh.ref_servidor                 = a.ref_cod_servidor
																						 GROUP BY qhh.ref_servidor ),'00:00') + '$str_hr_ves' +  COALESCE((SELECT SUM( qhha.hora_final - qhha.hora_inicial )
																																				     FROM pmieducar.quadro_horario_horarios_aux qhha
																																				    WHERE qhha.ref_cod_instituicao_servidor = '$int_ref_cod_instituicao'
																																				      AND qhha.ref_cod_escola = '$int_ref_cod_escola'
																																				      AND qhha.ref_servidor = a.ref_cod_servidor
																																					  AND hora_inicial >= '12:00'
																										  											  AND hora_inicial <= '18:00'
																																				      AND identificador     = $int_identificador
																																				    GROUP BY qhha.ref_servidor),'00:00') )";
				}
				else
				{
					$filtros .= "{$whereAnd} s.cod_servidor NOT IN ( SELECT a.ref_cod_servidor
																	   FROM pmieducar.servidor_alocacao a
																      WHERE $where
																     	AND a.periodo = 2 )";
				}
			}

			if ( $noturno )
			{
				if ( is_string( $str_horario ) && $str_horario == "S" )
				{
					$filtros .= "{$whereAnd} s.cod_servidor IN ( SELECT a.ref_cod_servidor
																   FROM pmieducar.servidor_alocacao a
															      WHERE $where
															        AND a.periodo = 3
															        AND a.carga_horaria >= coalesce(( SELECT SUM( qhh.hora_final - qhh.hora_inicial )
																							 FROM pmieducar.quadro_horario_horarios qhh
																							WHERE qhh.ref_cod_instituicao_servidor = '$int_ref_cod_instituicao'
																							  AND qhh.ref_cod_escola = '$int_ref_cod_escola'
																							  AND qhh.ativo 					   = '1'
																							  AND hora_inicial >= '18:00'
																							  AND hora_inicial <= '23:00'
																							  AND qhh.dia_semana 				   <> '$int_dia_semana'
																						 GROUP BY qhh.ref_servidor ),'00:00')  + '$str_hr_not' +  COALESCE((SELECT SUM( qhha.hora_final - qhha.hora_inicial )
																																				     FROM pmieducar.quadro_horario_horarios_aux qhha
																																				    WHERE qhha.ref_cod_instituicao_servidor = '$int_ref_cod_instituicao'
																																				      AND qhha.ref_cod_escola = '$int_ref_cod_escola'
																																				      AND qhha.ref_servidor = a.ref_cod_servidor
																																					  AND hora_inicial >= '18:00'
																										  											  AND hora_inicial <= '23:00'
																																				      AND identificador     = $int_identificador
																																				    GROUP BY qhha.ref_servidor),'00:00') )";
				}
				else
				{
					$filtros .= "{$whereAnd} s.cod_servidor NOT IN ( SELECT a.ref_cod_servidor
																	   FROM pmieducar.servidor_alocacao a
													   				  WHERE $where
													     				AND a.periodo = 3 )";
				}
			}

			if ( is_string( $str_horario ) && $str_horario == "S" )
			{
				/*$filtros .= "{$whereAnd} s.carga_horaria >= ( SELECT EXTRACT ( HOUR FROM ( SELECT COALESCE( sum( hora_final - hora_inicial ) + '".abs( $horas ).":".abs( $minutos )."' , '00:00' )
							    FROM pmieducar.servidor_alocacao sa
							   WHERE sa.ref_cod_servidor = s.cod_servidor
							     AND sa.ref_ref_cod_instituicao = '{$int_ref_cod_instituicao}' ) ) +
			   ( SELECT EXTRACT ( MINUTE FROM ( SELECT COALESCE( sum( hora_final - hora_inicial ) + '".abs( $horas ).":".abs( $minutos )."' , '00:00' )
							      FROM pmieducar.servidor_alocacao sa
							     WHERE sa.ref_cod_servidor = s.cod_servidor
							       AND sa.ref_ref_cod_instituicao = '{$int_ref_cod_instituicao}' ) ) ) )";*/
			}
			else
			{
					$filtros .= "{$whereAnd} s.carga_horaria >= coalesce(( SELECT sum(hora_final - hora_inicial ) + '".abs( $horas ).":".abs( $minutos )."'
																	FROM pmieducar.servidor_alocacao sa WHERE sa.ref_cod_servidor = s.cod_servidor and sa.ref_ref_cod_instituicao ='{$int_ref_cod_instituicao}' ),'00:00')";
			}

			/*$filtros .= "{$whereAnd} s.carga_horaria >= ( SELECT coalesce(extract(hour from (sum(hora_final - hora_inicial ) + '{$horas}:00'::time)) + (extract(minute from (sum(hora_final - hora_inicial ) + '00:$minutos'::time)) / 60)
																		,
																		(extract(hour from ('{$horas}:00'::time)) + (extract(minute from '00:$minutos'::time)) / 60)
																		) FROM pmieducar.servidor_alocacao sa WHERE sa.ref_cod_servidor = s.cod_servidor and sa.ref_ref_cod_instituicao ='{$int_ref_cod_instituicao}' )";*/
			$whereAnd = " AND ";

		}

		if(((is_array($array_horario) && $str_not_in_servidor) || is_string( $str_tipo ) && $str_not_in_servidor))
		{

			$filtros .= "{$whereAnd} s.cod_servidor NOT IN ( {$str_not_in_servidor} )";
			$whereAnd = " AND ";
		}
		/*if ( $boo_professor ) {
			$filtros .= "{$whereAnd} s.ref_cod_funcao = f.cod_funcao AND s.ref_cod_instituicao = f.ref_cod_instituicao AND f.professor = 1";
			$whereAnd = " AND ";
		}*/

		$obj_curso = new clsPmieducarCurso($int_ref_cod_curso);
		$det_curso = $obj_curso->detalhe();

		if($det_curso['falta_ch_globalizada'])
		{
			/**
			 * busca professores independentes da disciplina
			 * somente verifica se eh professor e se da a materia para o curso
			 */
				$filtros .= "{$whereAnd} EXISTS ( SELECT 1 FROM pmieducar.servidor_curso_ministra scm WHERE scm.ref_cod_curso = $int_ref_cod_curso AND scm.ref_ref_cod_instituicao = s.ref_cod_instituicao AND s.cod_servidor = scm.ref_cod_servidor)";
				$whereAnd = " AND ";
		}
		else
		{
			/**
			 * verifica se o professor pode dar aula para a disciplina
			 * se nao tiver a disciplina nao pode dar aula
			 */

			if(is_numeric($int_ref_cod_disciplina))
			{
				$filtros .= "{$whereAnd} EXISTS ( SELECT 1 FROM pmieducar.servidor_disciplina sd WHERE sd.ref_cod_disciplina = $int_ref_cod_disciplina AND sd.ref_ref_cod_instituicao = s.ref_cod_instituicao AND s.cod_servidor = sd.ref_cod_servidor)";
				$whereAnd = " AND ";
			}
			elseif ($int_ref_cod_disciplina == "NULL")
			{
				$filtros .= "{$whereAnd} FALSE";
				$whereAnd = " AND ";
			}

		}

		if ( $boo_professor ) {
			$filtros .= "{$whereAnd} EXISTS ( SELECT 1 FROM pmieducar.servidor_funcao sf,pmieducar.funcao f WHERE f.cod_funcao = sf.ref_cod_funcao AND f.professor = 1 AND sf.ref_ref_cod_instituicao = s.ref_cod_instituicao AND s.cod_servidor = sf.ref_cod_servidor )";
			$whereAnd = " AND ";
		}
		if ( is_string( $str_horario ) && $str_horario == "S" )
		{
			$filtros .= "{$whereAnd} s.cod_servidor NOT IN ( SELECT DISTINCT qhh.ref_servidor
															   FROM pmieducar.quadro_horario_horarios qhh
															  WHERE qhh.ref_servidor = s.cod_servidor
															    AND qhh.ref_cod_instituicao_servidor = s.ref_cod_instituicao
																AND qhh.dia_semana   = '{$array_horario[0]}'
																AND qhh.hora_inicial >= '{$array_horario[1]}'
																AND qhh.hora_final	 <= '{$array_horario[2]}'
																AND qhh.ativo		 = '1'";
			if ( is_string( $lst_matriculas ) )
			{
				$filtros .= "AND qhh.ref_servidor NOT IN ( {$lst_matriculas} )";
			}

			$filtros .= " )";

			$whereAnd = " AND ";
		}

		if(is_numeric($int_identificador))
		{
			/**
			 *
			 */
			//$filtros .= "AND qhh.ref_servidor NOT IN ( {$lst_matriculas} )";
		}

		if(is_numeric($int_ref_cod_subnivel))
		{
			$filtros .= "{$whereAnd} s.ref_cod_subnivel = '{$int_ref_cod_subnivel}'";
			$whereAnd = " AND ";
		}

		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$db = new clsBanco();

	 	$sql = "SELECT {$this->_campos_lista2} FROM {$this->_schema}servidor s{$tabela_compl} {$filtros}".$this->getOrderby().$this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_schema}servidor s{$tabela_compl} {$filtros}" );

		$db->Consulta( $sql );

		if( $countCampos > 1 )
		{
			while ( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();

				$tupla["_total"] = $this->_total;
				$resultado[] = $tupla;
			}
		}
		else
		{
			while ( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				$resultado[] = $tupla[$this->_campos_lista];
			}
		}
		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
	}

	/**
	 * Retorna um array com os dados de um registro
	 *
	 * @return array
	 */
	function detalhe()
	{
		if( is_numeric( $this->cod_servidor ) && is_numeric( $this->ref_cod_instituicao ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_servidor = '{$this->cod_servidor}' AND ref_cod_instituicao = '{$this->ref_cod_instituicao}'" );
		$db->ProximoRegistro();
		return $db->Tupla();
		}
		return false;
	}

	/**
	 * Retorna um array com os dados de um registro
	 *
	 * @return array
	 */
	function existe()
	{
		if( is_numeric( $this->cod_servidor ) && is_numeric( $this->ref_cod_instituicao ) )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_servidor = '{$this->cod_servidor}' AND ref_cod_instituicao = '{$this->ref_cod_instituicao}'" );
			$db->ProximoRegistro();
			return $db->Tupla();
		}
		return false;
	}

	/**
	 * Exclui um registro
	 *
	 * @return bool
	 */
	function excluir()
	{
		if( is_numeric( $this->cod_servidor ) && is_numeric( $this->ref_cod_instituicao ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_servidor = '{$this->cod_servidor}'" );
		return true;
		*/

		$this->ativo = 0;
			return $this->edita();
		}
		return false;
	}

	/**
	 * Define quais campos da tabela serao selecionados na invocacao do metodo lista
	 *
	 * @return null
	 */
	function setCamposLista( $str_campos )
	{
		$this->_campos_lista = $str_campos;
	}

	/**
	 * Define que o metodo Lista devera retornoar todos os campos da tabela
	 *
	 * @return null
	 */
	function resetCamposLista()
	{
		$this->_campos_lista = $this->_todos_campos;
	}

	/**
	 * Define limites de retorno para o metodo lista
	 *
	 * @return null
	 */
	function setLimite( $intLimiteQtd, $intLimiteOffset = null )
	{
		$this->_limite_quantidade = $intLimiteQtd;
		$this->_limite_offset = $intLimiteOffset;
	}

	/**
	 * Retorna a string com o trecho da query resposavel pelo Limite de registros
	 *
	 * @return string
	 */
	function getLimite()
	{
		if( is_numeric( $this->_limite_quantidade ) )
		{
			$retorno = " LIMIT {$this->_limite_quantidade}";
			if( is_numeric( $this->_limite_offset ) )
			{
				$retorno .= " OFFSET {$this->_limite_offset} ";
			}
			return $retorno;
		}
		return "";
	}

	/**
	 * Define campo para ser utilizado como ordenacao no metolo lista
	 *
	 * @return null
	 */
	function setOrderby( $strNomeCampo )
	{
		// limpa a string de possiveis erros (delete, insert, etc)
		//$strNomeCampo = eregi_replace();

		if( is_string( $strNomeCampo ) && $strNomeCampo )
		{
			$this->_campo_order_by = $strNomeCampo;
		}
	}

	/**
	 * Retorna a string com o trecho da query resposavel pela Ordenacao dos registros
	 *
	 * @return string
	 */
	function getOrderby()
	{
		if( is_string( $this->_campo_order_by ) )
		{
			return " ORDER BY {$this->_campo_order_by} ";
		}
		return "";
	}

	/**
	 * Retorna um array com os dados de um registro
	 *
	 * @return array
	 */
	function qtdhoras( $int_cod_servidor, $int_cod_escola, $int_ref_cod_instituicao, $dia_semana )
	{
		$db = new clsBanco();
		$db->Consulta( "SELECT EXTRACT( HOUR FROM ( SUM( hora_final - hora_inicial ) ) ) AS hora,
						       EXTRACT( MINUTE FROM ( SUM( hora_final - hora_inicial ) ) ) AS min
						  FROM pmieducar.servidor_alocacao
						 WHERE ref_cod_servidor        = {$int_cod_servidor}
						   AND ref_cod_escola	       = {$int_cod_escola}
						   AND ref_ref_cod_instituicao = {$int_ref_cod_instituicao}
						   AND dia_semana              = {$dia_semana}" );
		$db->ProximoRegistro();
		return $db->Tupla();
	}

}
?>