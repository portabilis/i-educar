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
* Criado em 06/12/2006 17:46 pelo gerador automatico de classes
*/

require_once( "include/pmidrh/geral.inc.php" );

class clsPmidrhPortariaAssinatura
{
	var $cod_portaria_assinatura;
	var $ref_cod_portaria;
	var $nm_responsavel;
	var $cargo_responsavel;

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
	 * @param integer cod_portaria_assinatura
	 * @param integer ref_cod_portaria
	 * @param string nm_responsavel
	 * @param string cargo_responsavel
	 *
	 * @return object
	 */
	function clsPmidrhPortariaAssinatura( $cod_portaria_assinatura = null, $ref_cod_portaria = null, $nm_responsavel = null, $cargo_responsavel = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmidrh.";
		$this->_tabela = "{$this->_schema}portaria_assinatura";

		$this->_campos_lista = $this->_todos_campos = "cod_portaria_assinatura, ref_cod_portaria, nm_responsavel, cargo_responsavel";



		if( is_numeric( $cod_portaria_assinatura ) )
		{
			$this->cod_portaria_assinatura = $cod_portaria_assinatura;
		}
		if( is_numeric( $ref_cod_portaria ) )
		{
			$this->ref_cod_portaria = $ref_cod_portaria;
		}
		if( is_string( $nm_responsavel ) )
		{
			$this->nm_responsavel = $nm_responsavel;
		}
		if( is_string( $cargo_responsavel ) )
		{
			$this->cargo_responsavel = $cargo_responsavel;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_portaria ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_portaria ) )
			{
				$campos .= "{$gruda}ref_cod_portaria";
				$valores .= "{$gruda}'{$this->ref_cod_portaria}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_responsavel ) )
			{
				$campos .= "{$gruda}nm_responsavel";
				$valores .= "{$gruda}'{$this->nm_responsavel}'";
				$gruda = ", ";
			}
			if( is_string( $this->cargo_responsavel ) )
			{
				$campos .= "{$gruda}cargo_responsavel";
				$valores .= "{$gruda}'{$this->cargo_responsavel}'";
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_portaria_assinatura_seq");
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
		if( is_numeric( $this->cod_portaria_assinatura ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_cod_portaria ) )
			{
				$set .= "{$gruda}ref_cod_portaria = '{$this->ref_cod_portaria}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_responsavel ) )
			{
				$set .= "{$gruda}nm_responsavel = '{$this->nm_responsavel}'";
				$gruda = ", ";
			}
			if( is_string( $this->cargo_responsavel ) )
			{
				$set .= "{$gruda}cargo_responsavel = '{$this->cargo_responsavel}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_portaria_assinatura = '{$this->cod_portaria_assinatura}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 * 
	 * @param integer int_ref_cod_portaria
	 * @param string str_nm_responsavel
	 * @param string str_cargo_responsavel
	 *
	 * @return array
	 */
	function lista( $int_ref_cod_portaria = null, $str_nm_responsavel = null, $str_cargo_responsavel = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_portaria_assinatura ) )
		{
			$filtros .= "{$whereAnd} cod_portaria_assinatura = '{$int_cod_portaria_assinatura}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_portaria ) )
		{
			$filtros .= "{$whereAnd} ref_cod_portaria = '{$int_ref_cod_portaria}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_responsavel ) )
		{
			$filtros .= "{$whereAnd} nm_responsavel LIKE '%{$str_nm_responsavel}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_cargo_responsavel ) )
		{
			$filtros .= "{$whereAnd} cargo_responsavel LIKE '%{$str_cargo_responsavel}%'";
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
		if( is_numeric( $this->cod_portaria_assinatura ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_portaria_assinatura = '{$this->cod_portaria_assinatura}'" );
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
		if( is_numeric( $this->cod_portaria_assinatura ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_portaria_assinatura = '{$this->cod_portaria_assinatura}'" );
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
		if( is_numeric( $this->ref_cod_portaria ) )
		{

		
		//	delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_portaria = '{$this->ref_cod_portaria}'" );
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