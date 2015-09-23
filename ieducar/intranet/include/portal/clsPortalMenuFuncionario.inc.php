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
* Criado em 22/12/2006 16:57 pelo gerador automatico de classes
*/


class clsPortalMenuFuncionario
{
	var $ref_ref_cod_pessoa_fj;
	var $cadastra;
	var $exclui;
	var $ref_cod_menu_submenu;

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
	 * @param integer ref_ref_cod_pessoa_fj
	 * @param integer cadastra
	 * @param integer exclui
	 * @param integer ref_cod_menu_submenu
	 *
	 * @return object
	 */
	function clsPortalMenuFuncionario( $ref_ref_cod_pessoa_fj = null, $cadastra = null, $exclui = null, $ref_cod_menu_submenu = null )
	{
		$db = new clsBanco();
		$this->_schema = "portal.";
		$this->_tabela = "{$this->_schema}menu_funcionario";

		$this->_campos_lista = $this->_todos_campos = "ref_ref_cod_pessoa_fj, cadastra, exclui, ref_cod_menu_submenu";

		if( is_numeric( $ref_ref_cod_pessoa_fj ) )
		{
			if( class_exists( "clsFuncionario" ) )
			{
				$tmp_obj = new clsFuncionario( $ref_ref_cod_pessoa_fj );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_ref_cod_pessoa_fj = $ref_ref_cod_pessoa_fj;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_ref_cod_pessoa_fj = $ref_ref_cod_pessoa_fj;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM funcionario WHERE ref_cod_pessoa_fj = '{$ref_ref_cod_pessoa_fj}'" ) )
				{
					$this->ref_ref_cod_pessoa_fj = $ref_ref_cod_pessoa_fj;
				}
			}
		}
		if( is_numeric( $ref_cod_menu_submenu ) )
		{
			if( class_exists( "clsMenuSubmenu" ) )
			{
				$tmp_obj = new clsMenuSubmenu( $ref_cod_menu_submenu );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_menu_submenu = $ref_cod_menu_submenu;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_menu_submenu = $ref_cod_menu_submenu;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM menu_submenu WHERE cod_menu_submenu = '{$ref_cod_menu_submenu}'" ) )
				{
					$this->ref_cod_menu_submenu = $ref_cod_menu_submenu;
				}
			}
		}


		if( is_numeric( $cadastra ) )
		{
			$this->cadastra = $cadastra;
		}
		if( is_numeric( $exclui ) )
		{
			$this->exclui = $exclui;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_ref_cod_pessoa_fj ) && is_numeric( $this->cadastra ) && is_numeric( $this->exclui ) && is_numeric( $this->ref_cod_menu_submenu ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_ref_cod_pessoa_fj ) )
			{
				$campos .= "{$gruda}ref_ref_cod_pessoa_fj";
				$valores .= "{$gruda}'{$this->ref_ref_cod_pessoa_fj}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->cadastra ) )
			{
				$campos .= "{$gruda}cadastra";
				$valores .= "{$gruda}'{$this->cadastra}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->exclui ) )
			{
				$campos .= "{$gruda}exclui";
				$valores .= "{$gruda}'{$this->exclui}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_menu_submenu ) )
			{
				$campos .= "{$gruda}ref_cod_menu_submenu";
				$valores .= "{$gruda}'{$this->ref_cod_menu_submenu}'";
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
		if( is_numeric( $this->ref_ref_cod_pessoa_fj ) && is_numeric( $this->ref_cod_menu_submenu ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->cadastra ) )
			{
				$set .= "{$gruda}cadastra = '{$this->cadastra}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->exclui ) )
			{
				$set .= "{$gruda}exclui = '{$this->exclui}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_ref_cod_pessoa_fj = '{$this->ref_ref_cod_pessoa_fj}' AND ref_cod_menu_submenu = '{$this->ref_cod_menu_submenu}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @param integer int_cadastra
	 * @param integer int_exclui
	 *
	 * @return array
	 */
	function lista( $int_cadastra = null, $int_exclui = null, $int_ref_ref_cod_pessoa_fj = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_ref_ref_cod_pessoa_fj ) )
		{
			$filtros .= "{$whereAnd} ref_ref_cod_pessoa_fj = '{$int_ref_ref_cod_pessoa_fj}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_cadastra ) )
		{
			$filtros .= "{$whereAnd} cadastra = '{$int_cadastra}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_exclui ) )
		{
			$filtros .= "{$whereAnd} exclui = '{$int_exclui}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_menu_submenu ) )
		{
			$filtros .= "{$whereAnd} ref_cod_menu_submenu = '{$int_ref_cod_menu_submenu}'";
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
	 * Retorna uma lista dos submenus do usuario
	 *
	 *
	 * @return array
	 */
	function listaSubmenu( $int_ref_ref_cod_pessoa_fj = null, $int_ref_cod_menu_menu = null, $str_order_by = null )
	{
		if( is_numeric($int_ref_ref_cod_pessoa_fj) && is_numeric($int_ref_cod_menu_menu) ) 
		{
			$sql = "SELECT
						*
					FROM
						menu_funcionario
						, menu_submenu
					WHERE
						ref_ref_cod_pessoa_fj = '{$int_ref_ref_cod_pessoa_fj}'
						AND ref_cod_menu_menu = '{$int_ref_cod_menu_menu}'
						AND cod_menu_submenu = ref_cod_menu_submenu
					ORDER BY
						{$str_order_by}";

			$db = new clsBanco();
			$db->Consulta( $sql );
			while ( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
//				$resultado[] = $tupla[$this->_campos_lista];
				$resultado[] = $tupla;
			}
			if( count( $resultado ) )
			{
				return $resultado;
			}
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
		if( is_numeric( $this->ref_ref_cod_pessoa_fj ) && is_numeric( $this->ref_cod_menu_submenu ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_ref_cod_pessoa_fj = '{$this->ref_ref_cod_pessoa_fj}' AND ref_cod_menu_submenu = '{$this->ref_cod_menu_submenu}'" );
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
		if( is_numeric( $this->ref_ref_cod_pessoa_fj ) && is_numeric( $this->ref_cod_menu_submenu ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_ref_cod_pessoa_fj = '{$this->ref_ref_cod_pessoa_fj}' AND ref_cod_menu_submenu = '{$this->ref_cod_menu_submenu}'" );
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
		if( is_numeric( $this->ref_ref_cod_pessoa_fj ) )
		{


			//delete
			$db = new clsBanco();
			$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_ref_cod_pessoa_fj = '{$this->ref_ref_cod_pessoa_fj}' " );
			return true;

		}
		return false;
	}

	/**
	 * Exclui Todos
	 *
	 * @return bool
	 */
	function excluirTodos( $cod_menu )
	{
		if( is_numeric( $this->ref_ref_cod_pessoa_fj ) && is_numeric($cod_menu) )
		{
			//delete
			$db = new clsBanco();
			$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_ref_cod_pessoa_fj = '{$this->ref_ref_cod_pessoa_fj}' AND ref_cod_menu_submenu IN ( SELECT cod_menu_submenu FROM menu_submenu WHERE ref_cod_menu_menu = '{$cod_menu}')" );
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