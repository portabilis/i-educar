<?php
/**
* @author Prefeitura Municipal de Itajaí
*
* Criado em 25/05/2007 11:08 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarNivel
{
	var $cod_nivel;
	var $ref_cod_categoria_nivel;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_nivel_anterior;
	var $nm_nivel;
	var $salario_base;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

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
	 * @param integer cod_nivel
	 * @param integer ref_cod_categoria_nivel
	 * @param integer ref_usuario_exc
	 * @param integer ref_usuario_cad
	 * @param integer ref_cod_nivel_anterior
	 * @param string nm_nivel
	 * @param integer salario_base
	 * @param string data_cadastro
	 * @param string data_exclusao
	 * @param bool ativo
	 *
	 * @return object
	 */
	function clsPmieducarNivel( $cod_nivel = null, $ref_cod_categoria_nivel = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $ref_cod_nivel_anterior = null, $nm_nivel = null, $salario_base = null, $data_cadastro = null, $data_exclusao = null, $ativo = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}nivel";

		$this->_campos_lista = $this->_todos_campos = "cod_nivel, ref_cod_categoria_nivel, ref_usuario_exc, ref_usuario_cad, ref_cod_nivel_anterior, nm_nivel, salario_base, data_cadastro, data_exclusao, ativo";

		if( is_numeric( $ref_cod_categoria_nivel ) )
		{
			if( class_exists( "clsPmieducarCategoriaNivel" ) )
			{
				$tmp_obj = new clsPmieducarCategoriaNivel( $ref_cod_categoria_nivel );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_categoria_nivel = $ref_cod_categoria_nivel;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_categoria_nivel = $ref_cod_categoria_nivel;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.categoria_nivel WHERE cod_categoria_nivel = '{$ref_cod_categoria_nivel}'" ) )
				{
					$this->ref_cod_categoria_nivel = $ref_cod_categoria_nivel;
				}
			}
		}
		if( is_numeric( $ref_usuario_cad ) )
		{
			if( class_exists( "clsPmieducarUsuario" ) )
			{
				$tmp_obj = new clsPmieducarUsuario( $ref_usuario_cad );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_usuario_cad = $ref_usuario_cad;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_usuario_cad = $ref_usuario_cad;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_cad}'" ) )
				{
					$this->ref_usuario_cad = $ref_usuario_cad;
				}
			}
		}
		if( is_numeric( $ref_usuario_exc ) )
		{
			if( class_exists( "clsPmieducarUsuario" ) )
			{
				$tmp_obj = new clsPmieducarUsuario( $ref_usuario_exc );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_usuario_exc = $ref_usuario_exc;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_usuario_exc = $ref_usuario_exc;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_exc}'" ) )
				{
					$this->ref_usuario_exc = $ref_usuario_exc;
				}
			}
		}
		if( is_numeric( $ref_cod_nivel_anterior ) )
		{
			if( class_exists( "clsPmieducarNivel" ) )
			{
				$tmp_obj = new clsPmieducarNivel( $ref_cod_nivel_anterior );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_nivel_anterior = $ref_cod_nivel_anterior;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_nivel_anterior = $ref_cod_nivel_anterior;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.nivel WHERE cod_nivel = '{$ref_cod_nivel_anterior}'" ) )
				{
					$this->ref_cod_nivel_anterior = $ref_cod_nivel_anterior;
				}
			}
		}


		if( is_numeric( $cod_nivel ) )
		{
			$this->cod_nivel = $cod_nivel;
		}
		if( is_string( $nm_nivel ) )
		{
			$this->nm_nivel = $nm_nivel;
		}
		if( is_numeric( $salario_base ) )
		{
			$this->salario_base = $salario_base;
		}
		if( is_string( $data_cadastro ) )
		{
			$this->data_cadastro = $data_cadastro;
		}
		if( is_string( $data_exclusao ) )
		{
			$this->data_exclusao = $data_exclusao;
		}
		if( ! is_null( $ativo ) )
		{
			$this->ativo = $ativo;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_categoria_nivel ) && is_numeric( $this->ref_usuario_cad ) && is_string( $this->nm_nivel ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_categoria_nivel ) )
			{
				$campos .= "{$gruda}ref_cod_categoria_nivel";
				$valores .= "{$gruda}'{$this->ref_cod_categoria_nivel}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_nivel_anterior ) )
			{
				$campos .= "{$gruda}ref_cod_nivel_anterior";
				$valores .= "{$gruda}'{$this->ref_cod_nivel_anterior}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_nivel ) )
			{
				$campos .= "{$gruda}nm_nivel";
				$valores .= "{$gruda}'{$this->nm_nivel}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->salario_base ) )
			{
				$campos .= "{$gruda}salario_base";
				$valores .= "{$gruda}'{$this->salario_base}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_nivel_seq");
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
		if( is_numeric( $this->cod_nivel ) && is_numeric( $this->ref_usuario_exc ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_cod_categoria_nivel ) )
			{
				$set .= "{$gruda}ref_cod_categoria_nivel = '{$this->ref_cod_categoria_nivel}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_exc ) )
			{
				$set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_nivel_anterior ) )
			{
				$set .= "{$gruda}ref_cod_nivel_anterior = '{$this->ref_cod_nivel_anterior}'";
				$gruda = ", ";
			}
			else
			{
				$set .= "{$gruda}ref_cod_nivel_anterior = NULL";
				$gruda = ", ";
			}
			if( is_string( $this->nm_nivel ) )
			{
				$set .= "{$gruda}nm_nivel = '{$this->nm_nivel}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->salario_base ) )
			{
				$set .= "{$gruda}salario_base = '{$this->salario_base}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_cadastro ) )
			{
				$set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
				$gruda = ", ";
			}
			$set .= "{$gruda}data_exclusao = NOW()";
			$gruda = ", ";
			if( ! is_null( $this->ativo ) )
			{
				$val = dbBool( $this->ativo ) ? "TRUE": "FALSE";
				$set .= "{$gruda}ativo = {$val}";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_nivel = '{$this->cod_nivel}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @param integer int_ref_cod_categoria_nivel
	 * @param integer int_ref_usuario_exc
	 * @param integer int_ref_usuario_cad
	 * @param integer int_ref_cod_nivel_anterior
	 * @param string str_nm_nivel
	 * @param integer int_salario_base
	 * @param string date_data_cadastro_ini
	 * @param string date_data_cadastro_fim
	 * @param string date_data_exclusao_ini
	 * @param string date_data_exclusao_fim
	 * @param bool bool_ativo
	 *
	 * @return array
	 */
	function lista( $int_cod_nivel = null, $int_ref_cod_categoria_nivel = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_cod_nivel_anterior = null, $str_nm_nivel = null, $int_salario_base = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $bool_ativo = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_nivel ) )
		{
			$filtros .= "{$whereAnd} cod_nivel = '{$int_cod_nivel}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_categoria_nivel ) )
		{
			$filtros .= "{$whereAnd} ref_cod_categoria_nivel = '{$int_ref_cod_categoria_nivel}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_exc ) )
		{
			$filtros .= "{$whereAnd} ref_usuario_exc = '{$int_ref_usuario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_nivel_anterior ) )
		{
			$filtros .= "{$whereAnd} ref_cod_nivel_anterior = '{$int_ref_cod_nivel_anterior}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_nivel ) )
		{
			$filtros .= "{$whereAnd} nm_nivel LIKE '%{$str_nm_nivel}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_salario_base ) )
		{
			$filtros .= "{$whereAnd} salario_base = '{$int_salario_base}'";
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
		if( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( ! is_null( $bool_ativo ) )
		{
			if( dbBool( $bool_ativo ) )
			{
				$filtros .= "{$whereAnd} ativo = TRUE";
			}
			else
			{
				$filtros .= "{$whereAnd} ativo = FALSE";
			}
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
		if( is_numeric( $this->cod_nivel ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_nivel = '{$this->cod_nivel}'" );
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
		if( is_numeric( $this->cod_nivel ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_nivel = '{$this->cod_nivel}'" );
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
		if( is_numeric( $this->cod_nivel ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_nivel = '{$this->cod_nivel}'" );
		return true;
		*/

		$this->ativo = 0;
			return $this->edita();
		}
		return false;
	}

	/**
	 * Exclui registros de uma categoria
	 *
	 * @return bool
	 */
	function desativaTodos()
	{
		if( is_numeric( $this->ref_cod_categoria_nivel ) && is_numeric( $this->ref_usuario_exc ))
		{


			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->_tabela} set ativo = false, ref_usuario_exc = '{$this->ref_usuario_exc}', ref_cod_nivel_anterior = NULL WHERE ref_cod_categoria_nivel = '{$this->ref_cod_categoria_nivel}'" );
			return true;

		}
		return false;
	}

	function buscaSequenciaNivel($int_ref_cod_categoria_nivel)
	{
		if(is_numeric($int_ref_cod_categoria_nivel))
		{
			$db = new clsBanco();
			$db->Consulta("SELECT * from {$this->_tabela} WHERE ref_cod_nivel_anterior IS NULL AND ref_cod_categoria_nivel = {$int_ref_cod_categoria_nivel} AND ativo = true");

			$resultado = array();

			if($db->Num_Linhas())
			{
				$db->ProximoRegistro();

				$registro = $db->Tupla();

				$resultado[] = $registro;

				do
				{
					$db->Consulta("SELECT * from {$this->_tabela} WHERE ref_cod_nivel_anterior = {$registro['cod_nivel']}  AND ativo = true");

					$db->ProximoRegistro();

					$registro = $db->Tupla();

					if($registro)
						$resultado[] = $registro;

				}while ($registro);

				return $resultado;

			}
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