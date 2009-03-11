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
		$this->SetTitulo( "{$this->_instituicao} Grupos de Email!" );
		$this->processoAp = "85";
	}
}

class indice extends clsCadastro
{
	var $id_grupo;
	var $nome_grupo;

	function Inicializar()
	{
		$retorno = "Novo";
		$this->id_grupo = @$_GET['id_grupo'];
		if ($this->id_grupo)
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT  nm_grupo FROM mailling_grupo WHERE cod_mailling_grupo ={$this->id_grupo}" );
			if ($db->ProximoRegistro())
			{
				list($this->nome_grupo) = $db->Tupla();
				$this->fexcluir = true;
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "mailling_grupos_det.php?id_grupo=$this->id_grupo" : "mailling_grupos_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto( "id_grupo", $this->id_grupo);
		$this->campoTexto( "nome_grupo", "Nome do Grupo",  $this->nome_grupo, "50", "250", true );
		$db = new clsBanco();

		$lis = array();
		if($this->id_grupo)
		{
			$db->Consulta( "
				SELECT e.nm_pessoa, e.cod_mailling_email, e.email
				FROM mailling_email e, mailling_grupo_email g
				WHERE g.ref_cod_mailling_grupo='{$this->id_grupo}'
				AND e.cod_mailling_email = g.ref_cod_mailling_email
				ORDER BY to_ascii(nm_pessoa) ASC
			" );
			while ($db->ProximoRegistro())
			{
				list($nome, $cod_email,$email) = $db->Tupla();
				$this->campoCheck("ch_{$cod_email}", "Menus", true, "{$nome} - {$email}");
			}
		}
		$db->Consulta( "
			SELECT nm_pessoa, cod_mailling_email, email
			FROM mailling_email
			WHERE cod_mailling_email NOT IN ( SELECT ref_cod_mailling_email FROM mailling_grupo_email )
			ORDER BY to_ascii(nm_pessoa) ASC
		" );
		while ($db->ProximoRegistro())
		{
			list($nome, $cod_email,$email) = $db->Tupla();
			$this->campoCheck("ch_{$cod_email}", "Menus", false, "{$nome} - {$email}");
		}
	}

	function Novo()
	{
		$db = new clsBanco();
		$db->Consulta( "INSERT INTO mailling_grupo (nm_grupo) VALUES ('{$this->nome_grupo}')" );
		//$db->Consulta("SELECT LAST_INSERT_ID() FROM mailling_grupo");
		$last_id = $db->insertId('portal.mailling_grupo_cod_mailling_grupo_seq');
		//$db->ProximoRegistro();
		//list($last_id) = $db->Tupla();

		foreach ($_POST as $chave=>$valor)
		{
			if(substr($chave,0,3) == "ch_")
			{
				$cod = substr($chave,3);
				$db->Consulta("INSERT INTO mailling_grupo_email (ref_cod_mailling_email, ref_cod_mailling_grupo) VALUES ($cod,$last_id) ");
			}
		}

		echo "<script>document.location='mailling_grupos_lst.php';</script>";
		return true;
	}

	function Editar()
	{
		$db = new clsBanco();
		$db->Consulta( "UPDATE mailling_grupo SET nm_grupo='{$this->nome_grupo}' WHERE cod_mailling_grupo={$this->id_grupo}" );
		reset($_POST);
		$db->Consulta("DELETE FROM mailling_grupo_email WHERE ref_cod_mailling_grupo ={$this->id_grupo} ");
		foreach ($_POST as $chave=>$valor)
		{
			if(substr($chave,0,3) == "ch_")
			{
				$cod = substr($chave,3);
				$db->Consulta("INSERT INTO mailling_grupo_email (ref_cod_mailling_email, ref_cod_mailling_grupo) VALUES ($cod,$this->id_grupo) ");
			}
		}
		echo "<script>document.location='mailling_grupos_lst.php';</script>";
		return true;
	}

	function Excluir()
	{
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM mailling_grupo_email WHERE ref_cod_mailling_grupo={$this->id_grupo}" );
		$db->Consulta( "DELETE FROM mailling_grupo WHERE cod_mailling_grupo={$this->id_grupo}" );
		echo "<script>document.location='mailling_grupos_lst.php';</script>";
		return true;
	}
}
$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();
?>
