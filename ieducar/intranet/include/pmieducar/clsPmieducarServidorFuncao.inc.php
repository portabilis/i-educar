<?php
/**
* @author Prefeitura Municipal de Itajaí
*
* Criado em 17/05/2007 14:36 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarServidorFuncao
{
	var $ref_ref_cod_instituicao;
	var $ref_cod_servidor;
	var $ref_cod_funcao;

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
	 * @param integer ref_ref_cod_instituicao
	 * @param integer ref_cod_servidor
	 * @param integer ref_cod_funcao
	 *
	 * @return object
	 */
	function clsPmieducarServidorFuncao( $ref_ref_cod_instituicao = null, $ref_cod_servidor = null, $ref_cod_funcao = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}servidor_funcao";

		$this->_campos_lista = $this->_todos_campos = "ref_ref_cod_instituicao, ref_cod_servidor, ref_cod_funcao";

		if( is_numeric( $ref_cod_funcao ) )
		{
			if( class_exists( "clsPmieducarFuncao" ) )
			{
				$tmp_obj = new clsPmieducarFuncao( $ref_cod_funcao, null, null, null, null, null, null, null, null, $ref_ref_cod_instituicao );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_funcao = $ref_cod_funcao;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_funcao = $ref_cod_funcao;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.funcao WHERE cod_funcao = '{$ref_cod_funcao}'" ) )
				{
					$this->ref_cod_funcao = $ref_cod_funcao;
				}
			}
		}
		if( is_numeric( $ref_cod_servidor ) && is_numeric( $ref_ref_cod_instituicao ) )
		{
			if( class_exists( "clsPmieducarServidor" ) )
			{
				$tmp_obj = new clsPmieducarServidor( $ref_cod_servidor, null, null, null, null, null, null, $ref_ref_cod_instituicao );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_servidor = $ref_cod_servidor;
						$this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_servidor = $ref_cod_servidor;
						$this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.servidor WHERE cod_servidor = '{$ref_cod_servidor}' AND ref_cod_instituicao = '{$ref_ref_cod_instituicao}'" ) )
				{
					$this->ref_cod_servidor = $ref_cod_servidor;
					$this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
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

		if( is_numeric( $this->ref_ref_cod_instituicao ) && is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->ref_cod_funcao ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_ref_cod_instituicao ) )
			{
				$campos .= "{$gruda}ref_ref_cod_instituicao";
				$valores .= "{$gruda}'{$this->ref_ref_cod_instituicao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_servidor ) )
			{
				$campos .= "{$gruda}ref_cod_servidor";
				$valores .= "{$gruda}'{$this->ref_cod_servidor}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_funcao ) )
			{
				$campos .= "{$gruda}ref_cod_funcao";
				$valores .= "{$gruda}'{$this->ref_cod_funcao}'";
				$gruda = ", ";
			}


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
		if( is_numeric( $this->ref_ref_cod_instituicao ) && is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->ref_cod_funcao ) )
		{

			$db = new clsBanco();
			$set = "";



			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_servidor = '{$this->ref_cod_servidor}' AND ref_cod_funcao = '{$this->ref_cod_funcao}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 *
	 * @return array
	 */
	function lista( $int_ref_ref_cod_instituicao =  null, $int_ref_cod_servidor = null, $int_ref_cod_funcao = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_ref_ref_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} ref_ref_cod_instituicao = '{$int_ref_ref_cod_instituicao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_servidor ) )
		{
			$filtros .= "{$whereAnd} ref_cod_servidor = '{$int_ref_cod_servidor}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_funcao ) )
		{
			$filtros .= "{$whereAnd} ref_cod_funcao = '{$int_ref_cod_funcao}'";
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
		if( is_numeric( $this->ref_ref_cod_instituicao ) && is_numeric( $this->ref_cod_servidor ) ) #&& is_numeric( $this->ref_cod_funcao ) )
		{
      $sql = sprintf(
        "SELECT %s FROM %s WHERE ref_ref_cod_instituicao = '%d' AND ref_cod_servidor = '%d'",
        $this->_todos_campos, $this->_tabela, $this->ref_ref_cod_instituicao,
        $this->ref_cod_servidor
      );

      if (is_numeric($this->ref_cod_funcao)) {
        $sql .= sprintf(" AND ref_cod_funcao = '%d'", $this->ref_cod_funcao);
      }

			$db = new clsBanco();
			#$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_servidor = '{$this->ref_cod_servidor}' AND ref_cod_funcao = '{$this->ref_cod_funcao}'" );
			$db->Consulta($sql);
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
		if( is_numeric( $this->ref_ref_cod_instituicao ) && is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->ref_cod_funcao ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_servidor = '{$this->ref_cod_servidor}' AND ref_cod_funcao = '{$this->ref_cod_funcao}'" );
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
		if( is_numeric( $this->ref_ref_cod_instituicao ) && is_numeric( $this->ref_cod_servidor ) && is_numeric( $this->ref_cod_funcao ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_servidor = '{$this->ref_cod_servidor}' AND ref_cod_funcao = '{$this->ref_cod_funcao}'" );
		return true;
		*/


		}
		return false;
	}

	/**
	 * Exclui todos registros
	 *
	 * @return bool
	 */
	function excluirTodos()
	{
		if( is_numeric( $this->ref_ref_cod_instituicao ) && is_numeric( $this->ref_cod_servidor ) )
		{

			$db = new clsBanco();
			$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_servidor = '{$this->ref_cod_servidor}'" );
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