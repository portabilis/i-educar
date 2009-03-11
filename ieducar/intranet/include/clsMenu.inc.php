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
require_once( "include/clsBanco.inc.php" );

class clsMenu
{
	var $aberto;

	/**
	 * @var $linhaTemplate	item do submenu
	 * @var $categoriaTemplate MENU
	 */
	/*public */ function MakeMenu( $linhaTemplate, $categoriaTemplate )
	{

		$this->aberto = 0;
		$saida = "";
		$linha_nova = $linhaTemplate;
		$linha_nova_subtitulo = $categoriaTemplate;
		$super_usuario = "";

		$itens_menu = array();
		$autorizado_menu = array();

		@session_start();
		$id_usuario = $_SESSION['id_pessoa'];
		$opcoes_menu = $_SESSION['menu_opt'];


		$dba = new clsBanco();
		$dba->Consulta( "SELECT ref_cod_menu_submenu FROM menu_funcionario WHERE ref_ref_cod_pessoa_fj={$id_usuario}" );
		while ($dba->ProximoRegistro())
		{
			list ($aut) = $dba->Tupla();
			$autorizado_menu[] = $aut;
			if($aut ==0)
			{
				$super_usuario = true;
			}
		}

		$strAutorizado = implode(", ", $autorizado_menu);


		if (@$_SESSION['convidado'])
		{
			$strAutorizado="999999";
		}
		session_write_close();

		$db = new clsBanco();

		if ($strAutorizado == "0" || $super_usuario)
		{
			if($_GET['suspenso'] ==1 || $_SESSION['suspenso']== 1 || $_SESSION['tipo_menu'] == 1 )
			{

			$sql ="
					SELECT  pai.nm_menu,
						nome_menu.nm_menu as nm_menu_pai,
						pai.title AS title_pai,
						sub.nm_submenu,
						sub.arquivo,
						sub.title,
						pai.cod_menu_menu,
						case
						when pai.ref_cod_menu_pai is null
						   then 0
						else
						   1
					        end as ref_menu_pai
					  FROM menu_menu as pai
						 left outer join
					       menu_menu as filho
					         on (filho.ref_cod_menu_pai = pai.cod_menu_menu AND pai.ref_cod_menu_pai = null)
					       ,menu_submenu as sub
					       ,menu_menu as nome_menu
					 WHERE nome_menu.cod_menu_menu = coalesce (pai.ref_cod_menu_pai,pai.cod_menu_menu) and
					       sub.cod_sistema = '2' AND
					       pai.cod_menu_menu = sub.ref_cod_menu_menu AND
					       sub.cod_menu_submenu not in (select ref_cod_menu_submenu
									      FROM pmicontrolesis.menu
									     WHERE suprime_menu =1
					 				       AND ref_cod_menu_submenu IS NOT null)
					ORDER BY
						upper(nome_menu.nm_menu),ref_menu_pai,upper(pai.nm_menu),sub.nm_submenu
				";
			//nm_menu_pai
			}
			else
			{

			$sql ="
					SELECT  DISTINCT pai.nm_menu,
						nome_menu.nm_menu as nm_menu_pai,
						pai.title AS title_pai,
						sub.nm_submenu,
						sub.arquivo,
						sub.title,
						pai.cod_menu_menu,
						case
						when pai.ref_cod_menu_pai is null
						   then 0
						else
						   1
					        end as ref_menu_pai,
					        upper(nome_menu.nm_menu),upper(pai.nm_menu),sub.nm_submenu
					  FROM menu_menu as pai
						 left outer join
					       menu_menu as filho
					         on (filho.ref_cod_menu_pai = pai.cod_menu_menu)
					       ,menu_submenu as sub
					       ,menu_menu as nome_menu
					 WHERE nome_menu.cod_menu_menu = coalesce (pai.ref_cod_menu_pai,pai.cod_menu_menu) and
					       sub.cod_sistema = '2' AND
					       pai.cod_menu_menu = sub.ref_cod_menu_menu
					ORDER BY
						upper(nome_menu.nm_menu),ref_menu_pai,upper(pai.nm_menu),sub.nm_submenu
				";
			}
		}
		else
		{
			$query_lista = "";
			if( $strAutorizado )
			{
				$query_lista = "sub.cod_menu_submenu in ({$strAutorizado}) OR ";
			}

			$suspenso = "";
			if($_GET['suspenso'] ==1 || $_SESSION['suspenso']== 1 || $_SESSION['tipo_menu'] == 1 )
			{
				$suspenso = " AND sub.cod_menu_submenu not in (select ref_cod_menu_submenu FROM pmicontrolesis.menu WHERE suprime_menu =1 AND ref_cod_menu_submenu IS NOT null)";

			}

			if ( $strAutorizado=="999999" )
			{
			$sql ="
					SELECT  pai.nm_menu,
						nome_menu.nm_menu as nm_menu_pai,
						pai.title AS title_pai,
						sub.nm_submenu,
						sub.arquivo,
						sub.title,
						pai.cod_menu_menu,
						case
						when pai.ref_cod_menu_pai is null
						   then 0
						else
						   1
					        end as ref_menu_pai
					  FROM menu_menu as pai
						 left outer join
					       menu_menu as filho
					         on (filho.ref_cod_menu_pai = pai.cod_menu_menu)
					       ,menu_submenu as sub
					       ,menu_menu as nome_menu
					 WHERE nome_menu.cod_menu_menu = coalesce (pai.ref_cod_menu_pai,pai.cod_menu_menu) and
					       sub.cod_sistema = '2' AND
					       pai.cod_menu_menu = sub.ref_cod_menu_menu AND
					       ($query_lista
					       sub.cod_menu_submenu in (SELECT sub2.cod_menu_submenu FROM menu_submenu sub2 WHERE sub2.nivel='1')
					       )
					ORDER BY
						upper(nome_menu.nm_menu),ref_menu_pai,upper(pai.nm_menu),sub.nm_submenu
				";
			//
			}
			else
			{

			$sql ="
					SELECT DISTINCT  pai.nm_menu,
						nome_menu.nm_menu as nm_menu_pai,
						pai.title AS title_pai,
						sub.nm_submenu,
						sub.arquivo,
						sub.title,
						pai.cod_menu_menu,
						case
						when pai.ref_cod_menu_pai is null
						   then 0
						else
						   1
					        end as ref_menu_pai,
					        upper(nome_menu.nm_menu),
					        upper(pai.nm_menu)
					  FROM menu_menu as pai
						 left outer join
					       menu_menu as filho
					         on (filho.ref_cod_menu_pai = pai.cod_menu_menu)
					       ,menu_submenu as sub
					       ,menu_menu as nome_menu
					 WHERE nome_menu.cod_menu_menu = coalesce (pai.ref_cod_menu_pai,pai.cod_menu_menu) and
					       sub.cod_sistema = '2' AND
					       pai.cod_menu_menu = sub.ref_cod_menu_menu AND
					       ($query_lista
					       sub.cod_menu_submenu in (SELECT sub2.cod_menu_submenu FROM menu_submenu sub2 WHERE sub2.nivel='2') )
					       $suspenso
					ORDER BY
						upper(nome_menu.nm_menu),ref_menu_pai,upper(pai.nm_menu),sub.nm_submenu
				";
			}
		}
//
		$db->Consulta( $sql );

		while ($db->ProximoRegistro())
		{
			list ($nome,$nomepai, $titlepai, $nomesub, $arquivo, $titlesub, $cod_submenu,$ref_menu_pai) = $db->Tupla();
			$itens_menu[] = array($nome,$nomepai, $titlepai, $nomesub, $arquivo, $titlesub, $cod_submenu,$ref_menu_pai);
		}

		$saida = "";
		$menuPaiAtual = "";
		foreach ($itens_menu as $item)
		{
			if ( $item[0] != $menuPaiAtual )
			{
				$estilo_linha = "nvp_cor_sim";

				$this->aberto = 0;
				$menuPaiId = $item[6];
				if(isset($_COOKIE["menu_{$menuPaiId}"]))
				{
					if( $_COOKIE["menu_{$menuPaiId}"] == 'V' )
					{
						$this->aberto = 1;
					}
				}
				// define a acao para ser contraria ao status atual
				if( $this->aberto )
				{
					$imagem = "up2";
					$acao = 0;
					$simbolo = "-";
					$title_acao = "Fechar a categoria";
				}
				else
				{
					$imagem = "down2";
					$acao = 1;
					$simbolo = "+";
					$title_acao = "Abrir a categoria";
				}
				$saida = str_replace("<!-- #&MENUS&# -->",  $submenus, $saida);
				$submenus = "";
				// Adiciona um menu pai
				$aux_temp = $linha_nova_subtitulo;
				$aux_temp = str_replace("<!-- #&NOME&# -->", $item[0], $aux_temp);
				$aux_temp = str_replace("<!-- #&ALT&# -->",  $item[3], $aux_temp);
				$aux_temp = str_replace("<!-- #&ID&# -->",  $item[6], $aux_temp);
				$aux_temp = str_replace("<!-- #&ACAO&# -->",  $acao, $aux_temp);
				$aux_temp = str_replace("<!-- #&SIMBOLO&# -->",  $simbolo, $aux_temp);
				$aux_temp = str_replace("<!-- #&TITLE_ACAO&# -->",  $title_acao, $aux_temp);
				$aux_temp = str_replace("<!-- #&MENUPAI&# -->",  $item[0], $aux_temp);
				$aux_temp = str_replace("<!-- #&IMAGEM&# -->",  $imagem, $aux_temp);
				$aux_temp = str_replace("<!-- #&IDMENUPAI&# -->",  $menuPaiId, $aux_temp);

				$style = $this->aberto == 1 ? "" : "style='display:none;'";
				$aux_temp = str_replace("<!-- #&STYLE&# -->", $style , $aux_temp);

				$saida .= $aux_temp;

				// Define que este é o menu pai atual
				$menuPaiAtual = $item[0];


			}

			// so mosta se estiver aberto
			//menus filhos
			//if( $this->aberto != 0 )
			{
				$aux_temp = $linha_nova;
				if (substr($item[4], 0, 5) == "http:")
				{
					$target = "_blank";
				}
				else
				{
					$target = "_top";
				}
				$estilo_linha = $estilo_linha=="nvp_cor_sim" ? "nvp_cor_nao" : "nvp_cor_sim";
				$aux_temp = str_replace("<!-- #&CLASS&# -->", $estilo_linha, $aux_temp);
				$aux_temp = str_replace("<!-- #&NOME&# -->", $item[3], $aux_temp);
				$aux_temp = str_replace("<!-- #&LINK&# -->", $item[4], $aux_temp);
				$aux_temp = str_replace("<!-- #&ALT&# -->",  $item[3], $aux_temp);
				$aux_temp = str_replace("<!-- #&TARGET&# -->",  $target, $aux_temp);
				$submenus .= $aux_temp;
			}

		}
		$saida = str_replace("<!-- #&MENUS&# -->",  $submenus, $saida);
		return $saida;
	}
}
?>