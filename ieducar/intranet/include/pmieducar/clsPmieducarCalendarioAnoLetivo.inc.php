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
* @author Prefeitura Municipal de Itajaï¿½
*
* Criado em 19/07/2006 09:11 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarCalendarioAnoLetivo
{
	var $cod_calendario_ano_letivo;
	var $ref_cod_escola;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ano;
	var $data_cadastra;
	var $data_exclusao;
	var $ativo;
	/*
	var $inicio_ano_letivo;
	var $termino_ano_letivo;
	*/
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
	 * @return object
	 */
	function clsPmieducarCalendarioAnoLetivo( $cod_calendario_ano_letivo = null, $ref_cod_escola = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $ano = null, $data_cadastra = null, $data_exclusao = null, $ativo = null/*, $inicio_ano_letivo = null, $termino_ano_letivo = null*/)
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}calendario_ano_letivo";

		$this->_campos_lista = $this->_todos_campos = "cod_calendario_ano_letivo, ref_cod_escola, ref_usuario_exc, ref_usuario_cad, ano, data_cadastra, data_exclusao, ativo";/*, inicio_ano_letivo, termino_ano_letivo";*/

		if( is_numeric( $ref_cod_escola ) )
		{
			if( class_exists( "clsPmieducarEscola" ) )
			{
				$tmp_obj = new clsPmieducarEscola( $ref_cod_escola );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_escola = $ref_cod_escola;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_escola = $ref_cod_escola;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.escola WHERE cod_escola = '{$ref_cod_escola}'" ) )
				{
					$this->ref_cod_escola = $ref_cod_escola;
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


		if( is_numeric( $cod_calendario_ano_letivo ) )
		{
			$this->cod_calendario_ano_letivo = $cod_calendario_ano_letivo;
		}
		if( is_numeric( $ano ) )
		{
			$this->ano = $ano;
		}
		if( is_string( $data_cadastra ) )
		{
			$this->data_cadastra = $data_cadastra;
		}
		if( is_string( $data_exclusao ) )
		{
			$this->data_exclusao = $data_exclusao;
		}
		if( is_numeric( $ativo ) )
		{
			$this->ativo = $ativo;
		}
		/*
		if( is_string( $inicio_ano_letivo ) )
		{
			$this->inicio_ano_letivo = $inicio_ano_letivo;
		}
		if( is_string( $termino_ano_letivo ) )
		{
			$this->termino_ano_letivo = $termino_ano_letivo;
		}
		*/

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ano ) /*&& is_string( $this->inicio_ano_letivo ) && is_string( $this->termino_ano_letivo ) */)
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_escola ) )
			{
				$campos .= "{$gruda}ref_cod_escola";
				$valores .= "{$gruda}'{$this->ref_cod_escola}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ano ) )
			{
				$campos .= "{$gruda}ano";
				$valores .= "{$gruda}'{$this->ano}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastra";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";
			/*
			if( is_string( $this->inicio_ano_letivo ) )
			{
				$campos .= "{$gruda}inicio_ano_letivo";
				$valores .= "{$gruda}'{$this->inicio_ano_letivo}'";
				$gruda = ", ";
			}
			if( is_string( $this->termino_ano_letivo ) )
			{
				$campos .= "{$gruda}termino_ano_letivo";
				$valores .= "{$gruda}'{$this->termino_ano_letivo}'";
				$gruda = ", ";
			}
			*/

			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_calendario_ano_letivo_seq");
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
		if( is_numeric( $this->cod_calendario_ano_letivo ) && is_numeric( $this->ref_usuario_exc ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_cod_escola ) )
			{
				$set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
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
			if( is_numeric( $this->ano ) )
			{
				$set .= "{$gruda}ano = '{$this->ano}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_cadastra ) )
			{
				$set .= "{$gruda}data_cadastra = '{$this->data_cadastra}'";
				$gruda = ", ";
			}
			$set .= "{$gruda}data_exclusao = NOW()";
			$gruda = ", ";
			if( is_numeric( $this->ativo ) )
			{
				$set .= "{$gruda}ativo = '{$this->ativo}'";
				$gruda = ", ";
			}
			/*
			if( is_string( $this->inicio_ano_letivo ) )
			{
				$set .= "{$gruda}inicio_ano_letivo = '{$this->inicio_ano_letivo}'";
				$gruda = ", ";
			}
			if( is_string( $this->termino_ano_letivo ) )
			{
				$set .= "{$gruda}termino_ano_letivo = '{$this->termino_ano_letivo}'";
				$gruda = ", ";
			}
			*/

			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_calendario_ano_letivo = '{$this->cod_calendario_ano_letivo}'" );
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
	function lista( $int_cod_calendario_ano_letivo = null, $int_ref_cod_escola = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ano = null, $date_data_cadastra_ini = null, $date_data_cadastra_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, /*$date_inicio_ano_letivo_ini = null, $date_inicio_ano_letivo_fim = null, $date_termino_ano_letivo_ini = null, $date_termino_ano_letivo_fim = null ,*/$max_ano = null, $int_ref_cod_instituicao = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_calendario_ano_letivo ) )
		{
			$filtros .= "{$whereAnd} cod_calendario_ano_letivo = '{$int_cod_calendario_ano_letivo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_escola ) )
		{
			$filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
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
		if( is_numeric( $int_ano ) )
		{
			$filtros .= "{$whereAnd} ano = '{$int_ano}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastra_ini ) )
		{
			$filtros .= "{$whereAnd} data_cadastra >= '{$date_data_cadastra_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastra_fim ) )
		{
			$filtros .= "{$whereAnd} data_cadastra <= '{$date_data_cadastra_fim}'";
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
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} ativo = '0'";
			$whereAnd = " AND ";
		}
		/*
		if( is_string( $date_inicio_ano_letivo_ini ) )
		{
			$filtros .= "{$whereAnd} inicio_ano_letivo >= '{$date_inicio_ano_letivo_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_inicio_ano_letivo_fim ) )
		{
			$filtros .= "{$whereAnd} inicio_ano_letivo <= '{$date_inicio_ano_letivo_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_termino_ano_letivo_ini ) )
		{
			$filtros .= "{$whereAnd} termino_ano_letivo >= '{$date_termino_ano_letivo_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_termino_ano_letivo_fim ) )
		{
			$filtros .= "{$whereAnd} termino_ano_letivo <= '{$date_termino_ano_letivo_fim}'";
			$whereAnd = " AND ";
		}
		*/
		if( is_bool( $max_ano ) )
		{
			$filtros .= "{$whereAnd} ano = ( SELECT min(ano) FROM pmieducar.calendario_ano_letivo max_ano WHERE max_ano.ref_cod_escola = ref_cod_escola )";
			$whereAnd = " AND ";
		}

		if( is_numeric( $int_ref_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} ref_cod_escola in ( SELECT ref_cod_escola FROM pmieducar.escola escola WHERE escola.ref_cod_instituicao = '{$int_ref_cod_instituicao}' )";
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
		if( is_numeric( $this->cod_calendario_ano_letivo ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_calendario_ano_letivo = '{$this->cod_calendario_ano_letivo}'" );
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
		if( is_numeric( $this->cod_calendario_ano_letivo ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_calendario_ano_letivo = '{$this->cod_calendario_ano_letivo}'" );
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
		if( is_numeric( $this->cod_calendario_ano_letivo ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_calendario_ano_letivo = '{$this->cod_calendario_ano_letivo}'" );
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

}
?>