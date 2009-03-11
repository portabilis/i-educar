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
* Criado em 06/07/2006 08:28 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarSeriePeriodoData
{
	var $ref_cod_serie;
	var $sequencial;
	var $ref_cod_serie_tipo_periodo_ano;
	var $data_inicial;
	var $data_final;

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
	function clsPmieducarSeriePeriodoData( $ref_cod_serie = null, $sequencial = null, $ref_cod_serie_tipo_periodo_ano = null, $data_inicial = null, $data_final = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}serie_periodo_data";

		$this->_campos_lista = $this->_todos_campos = "ref_cod_serie, sequencial, ref_cod_serie_tipo_periodo_ano, data_inicial, data_final";

		if( is_numeric( $ref_cod_serie_tipo_periodo_ano ) )
		{
			if( class_exists( "clsPmieducarSerieTipoPeriodoAno" ) )
			{
				$tmp_obj = new clsPmieducarSerieTipoPeriodoAno( $ref_cod_serie_tipo_periodo_ano );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_serie_tipo_periodo_ano = $ref_cod_serie_tipo_periodo_ano;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_serie_tipo_periodo_ano = $ref_cod_serie_tipo_periodo_ano;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.serie_tipo_periodo_ano WHERE cod_serie_tipo_periodo_ano = '{$ref_cod_serie_tipo_periodo_ano}'" ) )
				{
					$this->ref_cod_serie_tipo_periodo_ano = $ref_cod_serie_tipo_periodo_ano;
				}
			}
		}
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


		if( is_numeric( $sequencial ) )
		{
			$this->sequencial = $sequencial;
		}
		if( is_string( $data_inicial ) )
		{
			$this->data_inicial = $data_inicial;
		}
		if( is_string( $data_final ) )
		{
			$this->data_final = $data_final;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{

		if( is_numeric( $this->ref_cod_serie ) && is_numeric( $this->sequencial ) && is_numeric( $this->ref_cod_serie_tipo_periodo_ano ) && is_string( $this->data_inicial ) && is_string( $this->data_final ) )
		{

			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_serie ) )
			{
				$campos .= "{$gruda}ref_cod_serie";
				$valores .= "{$gruda}'{$this->ref_cod_serie}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->sequencial ) )
			{
				$campos .= "{$gruda}sequencial";
				$valores .= "{$gruda}'{$this->sequencial}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_serie_tipo_periodo_ano ) )
			{
				$campos .= "{$gruda}ref_cod_serie_tipo_periodo_ano";
				$valores .= "{$gruda}'{$this->ref_cod_serie_tipo_periodo_ano}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_inicial ) )
			{
				$campos .= "{$gruda}data_inicial";
				$valores .= "{$gruda}'{$this->data_inicial}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_final ) )
			{
				$campos .= "{$gruda}data_final";
				$valores .= "{$gruda}'{$this->data_final}'";
				$gruda = ", ";
			}

			//echo "<pre>INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )";
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

		if( is_numeric( $this->ref_cod_serie ) && is_numeric( $this->sequencial ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_cod_serie_tipo_periodo_ano ) )
			{
				$set .= "{$gruda}ref_cod_serie_tipo_periodo_ano = '{$this->ref_cod_serie_tipo_periodo_ano}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_inicial ) )
			{
				$set .= "{$gruda}data_inicial = '{$this->data_inicial}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_final ) )
			{
				$set .= "{$gruda}data_final = '{$this->data_final}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_cod_serie = '{$this->ref_cod_serie}' AND sequencial = '{$this->sequencial}'" );
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
	function lista( $int_ref_cod_serie = null, $int_sequencial = null, $int_ref_cod_serie_tipo_periodo_ano = null, $date_data_inicial_ini = null, $date_data_inicial_fim = null, $date_data_final_ini = null, $date_data_final_fim = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_ref_cod_serie ) )
		{
			$filtros .= "{$whereAnd} ref_cod_serie = '{$int_ref_cod_serie}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_sequencial ) )
		{
			$filtros .= "{$whereAnd} sequencial = '{$int_sequencial}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_serie_tipo_periodo_ano ) )
		{
			$filtros .= "{$whereAnd} ref_cod_serie_tipo_periodo_ano = '{$int_ref_cod_serie_tipo_periodo_ano}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_inicial_ini ) )
		{
			$filtros .= "{$whereAnd} data_inicial >= '{$date_data_inicial_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_inicial_fim ) )
		{
			$filtros .= "{$whereAnd} data_inicial <= '{$date_data_inicial_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_final_ini ) )
		{
			$filtros .= "{$whereAnd} data_final >= '{$date_data_final_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_final_fim ) )
		{
			$filtros .= "{$whereAnd} data_final <= '{$date_data_final_fim}'";
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
		if( is_numeric( $this->ref_cod_serie ) && is_numeric( $this->sequencial ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_serie = '{$this->ref_cod_serie}' AND sequencial = '{$this->sequencial}'" );
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
		if( is_numeric( $this->ref_cod_serie ) && is_numeric( $this->sequencial ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_cod_serie = '{$this->ref_cod_serie}' AND sequencial = '{$this->sequencial}'" );
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
		if( is_numeric( $this->ref_cod_serie ) && is_numeric( $this->sequencial ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_serie = '{$this->ref_cod_serie}' AND sequencial = '{$this->sequencial}'" );
		return true;
		*/


		}
		return false;
	}

	/**
	 * Exclui todos os registros referentes a um tipo de avaliacao
	 */
	function  excluirTodos()
	{
		if ( is_numeric( $this->ref_cod_serie ) ) {
			$db = new clsBanco();
			$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_serie = '{$this->ref_cod_serie}'" );
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