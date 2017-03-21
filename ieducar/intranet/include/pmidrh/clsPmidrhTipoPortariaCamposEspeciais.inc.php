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
* Criado em 15/12/2006 14:24 pelo gerador automatico de classes
*/

require_once( "include/pmidrh/geral.inc.php" );

class clsPmidrhTipoPortariaCamposEspeciais
{
	var $ref_cod_tipo_portaria;
	var $cod_campo;
	var $tipo;
	var $sequencial;
	var $ordem;
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
	 * @param integer ref_cod_tipo_portaria
	 * @param integer cod_campo
	 * @param integer tipo
	 * @param integer sequencial
	 * @param integer ordem
	 * @param bool ativo
	 *
	 * @return object
	 */
	function clsPmidrhTipoPortariaCamposEspeciais( $ref_cod_tipo_portaria = null, $cod_campo = null, $tipo = null, $sequencial = null, $ordem = null, $ativo = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmidrh.";
		$this->_tabela = "{$this->_schema}tipo_portaria_campos_especiais";

		$this->_campos_lista = $this->_todos_campos = "ref_cod_tipo_portaria, cod_campo, tipo, sequencial, ordem, ativo";

		if( is_numeric( $ref_cod_tipo_portaria ) )
		{
			if( class_exists( "clsPmidrhTipoPortaria" ) )
			{
				$tmp_obj = new clsPmidrhTipoPortaria( $ref_cod_tipo_portaria );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_tipo_portaria = $ref_cod_tipo_portaria;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_tipo_portaria = $ref_cod_tipo_portaria;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmidrh.tipo_portaria WHERE cod_tipo_portaria = '{$ref_cod_tipo_portaria}'" ) )
				{
					$this->ref_cod_tipo_portaria = $ref_cod_tipo_portaria;
				}
			}
		}


		if( is_numeric( $cod_campo ) )
		{
			$this->cod_campo = $cod_campo;
		}
		if( is_numeric( $tipo ) )
		{
			$this->tipo = $tipo;
		}
		if( is_numeric( $sequencial ) )
		{
			$this->sequencial = $sequencial;
		}
		if( is_numeric( $ordem ) )
		{
			$this->ordem = $ordem;
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
		if( is_numeric( $this->ref_cod_tipo_portaria ) && is_numeric( $this->cod_campo ) && is_numeric( $this->tipo ) && is_numeric( $this->sequencial ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_tipo_portaria ) )
			{
				$campos .= "{$gruda}ref_cod_tipo_portaria";
				$valores .= "{$gruda}'{$this->ref_cod_tipo_portaria}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->cod_campo ) )
			{
				$campos .= "{$gruda}cod_campo";
				$valores .= "{$gruda}'{$this->cod_campo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->tipo ) )
			{
				$campos .= "{$gruda}tipo";
				$valores .= "{$gruda}'{$this->tipo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->sequencial ) )
			{
				$campos .= "{$gruda}sequencial";
				$valores .= "{$gruda}'{$this->sequencial}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ordem ) )
			{
				$campos .= "{$gruda}ordem";
				$valores .= "{$gruda}'{$this->ordem}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";


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
		if( is_numeric( $this->ref_cod_tipo_portaria ) && is_numeric( $this->cod_campo ) && is_numeric( $this->tipo ) && is_numeric( $this->sequencial ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ordem ) )
			{
				$set .= "{$gruda}ordem = '{$this->ordem}'";
				$gruda = ", ";
			}
			if( ! is_null( $this->ativo ) )
			{
				$val = dbBool( $this->ativo ) ? "TRUE": "FALSE";
				$set .= "{$gruda}ativo = {$val}";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_cod_tipo_portaria = '{$this->ref_cod_tipo_portaria}' AND cod_campo = '{$this->cod_campo}' AND tipo = '{$this->tipo}' AND sequencial = '{$this->sequencial}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @param integer int_ordem
	 * @param bool bool_ativo
	 *
	 * @return array
	 */
	function lista( $int_ref_cod_tipo_portaria = null, $int_cod_campo = null, $int_tipo = null, $int_ordem = null, $bool_ativo = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_ref_cod_tipo_portaria ) )
		{
			$filtros .= "{$whereAnd} ref_cod_tipo_portaria = '{$int_ref_cod_tipo_portaria}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_cod_campo ) )
		{
			$filtros .= "{$whereAnd} cod_campo = '{$int_cod_campo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_tipo ) )
		{
			$filtros .= "{$whereAnd} tipo = '{$int_tipo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_sequencial ) )
		{
			$filtros .= "{$whereAnd} sequencial = '{$int_sequencial}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ordem ) )
		{
			$filtros .= "{$whereAnd} ordem = '{$int_ordem}'";
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
		if( is_numeric( $this->ref_cod_tipo_portaria ) && is_numeric( $this->cod_campo ) && is_numeric( $this->tipo ) && is_numeric( $this->sequencial ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_tipo_portaria = '{$this->ref_cod_tipo_portaria}' AND cod_campo = '{$this->cod_campo}' AND tipo = '{$this->tipo}' AND sequencial = '{$this->sequencial}'" );
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
		if( is_numeric( $this->ref_cod_tipo_portaria ) && is_numeric( $this->cod_campo ) && is_numeric( $this->tipo ) && is_numeric( $this->sequencial ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_cod_tipo_portaria = '{$this->ref_cod_tipo_portaria}' AND cod_campo = '{$this->cod_campo}' AND tipo = '{$this->tipo}' AND sequencial = '{$this->sequencial}'" );
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
		if( is_numeric( $this->ref_cod_tipo_portaria ) && is_numeric( $this->cod_campo ) && is_numeric( $this->tipo ) && is_numeric( $this->sequencial ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_tipo_portaria = '{$this->ref_cod_tipo_portaria}' AND cod_campo = '{$this->cod_campo}' AND tipo = '{$this->tipo}' AND sequencial = '{$this->sequencial}'" );
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

	function getNextSeq()
	{
		if( is_numeric($this->ref_cod_tipo_portaria) )
		{
			$db = new clsBanco();
			return $db->UnicoCampo("SELECT MAX(sequencial)+1 FROM {$this->_tabela} WHERE ref_cod_tipo_portaria = {$this->ref_cod_tipo_portaria}");
		}
		return "";
	}

	function desativarTodos()
	{
		if( is_numeric($this->ref_cod_tipo_portaria) )
		{
			$db = new clsBanco();
			$db->Consulta("UPDATE {$this->_tabela} SET ativo = FALSE WHERE ref_cod_tipo_portaria = {$this->ref_cod_tipo_portaria}");
			return true;
		}
		return false;
	}

}
?>