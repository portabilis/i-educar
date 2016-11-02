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
/*
* @author Prefeitura Municipal de Itajaí
*
* Criado em 21/07/2006 09:01 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarClienteTipoExemplarTipo
{
	var $ref_cod_cliente_tipo;
	var $ref_cod_exemplar_tipo;
	var $dias_emprestimo;

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
	function clsPmieducarClienteTipoExemplarTipo( $ref_cod_cliente_tipo = null, $ref_cod_exemplar_tipo = null, $dias_emprestimo = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}cliente_tipo_exemplar_tipo";

		$this->_campos_lista = $this->_todos_campos = "ctet.ref_cod_cliente_tipo, ctet.ref_cod_exemplar_tipo, ctet.dias_emprestimo";

		if( is_numeric( $ref_cod_exemplar_tipo ) )
		{
			if( class_exists( "clsPmieducarExemplarTipo" ) )
			{
				$tmp_obj = new clsPmieducarExemplarTipo( $ref_cod_exemplar_tipo );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_exemplar_tipo = $ref_cod_exemplar_tipo;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_exemplar_tipo = $ref_cod_exemplar_tipo;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.exemplar_tipo WHERE cod_exemplar_tipo = '{$ref_cod_exemplar_tipo}'" ) )
				{
					$this->ref_cod_exemplar_tipo = $ref_cod_exemplar_tipo;
				}
			}
		}
		if( is_numeric( $ref_cod_cliente_tipo ) )
		{
			if( class_exists( "clsPmieducarClienteTipo" ) )
			{
				$tmp_obj = new clsPmieducarClienteTipo( $ref_cod_cliente_tipo );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_cliente_tipo = $ref_cod_cliente_tipo;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_cliente_tipo = $ref_cod_cliente_tipo;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.cliente_tipo WHERE cod_cliente_tipo = '{$ref_cod_cliente_tipo}'" ) )
				{
					$this->ref_cod_cliente_tipo = $ref_cod_cliente_tipo;
				}
			}
		}


		if( is_numeric( $dias_emprestimo ) )
		{
			$this->dias_emprestimo = $dias_emprestimo;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_cliente_tipo ) && is_numeric( $this->ref_cod_exemplar_tipo ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_cliente_tipo ) )
			{
				$campos .= "{$gruda}ref_cod_cliente_tipo";
				$valores .= "{$gruda}'{$this->ref_cod_cliente_tipo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_exemplar_tipo ) )
			{
				$campos .= "{$gruda}ref_cod_exemplar_tipo";
				$valores .= "{$gruda}'{$this->ref_cod_exemplar_tipo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->dias_emprestimo ) )
			{
				$campos .= "{$gruda}dias_emprestimo";
				$valores .= "{$gruda}'{$this->dias_emprestimo}'";
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
		if( is_numeric( $this->ref_cod_cliente_tipo ) && is_numeric( $this->ref_cod_exemplar_tipo ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->dias_emprestimo ) )
			{
				$set .= "{$gruda}dias_emprestimo = '{$this->dias_emprestimo}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_cod_cliente_tipo = '{$this->ref_cod_cliente_tipo}' AND ref_cod_exemplar_tipo = '{$this->ref_cod_exemplar_tipo}'" );
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
	function lista( $int_ref_cod_cliente_tipo = null, $int_ref_cod_exemplar_tipo = null, $int_dias_emprestimo = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} ctet, {$this->_schema}cliente_tipo ct, {$this->_schema}exemplar_tipo et";

		$whereAnd = " AND ";
		$filtros = " WHERE ctet.ref_cod_cliente_tipo = ct.cod_cliente_tipo AND ctet.ref_cod_exemplar_tipo = et.cod_exemplar_tipo ";

		if( is_numeric( $int_ref_cod_cliente_tipo ) )
		{
			$filtros .= "{$whereAnd} ctet.ref_cod_cliente_tipo = '{$int_ref_cod_cliente_tipo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_exemplar_tipo ) )
		{
			$filtros .= "{$whereAnd} ctet.ref_cod_exemplar_tipo = '{$int_ref_cod_exemplar_tipo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_dias_emprestimo ) )
		{
			$filtros .= "{$whereAnd} ctet.dias_emprestimo = '{$int_dias_emprestimo}'";
			$whereAnd = " AND ";
		}

		// FILTRO EXTRA
		$filtros .= "{$whereAnd} ct.ativo = '1'";
		$whereAnd = " AND ";

		$filtros .= "{$whereAnd} et.ativo = '1'";
		$whereAnd = " AND ";


		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} ctet, {$this->_schema}cliente_tipo ct, {$this->_schema}exemplar_tipo et {$filtros}" );

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
		if( is_numeric( $this->ref_cod_cliente_tipo ) && is_numeric( $this->ref_cod_exemplar_tipo ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} ctet WHERE ctet.ref_cod_cliente_tipo = '{$this->ref_cod_cliente_tipo}' AND ctet.ref_cod_exemplar_tipo = '{$this->ref_cod_exemplar_tipo}'" );
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
		if( is_numeric( $this->ref_cod_cliente_tipo ) && is_numeric( $this->ref_cod_exemplar_tipo ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_cod_cliente_tipo = '{$this->ref_cod_cliente_tipo}' AND ref_cod_exemplar_tipo = '{$this->ref_cod_exemplar_tipo}'" );
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
		if( is_numeric( $this->ref_cod_cliente_tipo ) && is_numeric( $this->ref_cod_exemplar_tipo ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_cliente_tipo = '{$this->ref_cod_cliente_tipo}' AND ref_cod_exemplar_tipo = '{$this->ref_cod_exemplar_tipo}'" );
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