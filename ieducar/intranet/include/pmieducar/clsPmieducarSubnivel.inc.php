<?php
/**
* @author Prefeitura Municipal de Itajaí
*
* Criado em 25/05/2007 11:08 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarSubnivel
{
	var $cod_subnivel;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_subnivel_anterior;
	var $ref_cod_nivel;
	var $nm_subnivel;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $salario;

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
	 * @param integer cod_subnivel
	 * @param integer ref_usuario_exc
	 * @param integer ref_usuario_cad
	 * @param integer ref_cod_subnivel_anterior
	 * @param integer ref_cod_nivel
	 * @param string nm_subnivel
	 * @param string data_cadastro
	 * @param string data_exclusao
	 * @param bool ativo
	 *
	 * @return object
	 */
	function clsPmieducarSubnivel( $cod_subnivel = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $ref_cod_subnivel_anterior = null, $ref_cod_nivel = null, $nm_subnivel = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $salario = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}subnivel";

		$this->_campos_lista = $this->_todos_campos = "cod_subnivel, ref_usuario_exc, ref_usuario_cad, ref_cod_subnivel_anterior, ref_cod_nivel, nm_subnivel, data_cadastro, data_exclusao, ativo, salario";

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
		if( is_numeric( $ref_cod_subnivel_anterior ) )
		{
			if( class_exists( "clsPmieducarSubnivel" ) )
			{
				$tmp_obj = new clsPmieducarSubnivel( $ref_cod_subnivel_anterior );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_subnivel_anterior = $ref_cod_subnivel_anterior;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_subnivel_anterior = $ref_cod_subnivel_anterior;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.subnivel WHERE cod_subnivel = '{$ref_cod_subnivel_anterior}'" ) )
				{
					$this->ref_cod_subnivel_anterior = $ref_cod_subnivel_anterior;
				}
			}
		}
		if( is_numeric( $ref_cod_nivel ) )
		{
			if( class_exists( "clsPmieducarNivel" ) )
			{
				$tmp_obj = new clsPmieducarNivel( $ref_cod_nivel );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_nivel = $ref_cod_nivel;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_nivel = $ref_cod_nivel;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.nivel WHERE cod_nivel = '{$ref_cod_nivel}'" ) )
				{
					$this->ref_cod_nivel = $ref_cod_nivel;
				}
			}
		}


		if( is_numeric( $cod_subnivel ) )
		{
			$this->cod_subnivel = $cod_subnivel;
		}
		if( is_string( $nm_subnivel ) )
		{
			$this->nm_subnivel = $nm_subnivel;
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
		if( is_numeric( $salario ) )
		{
			$this->salario = $salario;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_cod_nivel )  && is_numeric( $this->salario ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_subnivel_anterior ) )
			{
				$campos .= "{$gruda}ref_cod_subnivel_anterior";
				$valores .= "{$gruda}'{$this->ref_cod_subnivel_anterior}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_nivel ) )
			{
				$campos .= "{$gruda}ref_cod_nivel";
				$valores .= "{$gruda}'{$this->ref_cod_nivel}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_subnivel ) )
			{
				$campos .= "{$gruda}nm_subnivel";
				$valores .= "{$gruda}'{$this->nm_subnivel}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->salario ) )
			{
				$campos .= "{$gruda}salario";
				$valores .= "{$gruda}'{$this->salario}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_subnivel_seq");
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
		if( is_numeric( $this->cod_subnivel ) && is_numeric( $this->ref_usuario_exc ) )
		{

			$db = new clsBanco();
			$set = "";

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
			if( is_numeric( $this->ref_cod_subnivel_anterior ) )
			{
				$set .= "{$gruda}ref_cod_subnivel_anterior = '{$this->ref_cod_subnivel_anterior}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_nivel ) )
			{
				$set .= "{$gruda}ref_cod_nivel = '{$this->ref_cod_nivel}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_subnivel ) )
			{
				$set .= "{$gruda}nm_subnivel = '{$this->nm_subnivel}'";
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
			if( is_numeric( $this->salario ) )
			{
				$set .= "{$gruda}salario = '{$this->salario}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_subnivel = '{$this->cod_subnivel}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @param integer int_ref_usuario_exc
	 * @param integer int_ref_usuario_cad
	 * @param integer int_ref_cod_subnivel_anterior
	 * @param integer int_ref_cod_nivel
	 * @param string str_nm_nivel
	 * @param string date_data_cadastro_ini
	 * @param string date_data_cadastro_fim
	 * @param string date_data_exclusao_ini
	 * @param string date_data_exclusao_fim
	 * @param bool bool_ativo
	 *
	 * @return array
	 */
	function lista( $int_cod_subnivel = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_cod_subnivel_anterior = null, $int_ref_cod_nivel = null, $str_nm_nivel = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $bool_ativo = null, $int_salario = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_subnivel ) )
		{
			$filtros .= "{$whereAnd} cod_subnivel = '{$int_cod_subnivel}'";
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
		if( is_numeric( $int_ref_cod_subnivel_anterior ) )
		{
			$filtros .= "{$whereAnd} ref_cod_subnivel_anterior = '{$int_ref_cod_subnivel_anterior}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_nivel ) )
		{
			$filtros .= "{$whereAnd} ref_cod_nivel = '{$int_ref_cod_nivel}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_nivel ) )
		{
			$filtros .= "{$whereAnd} nm_subnivel LIKE '%{$str_nm_nivel}%'";
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
		if( is_numeric( $int_salario ) )
		{
			$filtros .= "{$whereAnd} salario = '{$int_salario}'";
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
		if( is_numeric( $this->cod_subnivel ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_subnivel = '{$this->cod_subnivel}'" );
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

		if( is_numeric( $this->cod_subnivel ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_subnivel = '{$this->cod_subnivel}'" );
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
		if( is_numeric( $this->cod_subnivel ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_subnivel = '{$this->cod_subnivel}'" );
		return true;
		*/

		$this->ativo = 0;
			return $this->edita();
		}
		return false;
	}

	/**
	 * Exclui registros de uma categoria
	 * @param $niveis_not_in desativa todos os subniveis que nao se encontram no parametro ref_cod_nivel NOT IN (1,2,3)
	 * @return bool
	 */
	function desativaTodos($niveis_not_in)
	{
		if(is_array($niveis_not_in))
		{
			$niveis_not_in = implode($niveis_not_in,",");

			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->_tabela} set ativo = false, ref_usuario_exc = '{$this->ref_usuario_exc}', ref_cod_subnivel_anterior = NULL WHERE ref_cod_nivel NOT IN ($niveis_not_in)" );
			return true;
		}
		if( is_numeric( $this->ref_cod_nivel ) && is_numeric( $this->ref_usuario_exc ))
		{

			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->_tabela} set ativo = false, ref_usuario_exc = '{$this->ref_usuario_exc}', ref_cod_subnivel_anterior = NULL WHERE ref_cod_nivel = '{$this->ref_cod_nivel}'" );
			return true;

		}
		return false;
	}

	function buscaSequenciaSubniveis($int_ref_cod_nivel)
	{
		if(is_numeric($int_ref_cod_nivel))
		{
			$db = new clsBanco();
			$db->Consulta("SELECT * from {$this->_tabela} WHERE ref_cod_subnivel_anterior IS NULL AND ref_cod_nivel = {$int_ref_cod_nivel} AND ativo = true");

			$resultado = array();

			if($db->Num_Linhas())
			{
				$db->ProximoRegistro();

				$registro = $db->Tupla();

				$resultado[] = $registro;

				do
				{
					$db->Consulta("SELECT * from {$this->_tabela} WHERE ref_cod_subnivel_anterior = {$registro['cod_subnivel']}  AND ativo = true");

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