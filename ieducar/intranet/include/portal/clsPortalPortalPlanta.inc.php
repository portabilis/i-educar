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
* Criado em 02/03/2007 16:05 pelo gerador automatico de classes
*/

require_once( "include/portal/geral.inc.php" );

class clsPortalPortalPlanta
{
	var $cod_planta;
	var $local_plantio;
	var $data;
	var $observacao;
	var $nm_planta;
	var $nm_cientifico;
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
	 * @param integer cod_planta
	 * @param string local_plantio
	 * @param string data
	 * @param string observacao
	 * @param string nm_planta
	 * @param string nm_cientifico
	 * @param integer ativo
	 *
	 * @return object
	 */
	function clsPortalPortalPlanta( $cod_planta = null, $local_plantio = null, $data = null, $observacao = null, $nm_planta = null, $nm_cientifico = null, $ativo = null )
	{
		$db = new clsBanco();
		$this->_schema = "portal.";
		$this->_tabela = "{$this->_schema}portal_planta";

		$this->_campos_lista = $this->_todos_campos = "cod_planta, local_plantio, data, observacao, nm_planta, nm_cientifico, ativo";



		if( is_numeric( $cod_planta ) )
		{
			$this->cod_planta = $cod_planta;
		}
		if( is_string( $local_plantio ) )
		{
			$this->local_plantio = $local_plantio;
		}
		if( is_string( $data ) )
		{
			$this->data = $data;
		}
		if( is_string( $observacao ) )
		{
			$this->observacao = $observacao;
		}
		if( is_string( $nm_planta ) )
		{
			$this->nm_planta = $nm_planta;
		}
		if( is_string( $nm_cientifico ) )
		{
			$this->nm_cientifico = $nm_cientifico;
		}
		if( is_numeric( $ativo ) )
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
		if( is_string( $this->local_plantio ) && is_string( $this->data ) && is_string( $this->observacao ) && is_string( $this->nm_planta ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_string( $this->local_plantio ) )
			{
				$campos .= "{$gruda}local_plantio";
				$valores .= "{$gruda}'{$this->local_plantio}'";
				$gruda = ", ";
			}
			if( is_string( $this->data ) )
			{
				$campos .= "{$gruda}data";
				$valores .= "{$gruda}'{$this->data}'";
				$gruda = ", ";
			}
			if( is_string( $this->observacao ) )
			{
				$campos .= "{$gruda}observacao";
				$valores .= "{$gruda}'{$this->observacao}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_planta ) )
			{
				$campos .= "{$gruda}nm_planta";
				$valores .= "{$gruda}'{$this->nm_planta}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_cientifico ) )
			{
				$campos .= "{$gruda}nm_cientifico";
				$valores .= "{$gruda}'{$this->nm_cientifico}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_planta_seq");
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
		if( is_numeric( $this->cod_planta ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_string( $this->local_plantio ) )
			{
				$set .= "{$gruda}local_plantio = '{$this->local_plantio}'";
				$gruda = ", ";
			}
			if( is_string( $this->data ) )
			{
				$set .= "{$gruda}data = '{$this->data}'";
				$gruda = ", ";
			}
			if( is_string( $this->observacao ) )
			{
				$set .= "{$gruda}observacao = '{$this->observacao}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_planta ) )
			{
				$set .= "{$gruda}nm_planta = '{$this->nm_planta}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_cientifico ) )
			{
				$set .= "{$gruda}nm_cientifico = '{$this->nm_cientifico}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ativo ) )
			{
				$set .= "{$gruda}ativo = '{$this->ativo}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_planta = '{$this->cod_planta}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 * 
	 * @param string str_local_plantio
	 * @param string str_data
	 * @param string str_observacao
	 * @param string str_nm_planta
	 * @param string str_nm_cientifico
	 * @param integer int_ativo
	 *
	 * @return array
	 */
	function lista( $str_local_plantio = null, $str_data = null, $str_observacao = null, $str_nm_planta = null, $str_nm_cientifico = null, $int_ativo = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_planta ) )
		{
			$filtros .= "{$whereAnd} cod_planta = '{$int_cod_planta}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_local_plantio ) )
		{
			$filtros .= "{$whereAnd} local_plantio LIKE '%{$str_local_plantio}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_data ) )
		{
			$filtros .= "{$whereAnd} data LIKE '%{$str_data}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_observacao ) )
		{
			$filtros .= "{$whereAnd} observacao LIKE '%{$str_observacao}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_planta ) )
		{
			$filtros .= "{$whereAnd} nm_planta LIKE '%{$str_nm_planta}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_cientifico ) )
		{
			$filtros .= "{$whereAnd} nm_cientifico LIKE '%{$str_nm_cientifico}%'";
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
		if( is_numeric( $this->cod_planta ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_planta = '{$this->cod_planta}'" );
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
		if( is_numeric( $this->cod_planta ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_planta = '{$this->cod_planta}'" );
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
		if( is_numeric( $this->cod_planta ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_planta = '{$this->cod_planta}'" );
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