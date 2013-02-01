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
		$this->SetTitulo( "{$this->_instituicao} Tipos de Notícias!" );
		$this->processoAp = "104";
	}
}

class indice extends clsCadastro
{
	var $id_tipo;
	var $nome_tipo;

	function Inicializar()
	{
		$retorno = "Novo";
		$this->id_tipo = @$_GET['id_tipo'];
		if ($this->id_tipo)
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT  nm_tipo FROM not_tipo WHERE cod_not_tipo ={$this->id_tipo}" );
			if ($db->ProximoRegistro())
			{
				list($this->nome_tipo) = $db->Tupla();
				$this->fexcluir = true;
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "not_tipos_det.php?id_tipo=$this->id_tipo" : "not_tipos_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto( "id_tipo", $this->id_tipo);
		$this->campoTexto( "nome_tipo", "Nome do Tipo",  $this->nome_tipo, "50", "250", true );
	}

	function Novo() 
	{
		$db = new clsBanco();
		$db->Consulta( "INSERT INTO not_tipo (nm_tipo) VALUES ('{$this->nome_tipo}')" );
		echo "<script>document.location='not_tipos_lst.php';</script>";
		return true;
	}

	function Editar() 
	{
		$db = new clsBanco();
		$db->Consulta( "UPDATE not_tipo SET nm_tipo='{$this->nome_tipo}' WHERE cod_not_tipo={$this->id_tipo}" );
		echo "<script>document.location='not_tipos_lst.php';</script>";
		return true;
	}

	function Excluir()
	{
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM not_tipo WHERE cod_not_tipo={$this->id_tipo}" );
		echo "<script>document.location='not_tipos_lst.php';</script>";
		return true;
	}
}
$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();
?>
