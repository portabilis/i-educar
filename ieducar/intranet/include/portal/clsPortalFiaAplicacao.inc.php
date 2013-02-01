<?php
/**
* @author Prefeitura Municipal de Itajaí
*
* Criado em 27/04/2007 10:48 pelo gerador automatico de classes
*/


class clsPortalFiaAplicacao
{
	var $cod_aplicacao;
	var $ano;
	var $num_entidade;
	var $valor;

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
	 * @param integer cod_aplicacao
	 * @param integer ano
	 * @param integer num_entidade
	 * @param string valor
	 *
	 * @return object
	 */
	function clsPortalFiaAplicacao( $cod_aplicacao = null, $ano = null, $num_entidade = null, $valor = null )
	{
		$db = new clsBanco();
		$this->_schema = "portal.";
		$this->_tabela = "{$this->_schema}fia_aplicacao";

		$this->_campos_lista = $this->_todos_campos = "cod_aplicacao, ano, num_entidade, valor";



		if( is_numeric( $cod_aplicacao ) )
		{
			$this->cod_aplicacao = $cod_aplicacao;
		}
		if( is_numeric( $ano ) )
		{
			$this->ano = $ano;
		}
		if( is_numeric( $num_entidade ) )
		{
			$this->num_entidade = $num_entidade;
		}
		if( is_string( $valor ) )
		{
			$this->valor = $valor;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ano ) && is_numeric( $this->num_entidade ) && is_string( $this->valor ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ano ) )
			{
				$campos .= "{$gruda}ano";
				$valores .= "{$gruda}'{$this->ano}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->num_entidade ) )
			{
				$campos .= "{$gruda}num_entidade";
				$valores .= "{$gruda}'{$this->num_entidade}'";
				$gruda = ", ";
			}
			if( is_string( $this->valor ) )
			{
				$campos .= "{$gruda}valor";
				$valores .= "{$gruda}'{$this->valor}'";
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_aplicacao_seq");
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
		if( is_numeric( $this->cod_aplicacao ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ano ) )
			{
				$set .= "{$gruda}ano = '{$this->ano}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->num_entidade ) )
			{
				$set .= "{$gruda}num_entidade = '{$this->num_entidade}'";
				$gruda = ", ";
			}
			if( is_string( $this->valor ) )
			{
				$set .= "{$gruda}valor = '{$this->valor}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_aplicacao = '{$this->cod_aplicacao}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 * 
	 * @param integer int_ano
	 * @param integer int_num_entidade
	 * @param string str_valor
	 *
	 * @return array
	 */
	function lista( $int_ano = null, $int_num_entidade = null, $str_valor = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_aplicacao ) )
		{
			$filtros .= "{$whereAnd} cod_aplicacao = '{$int_cod_aplicacao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ano ) )
		{
			$filtros .= "{$whereAnd} ano = '{$int_ano}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_num_entidade ) )
		{
			$filtros .= "{$whereAnd} num_entidade = '{$int_num_entidade}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_valor ) )
		{
			$filtros .= "{$whereAnd} valor LIKE '%{$str_valor}%'";
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
		if( is_numeric( $this->cod_aplicacao ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_aplicacao = '{$this->cod_aplicacao}'" );
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
		if( is_numeric( $this->cod_aplicacao ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_aplicacao = '{$this->cod_aplicacao}'" );
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
		if( is_numeric( $this->cod_aplicacao ) )
		{

		
		//	delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_aplicacao = '{$this->cod_aplicacao}'" );
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