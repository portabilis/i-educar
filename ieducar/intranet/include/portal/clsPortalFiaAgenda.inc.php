<?php
/**
* @author Prefeitura Municipal de Itajaí
*
* Criado em 20/04/2007 14:51 pelo gerador automatico de classes
*/


class clsPortalFiaAgenda
{
	var $cod_agenda;
	var $nm_agenda;
	var $descricao;
	var $data;

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
	 * @param integer cod_agenda
	 * @param string nm_agenda
	 * @param string descricao
	 * @param string data
	 *
	 * @return object
	 */
	function clsPortalFiaAgenda( $cod_agenda = null, $nm_agenda = null, $descricao = null, $data = null )
	{
		$db = new clsBanco();
		$this->_schema = "portal.";
		$this->_tabela = "{$this->_schema}fia_agenda";

		$this->_campos_lista = $this->_todos_campos = "cod_agenda, nm_agenda, descricao, data";



		if( is_numeric( $cod_agenda ) )
		{
			$this->cod_agenda = $cod_agenda;
		}
		if( is_string( $nm_agenda ) )
		{
			$this->nm_agenda = $nm_agenda;
		}
		if( is_string( $descricao ) )
		{
			$this->descricao = $descricao;
		}
		if( is_string( $data ) )
		{
			$this->data = $data;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_string( $this->nm_agenda ) && is_string( $this->data ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_string( $this->nm_agenda ) )
			{
				$campos .= "{$gruda}nm_agenda";
				$valores .= "{$gruda}'{$this->nm_agenda}'";
				$gruda = ", ";
			}
			if( is_string( $this->descricao ) )
			{
				$campos .= "{$gruda}descricao";
				$valores .= "{$gruda}'{$this->descricao}'";
				$gruda = ", ";
			}
			if( is_string( $this->data ) )
			{
				$campos .= "{$gruda}data";
				$valores .= "{$gruda}'{$this->data}'";
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_agenda_seq");
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
		if( is_numeric( $this->cod_agenda ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_string( $this->nm_agenda ) )
			{
				$set .= "{$gruda}nm_agenda = '{$this->nm_agenda}'";
				$gruda = ", ";
			}
			if( is_string( $this->descricao ) )
			{
				$set .= "{$gruda}descricao = '{$this->descricao}'";
				$gruda = ", ";
			}
			if( is_string( $this->data ) )
			{
				$set .= "{$gruda}data = '{$this->data}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_agenda = '{$this->cod_agenda}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 * 
	 * @param string str_nm_agenda
	 * @param string str_descricao
	 * @param string date_data_ini
	 * @param string date_data_fim
	 *
	 * @return array
	 */
	function lista( $str_nm_agenda = null, $str_descricao = null, $date_data_ini = null, $date_data_fim = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_agenda ) )
		{
			$filtros .= "{$whereAnd} cod_agenda = '{$int_cod_agenda}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_agenda ) )
		{
			$filtros .= "{$whereAnd} nm_agenda LIKE '%{$str_nm_agenda}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_descricao ) )
		{
			$filtros .= "{$whereAnd} descricao LIKE '%{$str_descricao}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_ini ) )
		{
			$filtros .= "{$whereAnd} data >= '{$date_data_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_fim ) )
		{
			$filtros .= "{$whereAnd} data < '{$date_data_fim}'";
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
		if( is_numeric( $this->cod_agenda ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_agenda = '{$this->cod_agenda}'" );
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
		if( is_numeric( $this->cod_agenda ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_agenda = '{$this->cod_agenda}'" );
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
		if( is_numeric( $this->cod_agenda ) )
		{

		
		//	delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_agenda = '{$this->cod_agenda}'" );
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