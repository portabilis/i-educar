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
		$this->SetTitulo( "{$this->_instituicao} Banner!" );
		$this->processoAp = "89";
	}
}

class indice extends clsCadastro
{
	var $cod_portal_banner,
		$ref_ref_cod_pessoa_fj,
		$caminho,
		$title,
		$prioridade,
		$link,
		$lateral;

	function Inicializar()
	{
		@session_start();
		$id_pessoa = $_SESSION['id_pessoa'];
		session_write_close();

		$retorno = "Novo";
		 
		if (@$_GET['cod_portal_banner'])
		{
			$this->cod_portal_banner = @$_GET['cod_portal_banner'];
			$db = new clsBanco();
			$db->Consulta( "SELECT cod_portal_banner, ref_ref_cod_pessoa_fj, caminho, title, prioridade, link, lateral FROM portal_banner WHERE cod_portal_banner={$this->cod_portal_banner}" );
			if ($db->ProximoRegistro())
			{
				list($this->cod_portal_banner, $this->ref_ref_cod_pessoa_fj, $this->caminho, $this->title, $this->prioridade, $this->link, $this->lateral ) = $db->Tupla();
				$this->fexcluir = true;
				$retorno = "Editar";
			}
		}
		
		$this->url_cancelar = ($retorno == "Editar") ? "banner_det.php?cod_portal_banner=$this->cod_portal_banner" : "banner_lst.php";
		$this->nome_url_cancelar = "Cancelar";

		return $retorno;
	}

	function Gerar()
	{
		
		$this->campoOculto( "cod_portal_banner", $this->cod_portal_banner );
		$this->campoTexto( "title", "Titulo", $this->title, "50", "100", true );
		$this->campoTexto( "link", "Link", $this->link, "50", "100", false );
		$this->campoTexto( "prioridade", "Prioridade",  $this->prioridade, "5", "4", false );
		$this->campoArquivo("caminho", "Arquivo", $this->caminho, "50");
		$opcoes = array( "Não", "Sim" );
		$this->campoLista( "lateral", "Lateral", $opcoes, $this->lateral );
	}

	function Novo() 
	{
		global $HTTP_POST_FILES;
		$caminho  = "";

		if ( !empty($HTTP_POST_FILES['caminho']['name']) )
		{
			$caminho .= date("Y-m-d")."-";
			list($usec, $sec) = explode(" ", microtime());
			$caminho .= substr(md5("{$usec}{$sec}"), 0, 8);
			while (file_exists("fotos/imgs/{$caminho}"))
			{
				$caminho = $caminho . "a";
			}
			$caminho .= ".jpg";
			copy($HTTP_POST_FILES['caminho']['tmp_name'], "fotos/imgs/{$caminho}");
		}
		else
		{
			return false;
		}
			
		@session_start();
		$this->ref_ref_cod_pessoa_fj = @$_SESSION['id_pessoa'];
		session_write_close();
		
		$db = new clsBanco();
		$db->Consulta( "INSERT INTO portal_banner ( ref_ref_cod_pessoa_fj, caminho, title, prioridade, link, lateral ) VALUES ({$this->ref_ref_cod_pessoa_fj}, '{$caminho}', '{$this->title}', {$this->prioridade}, '{$this->link}', '{$this->lateral}')" );
		echo "<script>document.location='banner_lst.php';</script>";
		return true;
	}

	function Editar() 
	{
		@session_start();
		$this->ref_ref_cod_pessoa_fj = @$_SESSION['id_pessoa'];
		session_write_close();
		
		$db = new clsBanco();
		$db->Consulta( "UPDATE portal_banner SET ref_ref_cod_pessoa_fj={$this->ref_ref_cod_pessoa_fj}, title='{$this->title}', prioridade={$this->prioridade}, link='{$this->link}', lateral='{$this->lateral}' WHERE cod_portal_banner={$this->cod_portal_banner}" );
		echo "<script>document.location='banner_lst.php';</script>";
		return true;
	}

	function Excluir()
	{
		$db = new clsBanco();
		$db->Consulta("SELECT caminho FROM portal_banner WHERE cod_portal_banner = {$this->cod_portal_banner}");
		$db->ProximoRegistro();
		list ($caminho) = $db->Tupla();
		$db->Consulta( "DELETE FROM portal_banner WHERE cod_portal_banner = {$this->cod_portal_banner}" );
		
		@unlink("fotos/imgs/{$caminho}");
		echo "<script>document.location='banner_lst.php';</script>";			
	}

}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
