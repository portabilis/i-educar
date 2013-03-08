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
* Criado em 12/02/2007 15:36 pelo gerador automatico de classes
*/

require_once( "include/public/geral.inc.php" );

class clsPublicUf
{
	var $sigla_uf;
	var $nome;
	var $geom;
	var $idpais;

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
	 * @param string sigla_uf
	 * @param string nome
	 * @param string geom
	 * @param integer idpais
	 *
	 * @return object
	 */
	function clsPublicUf( $sigla_uf = null, $nome = null, $geom = null, $idpais = null )
	{
		$db = new clsBanco();
		$this->_schema = "public.";
		$this->_tabela = "{$this->_schema}uf";

		$this->_campos_lista = $this->_todos_campos = "uf.sigla_uf, uf.nome, uf.geom, uf.idpais";

		if( is_numeric( $idpais ) )
		{
			if( class_exists( "clsPais" ) )
			{
				$tmp_obj = new clsPais( $idpais );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->idpais = $idpais;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->idpais = $idpais;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pais WHERE idpais = '{$idpais}'" ) )
				{
					$this->idpais = $idpais;
				}
			}
		}


		if( is_string( $sigla_uf ) )
		{
			$this->sigla_uf = $sigla_uf;
		}
		if( is_string( $nome ) )
		{
			$this->nome = $nome;
		}
		if( is_string( $geom ) )
		{
			$this->geom = $geom;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_string( $this->sigla_uf ) && is_string( $this->nome ) && is_numeric( $this->idpais ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_string( $this->sigla_uf ) )
			{
				$campos .= "{$gruda}sigla_uf";
				$valores .= "{$gruda}'{$this->sigla_uf}'";
				$gruda = ", ";
			}
			if( is_string( $this->nome ) )
			{
				$campos .= "{$gruda}nome";
				$valores .= "{$gruda}'" . addslashes($this->nome) . "'";
				$gruda = ", ";
			}
			if( is_string( $this->geom ) )
			{
				$campos .= "{$gruda}geom";
				$valores .= "{$gruda}'{$this->geom}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idpais ) )
			{
				$campos .= "{$gruda}idpais";
				$valores .= "{$gruda}'{$this->idpais}'";
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $this->sigla_uf;
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
		if( is_string( $this->sigla_uf ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_string( $this->nome ) )
			{
				$set .= "{$gruda}nome = '" . addslashes($this->nome) . "'";
				$gruda = ", ";
			}
			if( is_string( $this->geom ) )
			{
				$set .= "{$gruda}geom = '{$this->geom}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idpais ) )
			{
				$set .= "{$gruda}idpais = '{$this->idpais}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE sigla_uf = '{$this->sigla_uf}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @param string str_nome
	 * @param string str_geom
	 * @param integer int_idpais
	 *
	 * @return array
	 */
	function lista( $str_nome = null, $str_geom = null, $int_idpais = null, $str_sigla_uf = null )
	{
		$sql = "SELECT {$this->_campos_lista}, p.nome AS nm_pais FROM {$this->_tabela} uf, public.pais p ";
		$whereAnd = " AND ";

		$filtros = " WHERE uf.idpais = p.idpais";

		if( is_string( $str_sigla_uf ) )
		{
			$filtros .= "{$whereAnd} uf.sigla_uf LIKE '%{$str_sigla_uf}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nome ) )
		{
			$filtros .= "{$whereAnd} uf.nome LIKE E'%" . addslashes($str_nome) . "%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_geom ) )
		{
			$filtros .= "{$whereAnd} uf.geom LIKE '%{$str_geom}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idpais ) )
		{
			$filtros .= "{$whereAnd} uf.idpais = '{$int_idpais}'";
			$whereAnd = " AND ";
		}


		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} uf, public.pais p {$filtros}" );

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
		if( is_string( $this->sigla_uf ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} uf WHERE uf.sigla_uf = '{$this->sigla_uf}'" );
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
		if( is_string( $this->sigla_uf ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE sigla_uf = '{$this->sigla_uf}'" );
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
		if( is_string( $this->sigla_uf ) )
		{
//			delete
			$db = new clsBanco();
			$db->Consulta( "DELETE FROM {$this->_tabela} WHERE sigla_uf = '{$this->sigla_uf}'" );
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