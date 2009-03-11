<?php
/**
* @author Prefeitura Municipal de Itajaí
*
* Criado em 26/04/2007 17:09 pelo gerador automatico de classes
*/



class clsPortalFiaContas
{
	var $cod_conta;
	var $entidade;
	var $num_convenio;
	var $valor;
	var $periodo_inicio;
	var $periodo_final;
	var $ano;

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
	 * @param integer cod_conta
	 * @param string entidade
	 * @param integer num_convenio
	 * @param string valor
	 * @param string periodo_inicio
	 * @param string periodo_final
	 * @param integer ano
	 *
	 * @return object
	 */
	function clsPortalFiaContas( $cod_conta = null, $entidade = null, $num_convenio = null, $valor = null, $periodo_inicio = null, $periodo_final = null, $ano = null )
	{
		$db = new clsBanco();
		$this->_schema = "portal.";
		$this->_tabela = "{$this->_schema}fia_contas";

		$this->_campos_lista = $this->_todos_campos = "cod_conta, entidade, num_convenio, valor, periodo_inicio, periodo_final, ano";



		if( is_numeric( $cod_conta ) )
		{
			$this->cod_conta = $cod_conta;
		}
		if( is_string( $entidade ) )
		{
			$this->entidade = $entidade;
		}
		if( is_numeric( $num_convenio ) )
		{
			$this->num_convenio = $num_convenio;
		}
		if( is_string( $valor ) )
		{
			$this->valor = $valor;
		}
		if( is_string( $periodo_inicio ) )
		{
			$this->periodo_inicio = $periodo_inicio;
		}
		if( is_string( $periodo_final ) )
		{
			$this->periodo_final = $periodo_final;
		}
		if( is_numeric( $ano ) )
		{
			$this->ano = $ano;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_string( $this->entidade ) && is_numeric( $this->num_convenio ) && is_string( $this->valor ) && is_string( $this->periodo_inicio ) && is_string( $this->periodo_final ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_string( $this->entidade ) )
			{
				$campos .= "{$gruda}entidade";
				$valores .= "{$gruda}'{$this->entidade}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->num_convenio ) )
			{
				$campos .= "{$gruda}num_convenio";
				$valores .= "{$gruda}'{$this->num_convenio}'";
				$gruda = ", ";
			}
			if( is_string( $this->valor ) )
			{
				$campos .= "{$gruda}valor";
				$valores .= "{$gruda}'{$this->valor}'";
				$gruda = ", ";
			}
			if( is_string( $this->periodo_inicio ) )
			{
				$campos .= "{$gruda}periodo_inicio";
				$valores .= "{$gruda}'{$this->periodo_inicio}'";
				$gruda = ", ";
			}
			if( is_string( $this->periodo_final ) )
			{
				$campos .= "{$gruda}periodo_final";
				$valores .= "{$gruda}'{$this->periodo_final}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ano ) )
			{
				$campos .= "{$gruda}ano";
				$valores .= "{$gruda}'{$this->ano}'";
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_conta_seq");
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
		if( is_numeric( $this->cod_conta ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_string( $this->entidade ) )
			{
				$set .= "{$gruda}entidade = '{$this->entidade}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->num_convenio ) )
			{
				$set .= "{$gruda}num_convenio = '{$this->num_convenio}'";
				$gruda = ", ";
			}
			if( is_string( $this->valor ) )
			{
				$set .= "{$gruda}valor = '{$this->valor}'";
				$gruda = ", ";
			}
			if( is_string( $this->periodo_inicio ) )
			{
				$set .= "{$gruda}periodo_inicio = '{$this->periodo_inicio}'";
				$gruda = ", ";
			}
			if( is_string( $this->periodo_final ) )
			{
				$set .= "{$gruda}periodo_final = '{$this->periodo_final}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ano ) )
			{
				$set .= "{$gruda}ano = '{$this->ano}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_conta = '{$this->cod_conta}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 * 
	 * @param string str_entidade
	 * @param integer int_num_convenio
	 * @param string str_valor
	 * @param string date_periodo_inicio_ini
	 * @param string date_periodo_inicio_fim
	 * @param string date_periodo_final_ini
	 * @param string date_periodo_final_fim
	 * @param integer int_ano
	 *
	 * @return array
	 */
	function lista( $str_entidade = null, $int_num_convenio = null, $str_valor = null, $date_periodo_inicio_ini = null, $date_periodo_inicio_fim = null, $date_periodo_final_ini = null, $date_periodo_final_fim = null, $int_ano = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_conta ) )
		{
			$filtros .= "{$whereAnd} cod_conta = '{$int_cod_conta}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_entidade ) )
		{
			$filtros .= "{$whereAnd} entidade LIKE '%{$str_entidade}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_num_convenio ) )
		{
			$filtros .= "{$whereAnd} num_convenio = '{$int_num_convenio}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_valor ) )
		{
			$filtros .= "{$whereAnd} valor LIKE '%{$str_valor}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_periodo_inicio_ini ) )
		{
			$filtros .= "{$whereAnd} periodo_inicio >= '{$date_periodo_inicio_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_periodo_inicio_fim ) )
		{
			$filtros .= "{$whereAnd} periodo_inicio <= '{$date_periodo_inicio_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_periodo_final_ini ) )
		{
			$filtros .= "{$whereAnd} periodo_final >= '{$date_periodo_final_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_periodo_final_fim ) )
		{
			$filtros .= "{$whereAnd} periodo_final <= '{$date_periodo_final_fim}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ano ) )
		{
			$filtros .= "{$whereAnd} ano = '{$int_ano}'";
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
		if( is_numeric( $this->cod_conta ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_conta = '{$this->cod_conta}'" );
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
		if( is_numeric( $this->cod_conta ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_conta = '{$this->cod_conta}'" );
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
	function excluir()
	{
		if( is_numeric( $this->cod_conta ) )
		{

		//	delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_conta = '{$this->cod_conta}'" );
		return true;
		
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

}
?>