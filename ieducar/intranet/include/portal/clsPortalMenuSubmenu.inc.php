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

require_once( "include/portal/geral.inc.php" );

class clsPortalMenuSubmenu
{
	var $cod_menu_submenu;
	var $ref_cod_menu_menu;
	var $cod_sistema;
	var $nm_submenu;
	var $arquivo;
	var $title;

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
	 * @param integer cod_menu_submenu
	 * @param integer ref_cod_menu_menu
	 * @param integer cod_sistema
	 * @param string nm_submenu
	 * @param string arquivo
	 * @param string title
	 *
	 * @return object
	 */
	function clsPortalMenuSubmenu( $cod_menu_submenu = null, $ref_cod_menu_menu = null, $cod_sistema = null, $nm_submenu = null, $arquivo = null, $title = null)
	{
		$db = new clsBanco();
		$this->_schema = "portal.";
		$this->_tabela = "{$this->_schema}menu_submenu";

		$this->_campos_lista = $this->_todos_campos = "cod_menu_submenu, ref_cod_menu_menu, cod_sistema, nm_submenu, arquivo, title";

		if( is_numeric( $ref_cod_menu_menu ) )
		{
			if( class_exists( "clsMenuMenu" ) )
			{
				$tmp_obj = new clsMenuMenu( $ref_cod_menu_menu );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_menu_menu = $ref_cod_menu_menu;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_menu_menu = $ref_cod_menu_menu;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM menu_menu WHERE cod_menu_menu = '{$ref_cod_menu_menu}'" ) )
				{
					$this->ref_cod_menu_menu = $ref_cod_menu_menu;
				}
			}
		}


		if( is_numeric( $cod_menu_submenu ) )
		{
			$this->cod_menu_submenu = $cod_menu_submenu;
		}
		if( is_numeric( $cod_sistema ) )
		{
			$this->cod_sistema = $cod_sistema;
		}
		if( is_string( $nm_submenu ) )
		{
			$this->nm_submenu = $nm_submenu;
		}
		if( is_string( $arquivo ) )
		{
			$this->arquivo = $arquivo;
		}
		if( is_string( $title ) )
		{
			$this->title = $title;
		}
		

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_string( $this->nm_submenu ) && is_string( $this->arquivo )  )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_menu_menu ) )
			{
				$campos .= "{$gruda}ref_cod_menu_menu";
				$valores .= "{$gruda}'{$this->ref_cod_menu_menu}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->cod_sistema ) )
			{
				$campos .= "{$gruda}cod_sistema";
				$valores .= "{$gruda}'{$this->cod_sistema}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_submenu ) )
			{
				$campos .= "{$gruda}nm_submenu";
				$valores .= "{$gruda}'{$this->nm_submenu}'";
				$gruda = ", ";
			}
			if( is_string( $this->arquivo ) )
			{
				$campos .= "{$gruda}arquivo";
				$valores .= "{$gruda}'{$this->arquivo}'";
				$gruda = ", ";
			}
			if( is_string( $this->title ) )
			{
				$campos .= "{$gruda}title";
				$valores .= "{$gruda}'{$this->title}'";
				$gruda = ", ";
			}
			

			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_menu_submenu_seq");
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
		if( is_numeric( $this->cod_menu_submenu ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_cod_menu_menu ) )
			{
				$set .= "{$gruda}ref_cod_menu_menu = '{$this->ref_cod_menu_menu}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->cod_sistema ) )
			{
				$set .= "{$gruda}cod_sistema = '{$this->cod_sistema}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_submenu ) )
			{
				$set .= "{$gruda}nm_submenu = '{$this->nm_submenu}'";
				$gruda = ", ";
			}
			if( is_string( $this->arquivo ) )
			{
				$set .= "{$gruda}arquivo = '{$this->arquivo}'";
				$gruda = ", ";
			}
			if( is_string( $this->title ) )
			{
				$set .= "{$gruda}title = '{$this->title}'";
				$gruda = ", ";
			}
			

			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_menu_submenu = '{$this->cod_menu_submenu}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @param integer int_ref_cod_menu_menu
	 * @param integer int_cod_sistema
	 * @param string str_nm_submenu
	 * @param string str_arquivo
	 * @param string str_title
	 *
	 * @return array
	 */
	function lista( $int_ref_cod_menu_menu = null, $int_cod_sistema = null, $str_nm_submenu = null, $str_arquivo = null, $str_title = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_menu_submenu ) )
		{
			$filtros .= "{$whereAnd} cod_menu_submenu = '{$int_cod_menu_submenu}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_menu_menu ) )
		{
			$filtros .= "{$whereAnd} ref_cod_menu_menu = '{$int_ref_cod_menu_menu}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_cod_sistema ) )
		{
			$filtros .= "{$whereAnd} cod_sistema = '{$int_cod_sistema}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_submenu ) )
		{
			$filtros .= "{$whereAnd} nm_submenu LIKE '%{$str_nm_submenu}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_arquivo ) )
		{
			$filtros .= "{$whereAnd} arquivo LIKE '%{$str_arquivo}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_title ) )
		{
			$filtros .= "{$whereAnd} title LIKE '%{$str_title}%'";
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
		if( is_numeric( $this->cod_menu_submenu ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_menu_submenu = '{$this->cod_menu_submenu}'" );
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
		if( is_numeric( $this->cod_menu_submenu ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_menu_submenu = '{$this->cod_menu_submenu}'" );
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
		if( is_numeric( $this->cod_menu_submenu ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_menu_submenu = '{$this->cod_menu_submenu}'" );
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