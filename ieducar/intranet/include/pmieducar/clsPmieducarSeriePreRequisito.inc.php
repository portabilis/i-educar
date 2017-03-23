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
* @author Prefeitura Municipal de Itajaí
*
* Criado em 14/07/2006 09:28 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarSeriePreRequisito
{
	var $ref_cod_pre_requisito;
	var $ref_cod_operador;
	var $ref_cod_serie;
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
	 * @return object
	 */
	function clsPmieducarSeriePreRequisito( $ref_cod_pre_requisito = null, $ref_cod_operador = null, $ref_cod_serie = null, $valor = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}serie_pre_requisito";

		$this->_campos_lista = $this->_todos_campos = "ref_cod_pre_requisito, ref_cod_operador, ref_cod_serie, valor";

		if( is_numeric( $ref_cod_serie ) )
		{
			if( class_exists( "clsPmieducarSerie" ) )
			{
				$tmp_obj = new clsPmieducarSerie( $ref_cod_serie );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_serie = $ref_cod_serie;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_serie = $ref_cod_serie;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.serie WHERE cod_serie = '{$ref_cod_serie}'" ) )
				{
					$this->ref_cod_serie = $ref_cod_serie;
				}
			}
		}
		if( is_numeric( $ref_cod_operador ) )
		{
			if( class_exists( "clsPmieducarOperador" ) )
			{
				$tmp_obj = new clsPmieducarOperador( $ref_cod_operador );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_operador = $ref_cod_operador;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_operador = $ref_cod_operador;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.operador WHERE cod_operador = '{$ref_cod_operador}'" ) )
				{
					$this->ref_cod_operador = $ref_cod_operador;
				}
			}
		}
		if( is_numeric( $ref_cod_pre_requisito ) )
		{
			if( class_exists( "clsPmieducarPreRequisito" ) )
			{
				$tmp_obj = new clsPmieducarPreRequisito( $ref_cod_pre_requisito );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_pre_requisito = $ref_cod_pre_requisito;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_pre_requisito = $ref_cod_pre_requisito;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.pre_requisito WHERE cod_pre_requisito = '{$ref_cod_pre_requisito}'" ) )
				{
					$this->ref_cod_pre_requisito = $ref_cod_pre_requisito;
				}
			}
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
		if( is_numeric( $this->ref_cod_pre_requisito ) && is_numeric( $this->ref_cod_operador ) && is_numeric( $this->ref_cod_serie ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_pre_requisito ) )
			{
				$campos .= "{$gruda}ref_cod_pre_requisito";
				$valores .= "{$gruda}'{$this->ref_cod_pre_requisito}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_operador ) )
			{
				$campos .= "{$gruda}ref_cod_operador";
				$valores .= "{$gruda}'{$this->ref_cod_operador}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_serie ) )
			{
				$campos .= "{$gruda}ref_cod_serie";
				$valores .= "{$gruda}'{$this->ref_cod_serie}'";
				$gruda = ", ";
			}
			if( is_string( $this->valor ) )
			{
				$campos .= "{$gruda}valor";
				$valores .= "{$gruda}'{$this->valor}'";
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
		if( is_numeric( $this->ref_cod_pre_requisito ) && is_numeric( $this->ref_cod_operador ) && is_numeric( $this->ref_cod_serie ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_string( $this->valor ) )
			{
				$set .= "{$gruda}valor = '{$this->valor}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_cod_pre_requisito = '{$this->ref_cod_pre_requisito}' AND ref_cod_operador = '{$this->ref_cod_operador}' AND ref_cod_serie = '{$this->ref_cod_serie}'" );
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
	function lista( $int_ref_cod_pre_requisito = null, $int_ref_cod_operador = null, $int_ref_cod_serie = null, $str_valor = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_ref_cod_pre_requisito ) )
		{
			$filtros .= "{$whereAnd} ref_cod_pre_requisito = '{$int_ref_cod_pre_requisito}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_operador ) )
		{
			$filtros .= "{$whereAnd} ref_cod_operador = '{$int_ref_cod_operador}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_serie ) )
		{
			$filtros .= "{$whereAnd} ref_cod_serie = '{$int_ref_cod_serie}'";
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
		if( is_numeric( $this->ref_cod_pre_requisito ) && is_numeric( $this->ref_cod_operador ) && is_numeric( $this->ref_cod_serie ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_pre_requisito = '{$this->ref_cod_pre_requisito}' AND ref_cod_operador = '{$this->ref_cod_operador}' AND ref_cod_serie = '{$this->ref_cod_serie}'" );
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
		if( is_numeric( $this->ref_cod_pre_requisito ) && is_numeric( $this->ref_cod_operador ) && is_numeric( $this->ref_cod_serie ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_cod_pre_requisito = '{$this->ref_cod_pre_requisito}' AND ref_cod_operador = '{$this->ref_cod_operador}' AND ref_cod_serie = '{$this->ref_cod_serie}'" );
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
		if( is_numeric( $this->ref_cod_pre_requisito ) && is_numeric( $this->ref_cod_operador ) && is_numeric( $this->ref_cod_serie ) )
		{
			$db = new clsBanco();
			$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_pre_requisito = '{$this->ref_cod_pre_requisito}' AND ref_cod_operador = '{$this->ref_cod_operador}' AND ref_cod_serie = '{$this->ref_cod_serie}'" );
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