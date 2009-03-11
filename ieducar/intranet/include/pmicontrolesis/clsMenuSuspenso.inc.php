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
require_once ("include/clsBanco.inc.php");

class clsMenuSuspenso
{
	var $cod_menu;
	var $ref_cod_menu_submenu;
	var $ref_cod_menu_pai;
	var $tt_menu;
	var $ref_cod_ico;
	var $ord_menu;
	var $caminho;
	var $alvo;
	var $suprime_menu;
	var $ref_cod_tutormenu;

	// Variï¿½veis que definem a tabela e o schema em que a tabela se encontra
	var $tabela;
	var $schema;

	/**
	 * Construtor
	 *
	 * @return Object:clsGrupo
	 */
	function clsMenuSuspenso( $cod_menu = false, $ref_cod_menu_submenu = false, $ref_cod_menu_pai = false, $tt_menu = false, $ref_cod_ico = false, $ord_menu = false, $caminho = false, $alvo = false, $suprime_menu = false, $ref_cod_tutormenu = false )
	{
		$this->cod_menu 			= $cod_menu;
		$this->ref_cod_menu_submenu = $ref_cod_menu_submenu;
		$this->ref_cod_menu_pai 	= $ref_cod_menu_pai;
		$this->tt_menu 				= $tt_menu;
		$this->ref_cod_ico 			= $ref_cod_ico;
		$this->ord_menu 			= $ord_menu;
		$this->caminho 				= $caminho;
		$this->alvo 				= $alvo;
		$this->suprime_menu 		= $suprime_menu;
		$this->ref_cod_tutormenu 	= $ref_cod_tutormenu;

		// Difiniï¿½ï¿½ï¿½o da tabela
		$this->tabela = "menu";
		// Difiniï¿½ï¿½ï¿½o do schema
		$this->schema = "pmicontrolesis";
	}

	/**
	 * Funcao que cadastra um novo registro com os valores atuais
	 *
	 * @return bool
	 */
	function cadastra()
	{
		$db = new clsBanco();

		// verificacoes de campos obrigatorios para insercao
		if( is_string( $this->tt_menu ) && is_numeric( $this->ord_menu ))
		{
			$campos  = "";
			$valores = "";

			if($this->ref_cod_menu_submenu)
			{
				$campos  .= ", ref_cod_menu_submenu";
				$valores .= ", '$this->ref_cod_menu_submenu' ";
			}

			if($this->ref_cod_menu_pai)
			{
				$campos  .= ", ref_cod_menu_pai";
				$valores .= ", '$this->ref_cod_menu_pai' ";
			}

			if($this->ref_cod_ico)
			{
				$campos  .= ", ref_cod_ico";
				$valores .= ", '$this->ref_cod_ico' ";
			}

			if($this->caminho)
			{
				$campos  .= ", caminho";
				$valores .= ", '$this->caminho' ";
			}

			if($this->alvo)
			{
				$campos  .= ", alvo";
				$valores .= ", '$this->alvo' ";
			}

			if($this->suprime_menu || $this->suprime_menu == '0')
			{
				$campos  .= ", suprime_menu";
				$valores .= ", '$this->suprime_menu' ";
			}

			if($this->ref_cod_tutormenu)
			{
				$campos  .= ", ref_cod_tutormenu";
				$valores .= ", '$this->ref_cod_tutormenu' ";
			}

			$db->Consulta( "INSERT INTO {$this->schema}.{$this->tabela} (  tt_menu, ord_menu {$campos} ) VALUES (  '$this->tt_menu', '$this->ord_menu' {$valores} )" );
			return $db->InsertId("{$this->schema}.menu_cod_menu_seq");
		}
		return false;
	}

	/**
	 * Edita o registro atual
	 *
	 * @return bool
	 */
	function edita(  )
	{
		// verifica campos obrigatorios para edicao
		if( is_numeric( $this->cod_menu ) )
		{

			$where_set = "SET";

			if( is_numeric($this->ref_cod_menu_submenu) )
			{
				$set .=  " {$where_set} ref_cod_menu_submenu = '$this->ref_cod_menu_submenu' ";
				$where_set = ",";
			}

			if( is_numeric($this->ref_cod_menu_pai) )
			{
				$set .=  " {$where_set} ref_cod_menu_pai = '$this->ref_cod_menu_pai' ";
				$where_set = ",";
			}

			if( is_string($this->tt_menu) )
			{
				$set .=  " {$where_set} tt_menu = '$this->tt_menu' ";
				$where_set = ",";
			}

			if( is_string($this->ref_cod_ico) )
			{
				$set .=  " {$where_set} ref_cod_ico = '$this->ref_cod_ico' ";
				$where_set = ",";
			}

			if( is_numeric($this->ord_menu) )
			{
				$set .=  " {$where_set} ord_menu = '$this->ord_menu' ";
				$where_set = ",";
			}

			if( is_string($this->caminho) )
			{
				$set .=  " {$where_set} caminho = '$this->caminho' ";
				$where_set = ",";
			}

			if( is_string($this->alvo) )
			{
				$set .=  " {$where_set} alvo = '$this->alvo' ";
				$where_set = ",";
			}

			if( is_numeric($this->suprime_menu) || $this->suprime_menu == '0')
			{
				$set .=  " {$where_set} suprime_menu = '$this->suprime_menu' ";
				$where_set = ",";
			}

			if( is_numeric($this->ref_cod_tutormenu) )
			{
				$set .=  " {$where_set} ref_cod_tutormenu = '$this->ref_cod_tutormenu' ";
			}

			if($set)
			{
				$db = new clsBanco();
				$db->Consulta( "UPDATE {$this->schema}.{$this->tabela} $set WHERE cod_menu = '{$this->cod_menu}'" );
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
	function exclui( )
	{
		// verifica se existe um ID definido para delecao
		if( is_numeric( $this->ref_cod_tutormenu) )
		{

			$db = new clsBanco();
			$dba = new clsBanco();
			$db->Consulta("SELECT cod_menu, ref_cod_menu_pai
					  FROM pmicontrolesis.menu
				         WHERE ref_cod_menu_pai IN (SELECT cod_menu
								      FROM pmicontrolesis.menu
								     WHERE ref_cod_menu_pai IN ( SELECT cod_menu
												   FROM pmicontrolesis.menu
												  WHERE ref_cod_menu_pai IN ( SELECT cod_menu
															        FROM pmicontrolesis.menu
															       WHERE ref_cod_menu_pai IS NULL )
															     )
												)
								       AND ref_cod_tutormenu = '$this->ref_cod_tutormenu'
				UNION all
					SELECT cod_menu, ref_cod_menu_pai
					  FROM pmicontrolesis.menu
					 WHERE ref_cod_menu_pai IN ( SELECT cod_menu
								       FROM pmicontrolesis.menu
								      WHERE ref_cod_menu_pai IN ( SELECT cod_menu
												    FROM pmicontrolesis.menu
												   WHERE ref_cod_menu_pai IS NULL )
								   )
					   AND ref_cod_tutormenu = '$this->ref_cod_tutormenu'
				UNION all
					SELECT cod_menu, ref_cod_menu_pai
					  FROM pmicontrolesis.menu
					 WHERE ref_cod_menu_pai IN ( SELECT cod_menu
								       FROM pmicontrolesis.menu
								      WHERE ref_cod_menu_pai IS NULL )
					   AND ref_cod_tutormenu = '$this->ref_cod_tutormenu'
				union all
				SELECT cod_menu, ref_cod_menu_pai
					  FROM pmicontrolesis.menu
					 WHERE ref_cod_menu_pai IS NULL
					   AND ref_cod_tutormenu = '$this->ref_cod_tutormenu'");



	//		die("DELETE FROM {$this->schema}.{$this->tabela} WHERE ref_cod_tutormenu = {$this->ref_cod_tutormenu} ");
			while($db->ProximoRegistro())
			{

				list($cod_menu,$ref_cod_menu_pai) = $db->Tupla();
				if($ref_cod_menu_pai)
				{
						$ref_cod_menu_pai = "AND ref_cod_menu_pai={$ref_cod_menu_pai}";
				}

				$dba->Consulta( "DELETE FROM {$this->schema}.{$this->tabela} WHERE ref_cod_tutormenu = {$this->ref_cod_tutormenu} $ref_cod_menu_pai AND cod_menu={$cod_menu}" );
			}

			//$db->Consulta( "DELETE FROM {$this->schema}.{$this->tabela} WHERE ref_cod_tutormenu = {$this->ref_cod_tutormenu} " );
			return true;
		}
		return false;
	}

	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $int_ref_cod_menu_submenu = false, $int_ref_cod_menu_pai = false, $str_tt_menu = false, $str_ref_cod_ico = false, $int_ord_menu = false, $str_caminho = false, $str_alvo = false, $int_suprime_menu = false,  $int_ref_cod_tutormenu = false, $int_limite_ini = false, $int_limite_qtd = false, $str_ordenacao = false, $int_cod_menu = false )
	{
		// verificacoes de filtros a serem usados
		$whereAnd = "WHERE ";

		if( is_numeric( $int_ref_cod_menu_submenu ))
		{
			$where .= "{$whereAnd}ref_cod_menu_submenu = '$int_ref_cod_menu_submenu'";
			$whereAnd = " AND ";
		}

		if( is_numeric( $int_ref_cod_menu_pai ))
		{
			$where .= "{$whereAnd}ref_cod_menu_pai = '$int_ref_cod_menu_pai'";
			$whereAnd = " AND ";
		}

		if( is_string( $str_tt_menu ) )
		{
			$where .= "{$whereAnd}tt_menu =  '$str_tt_menu'";
			$whereAnd = " AND ";
		}

		if( is_string( $str_ref_cod_ico ) )
		{
			$where .= "{$whereAnd}ref_cod_ico >= '$str_ref_cod_ico'";
			$whereAnd = " AND ";
		}

		if( is_numeric( $int_ord_menu) )
		{
			$where .= "{$whereAnd}ord_menu >= '$int_ord_menu'";
			$whereAnd = " AND ";
		}

		if( is_string( $str_caminho ) )
		{
			$where .= "{$whereAnd}caminho >= '$str_caminho'";
			$whereAnd = " AND ";
		}

		if( is_string( $str_alvo ) )
		{
			$where .= "{$whereAnd}alvo <= '$str_alvo'";
			$whereAnd = " AND ";
		}

		if( is_numeric( $int_suprime_menu ))
		{
			$where .= "{$whereAnd}suprime_menu = '$int_suprime_menu'";
			$whereAnd = " AND ";
		}

		if( is_numeric( $int_ref_cod_tutormenu))
		{
			$where .= "{$whereAnd}ref_cod_tutormenu = '$int_ref_cod_tutormenu'";
			$whereAnd = " AND ";
		}

		if( is_numeric( $int_cod_menu))
		{
			$where .= "{$whereAnd}cod_menu = '$int_cod_menu'";
			$whereAnd = " AND ";
		}

		$orderBy = "";
		if( is_string( $str_ordenacao ) )
		{
			$orderBy = "ORDER BY $str_ordenacao";
		}

		$limit = "";
		if( is_numeric( $int_limite_ini ) && is_numeric( $int_limite_qtd ) )
		{
			$limit = " LIMIT $int_limite_qtd OFFSET $int_limite_ini";
		}

		$db = new clsBanco();
		$total = $db->UnicoCampo( "SELECT COUNT(0) AS total FROM {$this->schema}.{$this->tabela} $where " );
		//echo "SELECT cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu, ref_cod_ico, ord_menu, caminho, alvo, suprime_menu, ref_cod_tutormenu FROM {$this->schema}.{$this->tabela} $where $orderBy $limit";
		$db->Consulta( "SELECT cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu, ref_cod_ico, ord_menu, caminho, alvo, suprime_menu, ref_cod_tutormenu FROM {$this->schema}.{$this->tabela} $where $orderBy $limit" );
		$resultado = array();
		while ( $db->ProximoRegistro() )
		{
			$tupla = $db->Tupla();
			$tupla['total'] = $total;
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
		if ($this->cod_menu)
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu, ref_cod_ico, ord_menu, caminho, alvo, suprime_menu, ref_cod_tutormenu FROM {$this->schema}.{$this->tabela} WHERE cod_menu = '$this->cod_menu' " );
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				return $tupla;
			}
			return false;
		}
	}

	function listaNivel($ref_cod_tutormenu, $idpes)
	{
		$db =new clsBanco();

		if($db->UnicoCampo( "SELECT 1 FROM menu_funcionario WHERE ref_ref_cod_pessoa_fj = '$idpes' AND ref_cod_menu_submenu ='0'"))
		{

			$menu_pai = ",(select mm.ref_cod_menu_pai from portal.menu_submenu ms
				,portal.menu_menu mm where ms.ref_cod_menu_menu = mm.cod_menu_menu
				and ms.cod_menu_submenu = m.ref_cod_menu_submenu) as menu_menu_pai ";

			$sql = "SELECT cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu, ref_cod_ico, ord_menu, caminho, alvo, suprime_menu, ref_cod_tutormenu, 1 as nivel $menu_pai FROM pmicontrolesis.menu m  WHERE ref_cod_menu_pai IS NULL AND ref_cod_tutormenu = '$ref_cod_tutormenu'
					UNION
					SELECT cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu, ref_cod_ico, ord_menu, caminho, alvo, suprime_menu, ref_cod_tutormenu, 2 as nivel $menu_pai FROM pmicontrolesis.menu m WHERE ref_cod_menu_pai IN ( SELECT cod_menu FROM pmicontrolesis.menu WHERE ref_cod_menu_pai IS NULL )  AND ref_cod_tutormenu = '$ref_cod_tutormenu'
					UNION
					SELECT cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu, ref_cod_ico, ord_menu, caminho, alvo, suprime_menu, ref_cod_tutormenu, 3 as nivel $menu_pai FROM pmicontrolesis.menu m WHERE ref_cod_menu_pai IN ( SELECT cod_menu FROM pmicontrolesis.menu WHERE ref_cod_menu_pai IN ( SELECT cod_menu FROM pmicontrolesis.menu WHERE ref_cod_menu_pai IS NULL ) )  AND ref_cod_tutormenu = '$ref_cod_tutormenu'
					UNION
					SELECT cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu, ref_cod_ico, ord_menu, caminho, alvo, suprime_menu, ref_cod_tutormenu, 4 as nivel $menu_pai FROM pmicontrolesis.menu m WHERE ref_cod_menu_pai IN (SELECT cod_menu FROM pmicontrolesis.menu WHERE ref_cod_menu_pai IN ( SELECT cod_menu FROM pmicontrolesis.menu WHERE ref_cod_menu_pai IN ( SELECT cod_menu FROM pmicontrolesis.menu WHERE ref_cod_menu_pai IS NULL ) ) ) AND ref_cod_tutormenu = '$ref_cod_tutormenu'
					ORDER BY nivel ASC, ord_menu ASC
					";

	/*		$sql = "SELECT cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu, ref_cod_ico, ord_menu, caminho, alvo, suprime_menu, ref_cod_tutormenu, 1 as nivel FROM pmicontrolesis.menu m  WHERE ref_cod_menu_pai IS NULL AND ref_cod_tutormenu = '$ref_cod_tutormenu'
					UNION
					SELECT cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu, ref_cod_ico, ord_menu, caminho, alvo, suprime_menu, ref_cod_tutormenu, 2 as nivel FROM pmicontrolesis.menu WHERE ref_cod_menu_pai IN ( SELECT cod_menu FROM pmicontrolesis.menu WHERE ref_cod_menu_pai IS NULL )  AND ref_cod_tutormenu = '$ref_cod_tutormenu'
					UNION
					SELECT cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu, ref_cod_ico, ord_menu, caminho, alvo, suprime_menu, ref_cod_tutormenu, 3 as nivel FROM pmicontrolesis.menu WHERE ref_cod_menu_pai IN ( SELECT cod_menu FROM pmicontrolesis.menu WHERE ref_cod_menu_pai IN ( SELECT cod_menu FROM pmicontrolesis.menu WHERE ref_cod_menu_pai IS NULL ) )  AND ref_cod_tutormenu = '$ref_cod_tutormenu'
					UNION
					SELECT cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu, ref_cod_ico, ord_menu, caminho, alvo, suprime_menu, ref_cod_tutormenu, 4 as nivel FROM pmicontrolesis.menu WHERE ref_cod_menu_pai IN (SELECT cod_menu FROM pmicontrolesis.menu WHERE ref_cod_menu_pai IN ( SELECT cod_menu FROM pmicontrolesis.menu WHERE ref_cod_menu_pai IN ( SELECT cod_menu FROM pmicontrolesis.menu WHERE ref_cod_menu_pai IS NULL ) ) ) AND ref_cod_tutormenu = '$ref_cod_tutormenu'
					ORDER BY nivel ASC, ord_menu ASC
				";
*/
		}else
		{

			$menus = "";
			$juncao = "";
			$db->Consulta( "SELECT ref_cod_menu_submenu FROM menu_funcionario WHERE ref_ref_cod_pessoa_fj = '$idpes' UNION SELECT cod_menu_submenu FROM menu_submenu WHERE nivel ='2' UNION SELECT cod_menu_submenu FROM menu_submenu WHERE nivel ='2'");
			while ($db->ProximoRegistro())
			{
				$tupla = $db->Tupla();
				$menus .= "$juncao {$tupla['ref_cod_menu_submenu']}";
				$juncao = ", ";
			}
			//echo $menus;

			$sql = "	SELECT cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu, ref_cod_ico, ord_menu, caminho, alvo, suprime_menu, ref_cod_tutormenu, 1 as nivel FROM pmicontrolesis.menu m
						WHERE
							ref_cod_menu_pai IS NULL AND
							ref_cod_tutormenu = '$ref_cod_tutormenu'  AND
							((ref_cod_menu_submenu iS NULL) OR (ref_cod_menu_submenu IN ($menus)))
					UNION
						SELECT cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu, ref_cod_ico, ord_menu, caminho, alvo, suprime_menu, ref_cod_tutormenu, 2 as nivel FROM pmicontrolesis.menu m
						WHERE
							ref_cod_menu_pai IN
							(SELECT cod_menu FROM pmicontrolesis.menu m WHERE ref_cod_menu_pai IS NULL AND ref_cod_tutormenu = '$ref_cod_tutormenu'  AND ((ref_cod_menu_submenu iS NULL) OR (ref_cod_menu_submenu IN ($menus))))
							AND ((ref_cod_menu_submenu iS NULL) OR (ref_cod_menu_submenu IN ($menus)))

					UNION
						SELECT cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu, ref_cod_ico, ord_menu, caminho, alvo, suprime_menu, ref_cod_tutormenu, 3 as nivel FROM pmicontrolesis.menu m
						WHERE
						ref_cod_menu_pai IN
						(SELECT cod_menu FROM pmicontrolesis.menu m WHERE ref_cod_menu_pai IN (SELECT cod_menu FROM pmicontrolesis.menu m WHERE ref_cod_menu_pai IS NULL AND ref_cod_tutormenu = '$ref_cod_tutormenu'  AND ((ref_cod_menu_submenu iS NULL) OR (ref_cod_menu_submenu IN ($menus)))) AND ((ref_cod_menu_submenu iS NULL) OR (ref_cod_menu_submenu IN ($menus))))
						AND ((ref_cod_menu_submenu iS NULL) OR (ref_cod_menu_submenu IN ($menus)))
					UNION
						SELECT cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu, ref_cod_ico, ord_menu, caminho, alvo, suprime_menu, ref_cod_tutormenu, 4 as nivel FROM pmicontrolesis.menu m
						WHERE
						ref_cod_menu_pai IN (SELECT cod_menu FROM pmicontrolesis.menu m WHERE ref_cod_menu_pai IN (SELECT cod_menu FROM pmicontrolesis.menu m WHERE ref_cod_menu_pai IN (SELECT cod_menu FROM pmicontrolesis.menu m WHERE ref_cod_menu_pai IS NULL AND ref_cod_tutormenu = '$ref_cod_tutormenu'  AND ((ref_cod_menu_submenu iS NULL) OR (ref_cod_menu_submenu IN ($menus)))) AND ((ref_cod_menu_submenu iS NULL) OR (ref_cod_menu_submenu IN ($menus)))) AND ((ref_cod_menu_submenu iS NULL) OR (ref_cod_menu_submenu IN ($menus))))
						AND ((ref_cod_menu_submenu iS NULL) OR (ref_cod_menu_submenu IN ($menus)))
						ORDER BY nivel ASC, ord_menu ASC
					";

		}

		$db->Consulta($sql);
		$resultado = array();
		while ( $db->ProximoRegistro() )
		{
			$tupla = $db->Tupla();
			$tupla['total'] = $total;
			$resultado[] = $tupla;
		}
		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
	}
}
?>
