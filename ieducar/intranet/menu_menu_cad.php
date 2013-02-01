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
$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Cadastro de Menu!" );
		$this->processoAp = "79";
	}
}

class indice extends clsCadastro
{
	var $id_item,
		$nome_,
		$title,
		$ref_cod_menu_pai;

	function Inicializar()
	{
		$retorno = "Novo";
		
		if (@$_GET['id_item'])
		{
			$this->id_item = @$_GET['id_item'];
			$db = new clsBanco();
			$db->Consulta( "SELECT  nm_menu, title, ref_cod_menu_pai FROM menu_menu WHERE cod_menu_menu={$this->id_item}" );
			if ($db->ProximoRegistro())
			{
				list($this->nome_, $this->title, $this->ref_cod_menu_pai) = $db->Tupla();
				$this->fexcluir = true;
				$retorno = "Editar";
			}
		}

		$this->url_cancelar = ($retorno == "Editar") ? "menu_menu_det.php?id_item=$this->id_item" : "menu_menu_lst.php";
		$this->nome_url_cancelar = "Cancelar";

		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto( "id_item", $this->id_item );		
		$lista = array();
		$this->campoTexto( "nome_", "Nome",  $this->nome_, "50", "100", true );
		
		$combo = array( '' => "Sem menu pai");
		
		$db = new clsBanco();
		if(!empty($this->id_item))
			$where = "WHERE cod_menu_menu!={$this->id_item} and exists(select 1 from menu_menu as t2 where t2.cod_menu_menu = t1.cod_menu_menu and t2.ref_cod_menu_pai is null) and cod_menu_menu != 1";
		else 
			$where = "WHERE cod_menu_menu != 1 and exists(select 1 from menu_menu as t2 where t2.cod_menu_menu = t1.cod_menu_menu and t2.ref_cod_menu_pai is null)";
		
		$order_by = " order by 2";	
		
		$db->Consulta( "SELECT  cod_menu_menu,nm_menu,ref_cod_menu_pai FROM menu_menu as t1 {$where} {$order_by}" );
		while($db->ProximoRegistro())
		{
			list($this->id_item,$this->nome_) = $db->Tupla();
			$combo[$this->id_item] = ucfirst($this->nome_);
		}
		
		$this->campoLista("ref_cod_menu_pai", "Menu Pai:", $combo, $this->ref_cod_menu_pai, false, false, false, false,false,false);
		
		$this->campoMemo( "title", "Title",  $this->title, "47", "2", false );
	}

	function Novo() 
	{
		if(empty($this->ref_cod_menu_pai))
			$this->ref_cod_menu_pai = "null";	
		else	
			$this->ref_cod_menu_pai = " '{$this->ref_cod_menu_pai}' ";			
		$db = new clsBanco();
		$db->Consulta( "INSERT INTO menu_menu (nm_menu, title, ref_cod_menu_pai) VALUES ('{$this->nome_}', '{$this->title}', {$this->ref_cod_menu_pai})" );

		echo "<script>document.location='menu_menu_lst.php';</script>";

		return true;
	}

	function Editar() 
	{
		if(empty($this->ref_cod_menu_pai))
			$this->ref_cod_menu_pai = "null";	
		else	
			$this->ref_cod_menu_pai = " '{$this->ref_cod_menu_pai}' ";	
		$db = new clsBanco();
		$db->Consulta( "UPDATE menu_menu SET nm_menu='{$this->nome_}', title='{$this->title}', ref_cod_menu_pai = {$this->ref_cod_menu_pai} WHERE cod_menu_menu = {$this->id_item}" );
		echo "<script>document.location='menu_menu_lst.php';</script>";
		return true;
	}

	function Excluir()
	{
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM menu_menu WHERE cod_menu_menu ={$this->id_item}" );
		echo "<script>document.location='menu_menu_lst.php';</script>";
		return true;
	}
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
