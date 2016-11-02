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
* Criado em 25/07/2006 15:19 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarAcervoAcervoAutor
{
	var $ref_cod_acervo_autor;
	var $ref_cod_acervo;
	var $principal;

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
	function clsPmieducarAcervoAcervoAutor( $ref_cod_acervo_autor = null, $ref_cod_acervo = null, $principal = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}acervo_acervo_autor";

		$this->_campos_lista = $this->_todos_campos = "ref_cod_acervo_autor, ref_cod_acervo, principal";

		if( is_numeric( $ref_cod_acervo ) )
		{
			if( class_exists( "clsPmieducarAcervo" ) )
			{
				$tmp_obj = new clsPmieducarAcervo( $ref_cod_acervo );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_acervo = $ref_cod_acervo;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_acervo = $ref_cod_acervo;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.acervo WHERE cod_acervo = '{$ref_cod_acervo}'" ) )
				{
					$this->ref_cod_acervo = $ref_cod_acervo;
				}
			}
		}
		if( is_numeric( $ref_cod_acervo_autor ) )
		{
			if( class_exists( "clsPmieducarAcervoAutor" ) )
			{
				$tmp_obj = new clsPmieducarAcervoAutor( $ref_cod_acervo_autor );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_acervo_autor = $ref_cod_acervo_autor;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_acervo_autor = $ref_cod_acervo_autor;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.acervo_autor WHERE cod_acervo_autor = '{$ref_cod_acervo_autor}'" ) )
				{
					$this->ref_cod_acervo_autor = $ref_cod_acervo_autor;
				}
			}
		}


		if( is_numeric( $principal ) )
		{
			$this->principal = $principal;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_acervo_autor ) && is_numeric( $this->ref_cod_acervo ) && is_numeric( $this->principal ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_acervo_autor ) )
			{
				$campos .= "{$gruda}ref_cod_acervo_autor";
				$valores .= "{$gruda}'{$this->ref_cod_acervo_autor}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_acervo ) )
			{
				$campos .= "{$gruda}ref_cod_acervo";
				$valores .= "{$gruda}'{$this->ref_cod_acervo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->principal ) )
			{
				$campos .= "{$gruda}principal";
				$valores .= "{$gruda}'{$this->principal}'";
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
		if( is_numeric( $this->ref_cod_acervo_autor ) && is_numeric( $this->ref_cod_acervo ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->principal ) )
			{
				$set .= "{$gruda}principal = '{$this->principal}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_cod_acervo_autor = '{$this->ref_cod_acervo_autor}' AND ref_cod_acervo = '{$this->ref_cod_acervo}'" );
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
	function lista( $int_ref_cod_acervo_autor = null, $int_ref_cod_acervo = null, $int_principal = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_ref_cod_acervo_autor ) )
		{
			$filtros .= "{$whereAnd} ref_cod_acervo_autor = '{$int_ref_cod_acervo_autor}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_acervo ) )
		{
			$filtros .= "{$whereAnd} ref_cod_acervo = '{$int_ref_cod_acervo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_principal ) )
		{
			$filtros .= "{$whereAnd} principal = '{$int_principal}'";
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
		if( is_numeric( $this->ref_cod_acervo_autor ) && is_numeric( $this->ref_cod_acervo ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_acervo_autor = '{$this->ref_cod_acervo_autor}' AND ref_cod_acervo = '{$this->ref_cod_acervo}'" );
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
		if( is_numeric( $this->ref_cod_acervo_autor ) && is_numeric( $this->ref_cod_acervo ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_cod_acervo_autor = '{$this->ref_cod_acervo_autor}' AND ref_cod_acervo = '{$this->ref_cod_acervo}'" );
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
		if( is_numeric( $this->ref_cod_acervo_autor ) && is_numeric( $this->ref_cod_acervo ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_acervo_autor = '{$this->ref_cod_acervo_autor}' AND ref_cod_acervo = '{$this->ref_cod_acervo}'" );
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
		if ( is_numeric( $this->ref_cod_acervo ) ) {
			$db = new clsBanco();
			$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_acervo = '{$this->ref_cod_acervo}'" );
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