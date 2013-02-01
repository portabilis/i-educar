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

class clsIndex extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Opção Menu" );
		$this->processoAp = "475";
	}
}

class indice extends clsCadastro
{
	var $idpes,
		$tipo_menu;
		
	function Inicializar()
	{
		$retorno = "Editar";

		@session_start();
		 $this->idpes = $_SESSION['id_pessoa'];
		@session_write_close();
		
		if($this->idpes)
		{
			$db = new clsBanco();
			$this->tipo_menu = $db->UnicoCampo("SELECT tipo_menu FROM funcionario WHERE ref_cod_pessoa_fj = '$this->idpes'");
		}
		$this->url_cancelar = "opcao_menu_det.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		$opcao = array("0"=>"Menu Padrão","1"=> "Menu Suspenso");
		$this->campoRadio("tipo_menu","Tipo do Menu",$opcao,$this->tipo_menu);
		$this->campoOculto("idpes",$this->idpes);
	}

	function Novo() 
	{
		return false;
	}

	function Editar() 
	{
		$db = new clsBanco();
		$db->Consulta("UPDATE funcionario SET tipo_menu='$this->tipo_menu' WHERE ref_cod_pessoa_fj = '$this->idpes' ");
		
		@session_start();
		$_SESSION['tipo_menu'] = $this->tipo_menu;
		@session_write_close();
		
		header("Location: opcao_menu_det.php");
		return false;
	}

	function Excluir()
	{
		return true;
	}

}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();
?>
