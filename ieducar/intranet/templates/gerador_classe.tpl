/**
* @author Prefeitura Municipal de Itajaí
*
* Criado em #data_criacao# pelo gerador automatico de classes
*/

require_once( "include/#nome_schema#/geral.inc.php" );

class #nome_classe#
{
	#inicia_variaveis#

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
	 * #doc_construct#
	 *
	 * @return object
	 */
	function #nome_classe#( #parametros_inicializa# )
	{
		$db = new clsBanco();
		$this->_schema = "#nome_schema#.";
		$this->_tabela = "{$this->_schema}#nome_tabela#";

		$this->_campos_lista = $this->_todos_campos = "#todos_campos#";

#check_inicializacao_fk#

#check_inicializacao#
	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( #check_obrigatorio# )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

#check_valores_cadas#

			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return #cadastra_return#;
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
		#check_pk_start_edicao#
#check_pk_tabulacao#		$db = new clsBanco();
#check_pk_tabulacao#		$set = "";

#check_valores_edita#

#check_pk_tabulacao#		if( $set )
#check_pk_tabulacao#		{
#check_pk_tabulacao#			$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE #where_primary_key#" );
#check_pk_tabulacao#			return true;
#check_pk_tabulacao#		}
		#check_pk_end#
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 * #doc_lista#
	 *
	 * @return array
	 */
	function lista( #parametros_lista# )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

#check_valores_lista#

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
		#check_pk_start#
#check_pk_tab#			$db = new clsBanco();
#check_pk_tab#			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE #where_primary_key#" );
#check_pk_tab#			$db->ProximoRegistro();
#check_pk_tab#			return $db->Tupla();
		#check_pk_end#
		return false;
	}

	/**
	 * Retorna true se o registro existir. Caso contrário retorna false.
	 *
	 * @return bool
	 */
	function existe()
	{
		#check_pk_start#
#check_pk_tab#			$db = new clsBanco();
#check_pk_tab#			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE #where_primary_key#" );
#check_pk_tab#			if( $db->ProximoRegistro() )
#check_pk_tab#			{
#check_pk_tab#				return true;
#check_pk_tab#			}
		#check_pk_end#
		return false;
	}

	/**
	 * Exclui um registro
	 *
	 * @return bool
	 */
	function excluir()
	{
		#check_pk_start_edicao#
#check_pk_tab#		/*
#check_pk_tab#			delete
#check_pk_tab#		$db = new clsBanco();
#check_pk_tab#		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE #where_primary_key#" );
#check_pk_tab#		return true;
#check_pk_tab#		*/

#check_pk_tab#		#inativar#
		#check_pk_end#
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