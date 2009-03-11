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
* Criado em 05/03/2007 16:41 pelo gerador automatico de classes
*/

//require_once( "include/portal/geral.inc.php" );

class clsPortalPortalPlantaNascimento
{
	var $cod_planta_nasc;
	var $crianca;
	var $pai;
	var $mae;
	var $data_nasc;
	var $bairro;
	var $planta;

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
	 * @param integer cod_planta_nasc
	 * @param string crianca
	 * @param string pai
	 * @param string mae
	 * @param string data_nasc
	 * @param string bairro
	 * @param string planta
	 *
	 * @return object
	 */
	function clsPortalPortalPlantaNascimento( $cod_planta_nasc = null, $crianca = null, $pai = null, $mae = null, $data_nasc = null, $bairro = null, $planta = null )
	{
		$db = new clsBanco();
		$this->_schema = "portal.";
		$this->_tabela = "{$this->_schema}portal_planta_nascimento";

		$this->_campos_lista = $this->_todos_campos = "cod_planta_nasc, crianca, pai, mae, data_nasc, bairro, planta";



		if( is_numeric( $cod_planta_nasc ) )
		{
			$this->cod_planta_nasc = $cod_planta_nasc;
		}
		if( is_string( $crianca ) )
		{
			$this->crianca = $crianca;
		}
		if( is_string( $pai ) )
		{
			$this->pai = $pai;
		}
		if( is_string( $mae ) )
		{
			$this->mae = $mae;
		}
		if( is_string( $data_nasc ) )
		{
			$this->data_nasc = $data_nasc;
		}
		if( is_string( $bairro ) )
		{
			$this->bairro = $bairro;
		}
		if( is_string( $planta ) )
		{
			$this->planta = $planta;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_string( $this->crianca ) && is_string( $this->pai ) && is_string( $this->mae ) && is_string( $this->data_nasc ) && is_string( $this->bairro ) && is_string( $this->planta ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_string( $this->crianca ) )
			{
				$campos .= "{$gruda}crianca";
				$valores .= "{$gruda}'{$this->crianca}'";
				$gruda = ", ";
			}
			if( is_string( $this->pai ) )
			{
				$campos .= "{$gruda}pai";
				$valores .= "{$gruda}'{$this->pai}'";
				$gruda = ", ";
			}
			if( is_string( $this->mae ) )
			{
				$campos .= "{$gruda}mae";
				$valores .= "{$gruda}'{$this->mae}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_nasc ) )
			{
				$campos .= "{$gruda}data_nasc";
				$valores .= "{$gruda}'{$this->data_nasc}'";
				$gruda = ", ";
			}
			if( is_string( $this->bairro ) )
			{
				$campos .= "{$gruda}bairro";
				$valores .= "{$gruda}'{$this->bairro}'";
				$gruda = ", ";
			}
			if( is_string( $this->planta ) )
			{
				$campos .= "{$gruda}planta";
				$valores .= "{$gruda}'{$this->planta}'";
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_planta_nasc_seq");
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
		if( is_numeric( $this->cod_planta_nasc ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_string( $this->crianca ) )
			{
				$set .= "{$gruda}crianca = '{$this->crianca}'";
				$gruda = ", ";
			}
			if( is_string( $this->pai ) )
			{
				$set .= "{$gruda}pai = '{$this->pai}'";
				$gruda = ", ";
			}
			if( is_string( $this->mae ) )
			{
				$set .= "{$gruda}mae = '{$this->mae}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_nasc ) )
			{
				$set .= "{$gruda}data_nasc = '{$this->data_nasc}'";
				$gruda = ", ";
			}
			if( is_string( $this->bairro ) )
			{
				$set .= "{$gruda}bairro = '{$this->bairro}'";
				$gruda = ", ";
			}
			if( is_string( $this->planta ) )
			{
				$set .= "{$gruda}planta = '{$this->planta}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_planta_nasc = '{$this->cod_planta_nasc}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 * 
	 * @param string str_crianca
	 * @param string str_pai
	 * @param string str_mae
	 * @param string date_data_nasc_ini
	 * @param string date_data_nasc_fim
	 * @param string str_bairro
	 * @param string str_planta
	 *
	 * @return array
	 */
	function lista( $str_crianca = null, $str_pai = null, $str_mae = null, $date_data_nasc_ini = null, $date_data_nasc_fim = null, $str_bairro = null, $str_planta = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_planta_nasc ) )
		{
			$filtros .= "{$whereAnd} cod_planta_nasc = '{$int_cod_planta_nasc}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_crianca ) )
		{
			$filtros .= "{$whereAnd} crianca LIKE '%{$str_crianca}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_pai ) )
		{
			$filtros .= "{$whereAnd} pai LIKE '%{$str_pai}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_mae ) )
		{
			$filtros .= "{$whereAnd} mae LIKE '%{$str_mae}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_nasc_ini ) )
		{
			$filtros .= "{$whereAnd} data_nasc >= '{$date_data_nasc_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_nasc_fim ) )
		{
			$filtros .= "{$whereAnd} data_nasc <= '{$date_data_nasc_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_bairro ) )
		{
			$filtros .= "{$whereAnd} bairro LIKE '%{$str_bairro}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_planta ) )
		{
			$filtros .= "{$whereAnd} planta LIKE '%{$str_planta}%'";
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
		if( is_numeric( $this->cod_planta_nasc ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_planta_nasc = '{$this->cod_planta_nasc}'" );
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
		if( is_numeric( $this->cod_planta_nasc ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_planta_nasc = '{$this->cod_planta_nasc}'" );
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
		if( is_numeric( $this->cod_planta_nasc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_planta_nasc = '{$this->cod_planta_nasc}'" );
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