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
		$this->SetTitulo( "{$this->_instituicao} Emails!" );
		$this->processoAp = "86";
	}
}

class indice extends clsCadastro
{
	var $id_email;
	var $nome_pessoa;
	var $email_;

	function Inicializar()
	{
		$retorno = "Novo";
		$this->id_email= @$_GET['id_email'];
		if ($this->id_email)
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT  nm_pessoa, email FROM mailling_email WHERE cod_mailling_email={$this->id_email}" );
			if ($db->ProximoRegistro())
			{
				list($this->nome_pessoa,$this->email_) = $db->Tupla();
				$this->fexcluir = true;
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "mailling_email_det.php?id_email=$this->id_email" : "mailling_email_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto( "id_email", $this->id_email);
		$this->campoTexto( "nome_pessoa", "Nome",  $this->nome_pessoa, "50", "250", true );
		$this->campoTexto( "email_", "Email",  $this->email_, "50", "250", true );
	}

	function Novo()
	{
		$db = new clsBanco();
		$db->Consulta( "INSERT INTO mailling_email (nm_pessoa, email) VALUES ('{$this->nome_pessoa}','{$this->email_}' )" );
		echo "<script>document.location='mailling_email_lst.php';</script>";
		return true;
	}

	function Editar()
	{
		$db = new clsBanco();
		$db->Consulta( "UPDATE mailling_email SET nm_pessoa='{$this->nome_pessoa}', email='{$this->email_}' WHERE cod_mailling_email={$this->id_email}" );
		echo "<script>document.location='mailling_email_lst.php';</script>";
		return true;
	}

	function Excluir()
	{
		$db = new clsBanco();

		$db->Consulta( "DELETE FROM mailling_grupo_email WHERE ref_cod_mailling_email={$this->id_email}" );
		$db->Consulta( "UPDATE mailling_fila_envio SET ref_cod_mailling_email = NULL WHERE ref_cod_mailling_email = {$this->id_email}" );
		$db->Consulta( "DELETE FROM mailling_email WHERE cod_mailling_email = {$this->id_email}" );


		echo "<script>document.location='mailling_email_lst.php';</script>";
		return true;
	}
}
$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();
?>
