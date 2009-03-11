<?php
/**
* @author Prefeitura Municipal de Itajaí
*
* Criado em 24/04/2007 11:06 pelo gerador automatico de classes
*/

require_once( "include/portal/geral.inc.php" );

class clsPortalFiaFaleConosco
{
	var $cod_fale_conosco;
	var $data_cadastro;
	var $email_remetente;
	var $nm_remetente;
	var $mensagem;

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
	 * @param integer cod_fale_conosco
	 * @param string data_cadastro
	 * @param string email_remetente
	 * @param string nm_remetente
	 * @param string mensagem
	 *
	 * @return object
	 */
	function clsPortalFiaFaleConosco( $cod_fale_conosco = null, $data_cadastro = null, $email_remetente = null, $nm_remetente = null, $mensagem = null )
	{
		$db = new clsBanco();
		$this->_schema = "portal.";
		$this->_tabela = "{$this->_schema}fia_fale_conosco";

		$this->_campos_lista = $this->_todos_campos = "cod_fale_conosco, data_cadastro, email_remetente, nm_remetente, mensagem";



		if( is_numeric( $cod_fale_conosco ) )
		{
			$this->cod_fale_conosco = $cod_fale_conosco;
		}
		if( is_string( $data_cadastro ) )
		{
			$this->data_cadastro = $data_cadastro;
		}
		if( is_string( $email_remetente ) )
		{
			$this->email_remetente = $email_remetente;
		}
		if( is_string( $nm_remetente ) )
		{
			$this->nm_remetente = $nm_remetente;
		}
		if( is_string( $mensagem ) )
		{
			$this->mensagem = $mensagem;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_string( $this->email_remetente ) && is_string( $this->mensagem ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			if( is_string( $this->email_remetente ) )
			{
				$campos .= "{$gruda}email_remetente";
				$valores .= "{$gruda}'{$this->email_remetente}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_remetente ) )
			{
				$campos .= "{$gruda}nm_remetente";
				$valores .= "{$gruda}'{$this->nm_remetente}'";
				$gruda = ", ";
			}
			if( is_string( $this->mensagem ) )
			{
				$campos .= "{$gruda}mensagem";
				$valores .= "{$gruda}'{$this->mensagem}'";
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_fale_conosco_seq");
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
		if( is_numeric( $this->cod_fale_conosco ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_string( $this->data_cadastro ) )
			{
				$set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
				$gruda = ", ";
			}
			if( is_string( $this->email_remetente ) )
			{
				$set .= "{$gruda}email_remetente = '{$this->email_remetente}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_remetente ) )
			{
				$set .= "{$gruda}nm_remetente = '{$this->nm_remetente}'";
				$gruda = ", ";
			}
			if( is_string( $this->mensagem ) )
			{
				$set .= "{$gruda}mensagem = '{$this->mensagem}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_fale_conosco = '{$this->cod_fale_conosco}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 * 
	 * @param string date_data_cadastro_ini
	 * @param string date_data_cadastro_fim
	 * @param string str_email_remetente
	 * @param string str_nm_remetente
	 * @param string str_mensagem
	 *
	 * @return array
	 */
	function lista( $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $str_email_remetente = null, $str_nm_remetente = null, $str_mensagem = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_fale_conosco ) )
		{
			$filtros .= "{$whereAnd} cod_fale_conosco = '{$int_cod_fale_conosco}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_email_remetente ) )
		{
			$filtros .= "{$whereAnd} email_remetente LIKE '%{$str_email_remetente}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_remetente ) )
		{
			$filtros .= "{$whereAnd} nm_remetente LIKE '%{$str_nm_remetente}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_mensagem ) )
		{
			$filtros .= "{$whereAnd} mensagem LIKE '%{$str_mensagem}%'";
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
		if( is_numeric( $this->cod_fale_conosco ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_fale_conosco = '{$this->cod_fale_conosco}'" );
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
		if( is_numeric( $this->cod_fale_conosco ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_fale_conosco = '{$this->cod_fale_conosco}'" );
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
		if( is_numeric( $this->cod_fale_conosco ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_fale_conosco = '{$this->cod_fale_conosco}'" );
		return true;
		*/

		
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