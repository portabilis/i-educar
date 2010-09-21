<?php
/**
* @author Prefeitura Municipal de Itajaí
*
* Criado em 04/05/2007 17:22 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarQuadroHorarioHorariosAux
{
	var $ref_cod_quadro_horario;
	var $sequencial;
	var $ref_cod_disciplina;
	var $ref_cod_escola;
	var $ref_cod_serie;
	var $ref_cod_instituicao_servidor;
	var $ref_servidor;
	var $dia_semana;
	var $hora_inicial;
	var $hora_final;
	var $identificador;
	var $data_cadastro;

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

	/**
	 * Lista com todos os campos da tabela separados por virgula, padrao para selecao no metodo lista
	 *
	 * @var string
	 */
	var $_todos_campos;

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


	/**
	 * Construtor (PHP 4)
	 *
	 * @param integer ref_cod_quadro_horario
	 * @param integer sequencial
	 * @param integer ref_cod_disciplina
	 * @param integer ref_cod_escola
	 * @param integer ref_cod_serie
	 * @param integer ref_cod_instituicao_servidor
	 * @param integer ref_servidor
	 * @param integer dia_semana
	 * @param string hora_inicial
	 * @param string hora_final
	 * @param string identificador
	 *
	 * @return object
	 */
	function clsPmieducarQuadroHorarioHorariosAux( $ref_cod_quadro_horario = null, $sequencial = null, $ref_cod_disciplina = null, $ref_cod_escola = null, $ref_cod_serie = null, $ref_cod_instituicao_servidor = null, $ref_servidor = null, $dia_semana = null, $hora_inicial = null, $hora_final = null, $identificador = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}quadro_horario_horarios_aux";

		$this->_campos_lista = $this->_todos_campos = "ref_cod_quadro_horario, sequencial, ref_cod_disciplina, ref_cod_escola, ref_cod_serie, ref_cod_instituicao_servidor, ref_servidor, dia_semana, hora_inicial, hora_final, identificador, data_cadastro";

		if( is_numeric( $ref_servidor ) && is_numeric( $ref_cod_instituicao_servidor ) )
		{
			if( class_exists( "clsPmieducarServidor" ) )
			{
				$tmp_obj = new clsPmieducarServidor( $ref_servidor,null,null,null,null,null,null,null, $ref_cod_instituicao_servidor );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_servidor = $ref_servidor;
						$this->ref_cod_instituicao_servidor = $ref_cod_instituicao_servidor;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_servidor = $ref_servidor;
						$this->ref_cod_instituicao_servidor = $ref_cod_instituicao_servidor;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.servidor WHERE cod_servidor = '{$ref_servidor}' AND ref_cod_instituicao = '{$ref_cod_instituicao_servidor}'" ) )
				{
					$this->ref_servidor = $ref_servidor;
					$this->ref_cod_instituicao_servidor = $ref_cod_instituicao_servidor;
				}
			}
		}
		if( is_numeric( $ref_cod_serie ) && is_numeric( $ref_cod_escola ) && is_numeric( $ref_cod_disciplina ) )
		{
			if( class_exists( "clsPmieducarEscolaSerieDisciplina" ) )
			{
				$tmp_obj = new clsPmieducarEscolaSerieDisciplina( $ref_cod_serie, $ref_cod_escola, $ref_cod_disciplina );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_serie = $ref_cod_serie;
						$this->ref_cod_escola = $ref_cod_escola;
						$this->ref_cod_disciplina = $ref_cod_disciplina;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_serie = $ref_cod_serie;
						$this->ref_cod_escola = $ref_cod_escola;
						$this->ref_cod_disciplina = $ref_cod_disciplina;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.escola_serie_disciplina WHERE ref_ref_cod_serie = '{$ref_cod_serie}' AND ref_ref_cod_escola = '{$ref_cod_escola}' AND ref_cod_disciplina = '{$ref_cod_disciplina}'" ) )
				{
					$this->ref_cod_serie = $ref_cod_serie;
					$this->ref_cod_escola = $ref_cod_escola;
					$this->ref_cod_disciplina = $ref_cod_disciplina;
				}
			}
		}
		if( is_numeric( $ref_cod_quadro_horario ) )
		{
			if( class_exists( "clsPmieducarQuadroHorario" ) )
			{
				$tmp_obj = new clsPmieducarQuadroHorario( $ref_cod_quadro_horario );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_quadro_horario = $ref_cod_quadro_horario;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_quadro_horario = $ref_cod_quadro_horario;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.quadro_horario WHERE cod_quadro_horario = '{$ref_cod_quadro_horario}'" ) )
				{
					$this->ref_cod_quadro_horario = $ref_cod_quadro_horario;
				}
			}
		}


		if( is_numeric( $sequencial ) )
		{
			$this->sequencial = $sequencial;
		}
		if( is_numeric( $dia_semana ) )
		{
			$this->dia_semana = $dia_semana;
		}
		if( ( $hora_inicial ) )
		{
			$this->hora_inicial = $hora_inicial;
		}
		if( ( $hora_final ) )
		{
			$this->hora_final = $hora_final;
		}
		if( is_string( $identificador ) )
		{
			$this->identificador = $identificador;
		}

		$this->excluirRegistrosAntigos();

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_quadro_horario ) && is_numeric( $this->ref_cod_disciplina ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_serie ) && is_numeric( $this->ref_cod_instituicao_servidor ) && is_numeric( $this->ref_servidor ) && is_numeric( $this->dia_semana ) && ( $this->hora_inicial ) && ( $this->hora_final ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_quadro_horario ) )
			{
				$campos .= "{$gruda}ref_cod_quadro_horario";
				$valores .= "{$gruda}'{$this->ref_cod_quadro_horario}'";
				$gruda = ", ";
			}
			$this->sequencial = $db->CampoUnico( "SELECT ( COALESCE( MAX( sequencial ), 0 ) + 1 ) AS sequencial
												    FROM pmieducar.quadro_horario_horarios_aux
												   WHERE ref_cod_quadro_horario = {$this->ref_cod_quadro_horario}
												     AND ref_cod_serie      = {$this->ref_cod_serie}
												     AND ref_cod_escola		= {$this->ref_cod_escola}" );

			if( is_numeric( $this->sequencial ) )
			{
				$campos .= "{$gruda}sequencial";
				$valores .= "{$gruda}'{$this->sequencial}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_disciplina ) )
			{
				$campos .= "{$gruda}ref_cod_disciplina";
				$valores .= "{$gruda}'{$this->ref_cod_disciplina}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_escola ) )
			{
				$campos .= "{$gruda}ref_cod_escola";
				$valores .= "{$gruda}'{$this->ref_cod_escola}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_serie ) )
			{
				$campos .= "{$gruda}ref_cod_serie";
				$valores .= "{$gruda}'{$this->ref_cod_serie}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_instituicao_servidor ) )
			{
				$campos .= "{$gruda}ref_cod_instituicao_servidor";
				$valores .= "{$gruda}'{$this->ref_cod_instituicao_servidor}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_servidor ) )
			{
				$campos .= "{$gruda}ref_servidor";
				$valores .= "{$gruda}'{$this->ref_servidor}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->dia_semana ) )
			{
				$campos .= "{$gruda}dia_semana";
				$valores .= "{$gruda}'{$this->dia_semana}'";
				$gruda = ", ";
			}
			if( ( $this->hora_inicial ) )
			{
				$campos .= "{$gruda}hora_inicial";
				$valores .= "{$gruda}'{$this->hora_inicial}'";
				$gruda = ", ";
			}
			if( ( $this->hora_final ) )
			{
				$campos .= "{$gruda}hora_final";
				$valores .= "{$gruda}'{$this->hora_final}'";
				$gruda = ", ";
			}
			if( is_string( $this->identificador ) )
			{
				$campos .= "{$gruda}identificador";
				$valores .= "{$gruda}'{$this->identificador}'";
				$gruda = ", ";
			}

			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";

			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return true;
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
		if( is_numeric( $this->ref_cod_quadro_horario ) && is_numeric( $this->sequencial ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_cod_disciplina ) )
			{
				$set .= "{$gruda}ref_cod_disciplina = '{$this->ref_cod_disciplina}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_escola ) )
			{
				$set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_serie ) )
			{
				$set .= "{$gruda}ref_cod_serie = '{$this->ref_cod_serie}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_instituicao_servidor ) )
			{
				$set .= "{$gruda}ref_cod_instituicao_servidor = '{$this->ref_cod_instituicao_servidor}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_servidor ) )
			{
				$set .= "{$gruda}ref_servidor = '{$this->ref_servidor}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->dia_semana ) )
			{
				$set .= "{$gruda}dia_semana = '{$this->dia_semana}'";
				$gruda = ", ";
			}
			if( ( $this->hora_inicial ) )
			{
				$set .= "{$gruda}hora_inicial = '{$this->hora_inicial}'";
				$gruda = ", ";
			}
			if( ( $this->hora_final ) )
			{
				$set .= "{$gruda}hora_final = '{$this->hora_final}'";
				$gruda = ", ";
			}
			if( is_string( $this->identificador ) )
			{
				$set .= "{$gruda}identificador = '{$this->identificador}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_cod_quadro_horario = '{$this->ref_cod_quadro_horario}' AND sequencial = '{$this->sequencial}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @param integer int_ref_cod_disciplina
	 * @param integer int_ref_cod_escola
	 * @param integer int_ref_cod_serie
	 * @param integer int_ref_cod_instituicao_servidor
	 * @param integer int_ref_servidor
	 * @param integer int_dia_semana
	 * @param string time_hora_inicial_ini
	 * @param string time_hora_inicial_fim
	 * @param string time_hora_final_ini
	 * @param string time_hora_final_fim
	 * @param string str_identificador
	 *
	 * @return array
	 */
	function lista( $int_ref_cod_disciplina = null, $int_ref_cod_escola = null, $int_ref_cod_serie = null, $int_ref_cod_instituicao_servidor = null, $int_ref_servidor = null, $int_dia_semana = null, $time_hora_inicial_ini = null, $time_hora_inicial_fim = null, $time_hora_final_ini = null, $time_hora_final_fim = null, $str_identificador = null, $str_data_cadastro_ini = null, $str_data_cadastro_fim = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_ref_cod_quadro_horario ) )
		{
			$filtros .= "{$whereAnd} ref_cod_quadro_horario = '{$int_ref_cod_quadro_horario}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_sequencial ) )
		{
			$filtros .= "{$whereAnd} sequencial = '{$int_sequencial}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_disciplina ) )
		{
			$filtros .= "{$whereAnd} ref_cod_disciplina = '{$int_ref_cod_disciplina}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_escola ) )
		{
			$filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_serie ) )
		{
			$filtros .= "{$whereAnd} ref_cod_serie = '{$int_ref_cod_serie}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao_servidor ) )
		{
			$filtros .= "{$whereAnd} ref_cod_instituicao_servidor = '{$int_ref_cod_instituicao_servidor}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_servidor ) )
		{
			$filtros .= "{$whereAnd} ref_servidor = '{$int_ref_servidor}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_dia_semana ) )
		{
			$filtros .= "{$whereAnd} dia_semana = '{$int_dia_semana}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_inicial_ini ) )
		{
			$filtros .= "{$whereAnd} hora_inicial >= '{$time_hora_inicial_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_inicial_fim ) )
		{
			$filtros .= "{$whereAnd} hora_inicial <= '{$time_hora_inicial_fim}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_final_ini ) )
		{
			$filtros .= "{$whereAnd} hora_final >= '{$time_hora_final_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_final_fim ) )
		{
			$filtros .= "{$whereAnd} hora_final <= '{$time_hora_final_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_identificador ) )
		{
			$filtros .= "{$whereAnd} identificador LIKE '%{$str_identificador}%'";
			$whereAnd = " AND ";
		}

		if( ( $str_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} data_cadastro >= '{$str_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $str_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} data_cadastro <= '{$str_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}


		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} {$filtros}" );

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
		if( is_numeric( $this->ref_cod_quadro_horario ) && is_numeric( $this->sequencial ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_quadro_horario = '{$this->ref_cod_quadro_horario}' AND sequencial = '{$this->sequencial}'" );
			$db->ProximoRegistro();
			return $db->Tupla();
		}
		return false;
	}

	/**
	 * Retorna true se o registro existir. Caso contrário retorna false.
	 *
	 * @return bool
	 */
	function existe()
	{
		if( is_numeric( $this->ref_cod_quadro_horario ) && is_numeric( $this->sequencial ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_cod_quadro_horario = '{$this->ref_cod_quadro_horario}' AND sequencial = '{$this->sequencial}'" );
			if( $db->ProximoRegistro() )
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Exclui um registro
	 *
	 * @return bool
	 */
	function excluiRegistro($ref_cod_quadro_horario, $ref_cod_serie, $ref_cod_escola, $ref_cod_disciplina, $ref_cod_instituicao_servidor, $ref_servidor, $identificador)
	{
		if(is_numeric($ref_cod_quadro_horario) && is_numeric($ref_cod_serie) && is_numeric($ref_cod_escola) && is_numeric($ref_cod_disciplina) && is_numeric($ref_cod_instituicao_servidor) && is_numeric($ref_servidor) && is_numeric($identificador))
		{


			$db = new clsBanco();
			$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_quadro_horario = '{$ref_cod_quadro_horario}' AND ref_cod_serie = '$ref_cod_serie' AND ref_cod_escola = '$ref_cod_escola' AND ref_cod_disciplina = '$ref_cod_disciplina' AND ref_cod_instituicao_servidor = '$ref_cod_instituicao_servidor' AND ref_servidor = '$ref_servidor' AND identificador = '$identificador'" );
			return true;

		}
		return false;
	}

	/**
	 * Exclui todos registros de um identificador
	 *
	 * @return bool
	 */
	function excluirTodos($identificador)
	{
		if( is_numeric( $identificador )  )
		{



			$db = new clsBanco();
			$db->Consulta( "DELETE FROM {$this->_tabela} WHERE identificador = '{$identificador}'" );
			return true;

		}

		return false;
	}

	/**
	 * Exclui todos registros de um identificador
	 *
	 * @return bool
	 */
	function excluirRegistrosAntigos()
	{

		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE data_cadastro < NOW() - interval '3 hours'" );

		return true;


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

}
?>