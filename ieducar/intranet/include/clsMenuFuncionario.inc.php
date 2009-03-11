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
require_once ("clsBanco.inc.php");

class clsMenuFuncionario
{
	var $ref_ref_cod_pessoa_fj=false;
	var $cadastra=false;
	var $exclui=false;
	var $ref_cod_menu_submenu=false;

	/**
	 * Construtor
	 *
	 * @return Object
	 */
	function clsMenuFuncionario($int_ref_ref_cod_pessoa_fj = false, $cadastra = false, $exclui = false, $int_ref_cod_menu_submenu=false)
	{
		$obj = new clsPessoaFj($int_ref_ref_cod_pessoa_fj);
		if($obj->detalhe())
		{
			$this->ref_ref_cod_pessoa_fj= $int_ref_ref_cod_pessoa_fj;
		}
		$this->cadastra= $cadastra;
		$this->exclui= $exclui;
		$this->ref_cod_menu_submenu= $int_ref_cod_menu_submenu;
		$this->tabela = "menu_funcionario";
	}

	/**
	 * Função que cadastra um novo registro com os valores atuais
	 *
	 * @return bool
	 */
	function cadastra()
	{
		$db = new clsBanco();

		// verificações de campos obrigatorios para inserção
		if( $this->ref_ref_cod_pessoa_fj && $this->ref_cod_menu_submenu)
		{
			$campos = "";
			$valores ="";

			if(is_numeric( $this->cadastra) )
			{
				$campos .= ", cadastra";
				$valores .= ", '$this->cadastra'";
			}
			if(is_numeric( $this->exclui) )
			{
				$campos .= ", exclui";
				$valores .= ", '$this->exclui'";
			}
			$db->Consulta("INSERT INTO {$this->tabela} ( ref_ref_cod_pessoa_fj, ref_cod_menu_submenu $campos) VALUES ( '$this->ref_ref_cod_pessoa_fj', '$this->ref_cod_menu_submenu'  $valores)");
					
			return true;
		}
		return false;
	}

	/**
	 * Edita o registro atual
	 *
	 * @return bool
	 */
	function edita()
	{

		// verifica campos obrigatorios para edicao
		if( is_numeric( $this->ref_cod_menu_submenu) && $this->ref_ref_cod_pessoa_fj)
		{
			$set = "";
			$gruda = " ";
			if( is_numeric( $this->cadastra) )
			{
				$set .= "{$gruda}cadastra = '{$this->cadastra}'";
				$gruda = ", ";

			}
			if( is_numeric( $this->exclui) )
			{
				$set .= "{$gruda}exclui = '{$this->exclui}'";
				$gruda = ", ";

			}

			if( $set != "" )
			{
				$db = new clsBanco();
				$db->Consulta( "UPDATE {$this->tabela} SET $set WHERE ref_cod_menu_submenu = '{$this->ref_cod_menu_submenu}' AND ref_ref_cod_pessoa_fj = '{$this->ref_ref_cod_pessoa_fj}' ");
				return true;
			}
		}
		return false;
	}

	/**
	 * Remove o registro atual
	 *
	 * @return bool
	 */
	function exclui()
	{
		if($this->ref_ref_cod_pessoa_fj &&  $this->ref_cod_menu_submenu)
		{
			$db = new clsBanco();
			$db->Consulta("DELETE FROM $this->tabela  WHERE ref_ref_cod_pessoa_fj ='$this->ref_ref_cod_pessoa_fj' AND ref_cod_menu_submenu = '$this->ref_cod_menu_submenu' ");
			return true;
		}
		return false;
	}

	function exclui_todos($int_cod_menu_menu=false)
	{
		if($this->ref_ref_cod_pessoa_fj)
		{
			if(is_numeric($int_cod_menu_menu))
			{
				$db = new clsBanco();
				$db->Consulta("delete from menu_funcionario where ref_ref_cod_pessoa_fj = '{$this->ref_ref_cod_pessoa_fj}' and ref_cod_menu_submenu in (select cod_menu_submenu from  menu_submenu where ref_cod_menu_menu in (select cod_menu_menu from menu_menu where cod_menu_menu = '{$int_cod_menu_menu}' or ref_cod_menu_pai ='{$int_cod_menu_menu}')) ");
				return true;
			}
		}
		return false;
	}

	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $int_ref_ref_cod_pessoa_fj = false, $int_ref_cod_menu_submenu = false, $str_ordenacao = false, $int_limite_ini =false, $int_limite_qtd =false, $int_ref_cod_menu_menu = false  )
	{
		// verificacoes de filtros a serem usados
		$where = "";
		$and = "";
		if( is_numeric( $int_ref_ref_cod_pessoa_fj) )
		{
			$where .= " $and ref_ref_cod_pessoa_fj = '$int_ref_ref_cod_pessoa_fj'";
			$and = " AND ";
		}
		if( is_numeric( $int_ref_cod_menu_submenu) )
		{
			$where .= " $and ref_cod_menu_submenu  = '$int_ref_cod_menu_submenu'";
			$and = " AND ";
		}
		if( is_numeric( $int_ref_cod_menu_menu) )
		{
			$where .= " $and ref_cod_menu_submenu  = ms.cod_menu_submenu AND ref_cod_menu_menu = '$int_ref_cod_menu_menu'";
			$tabela = ", menu_submenu ms";
			$and = " AND ";
		}

		$ordernacao = "";
		if( is_string( $str_ordenacao))
		{
			$ordernacao = " $str_ordenacao";
		}
		if($where)
		{
			$where = " WHERE $where";
		}
		if($int_limite_ini !== false && $int_limite_qtd !== false)
		{
			$limit = " LIMIT $int_limite_ini,$int_limite_qtd";
		}

		$db = new clsBanco();
		$total = $db->UnicoCampo( "SELECT COUNT(0) AS total FROM {$this->tabela} " );

		$db->Consulta( "SELECT ref_ref_cod_pessoa_fj, ref_cod_menu_submenu, cadastra, exclui FROM {$this->tabela} $tabela $where $ordernacao $limit" );
		$resultado = array();
		while ( $db->ProximoRegistro() )
		{
			$tupla = $db->Tupla();
			$tupla["total"] = $total;
			$tupla[4] = &$tupla["total"];

			$resultado[] = $tupla;
		}
		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
	}

	/**
	 * Retorna um array com os detalhes do objeto
	 *
	 * @return Array
	 */
	function detalhe()
	{

		if( is_numeric($this->ref_ref_cod_pessoa_fj) && is_numeric($this->ref_cod_menu_submenu)  )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT ref_ref_cod_pessoa_fj, ref_cod_menu_submenu, cadastra, exclui FROM {$this->tabela} WHERE ref_ref_cod_pessoa_fj = '{$this->ref_ref_cod_pessoa_fj}' AND ref_cod_menu_submenu = '$this->ref_cod_menu_submenu' " );
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				return $tupla;
			}
		}
		return false;
	}

	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista_menus( $int_ref_cod_menu_menu = false, $str_ordenacao = false, $int_limite_ini =false, $int_limite_qtd =false)
	{
		// verificacoes de filtros a serem usados
		$where = "";
		$and = "";
		if( is_numeric( $int_ref_cod_menu_menu) )
		{
			$where .= " ref_cod_menu_menu = '$int_ref_cod_menu_menu'";
			$and = " AND ";
		}

		$ordernacao = "";
		if( is_string( $str_ordenacao))
		{
			$ordernacao = " $str_ordenacao";
		}
		if($where)
		{
			$where = " WHERE $where";
		}
		if($int_limite_ini !== false && $int_limite_qtd !== false)
		{
			$limit = " LIMIT $int_limite_ini,$int_limite_qtd";
		}

		$db = new clsBanco();
		$total = $db->UnicoCampo( "SELECT COUNT(0) AS total FROM menu_submenu $where" );

		$db->Consulta( "SELECT cod_menu_submenu, nm_submenu FROM menu_submenu $where $ordernacao $limit" );
		$resultado = array();
		while ( $db->ProximoRegistro() )
		{
			$tupla = $db->Tupla();
			$tupla["total"] = $total;
			$tupla[4] = &$tupla["total"];

			$resultado[] = $tupla;
		}
		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
	}


	function detalhe_submenu($cod_submenu)
	{
		$db = new clsBanco();

		return $db->UnicoCampo( "SELECT nm_submenu FROM menu_submenu WHERE cod_menu_submenu = '$cod_submenu'" );

	}
}
?>
