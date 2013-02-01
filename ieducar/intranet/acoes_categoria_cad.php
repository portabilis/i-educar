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
require_once ("include/pmiacoes/geral.inc.php");
require_once( "include/Geral.inc.php" );

class clsIndex extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Sistema de Cadastro de Ações do Governo - Cadastro de Categorias" );
		$this->processoAp = "552";
	}
}

class indice extends clsCadastro
{
	var $pessoa_logada;
	
	var $cod_categoria,
		$nm_categoria;
		
	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$this->cod_categoria = $_GET['cod_categoria'];
		
		if($this->cod_categoria)
		{
			$obj = new clsPmiacoesCategoria($this->cod_categoria);
			$detalhe  = $obj->detalhe();
			$this->nm_categoria = $detalhe['nm_categoria'];
			
			
			$obj_acao = new clsPmiacoesAcaoGovernoCategoria();
			$lista = $obj_acao->lista($this->cod_categoria);
			if(!$lista)
				$this->fexcluir = true;		
			
			
			$retorno = "Editar";
		}
		$this->url_cancelar = ($retorno == "Editar") ? "acoes_categoria_det.php?cod_categoria={$this->cod_categoria}" : "acoes_categoria_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto("cod_categoria", $this->cod_categoria);
		$this->campoOculto("pessoa_logada", $this->pessoa_logada);
		$this->campoTexto("nm_categoria", "Nome", $this->nm_categoria,30,255,true);
	}
 
	function Novo() 
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		$obj = new clsPmiacoesCategoria(null, null, $this->pessoa_logada, $this->nm_categoria, null, null, 1);
		if($obj->cadastra())
		{
			header("Location: acoes_categoria_lst.php");
		}
		return false;
	}

	function Editar() 
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		$obj = new clsPmiacoesCategoria($this->cod_categoria, $this->pessoa_logada, null, $this->nm_categoria, null, null, 1);
		if($obj->edita())
		{
			header("Location: acoes_categoria_lst.php");
		}
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$obj_acao = new clsPmiacoesAcaoGovernoCategoria();
		$lista = $obj_acao->lista($this->cod_categoria);
		if($lista)	 
			echo "<script>alert('Não é possível excluir o registro! \n Existe ação utilizando esta categoria');window.location = \"acoes_categoria_lst.php\";</script>";
		
			$obj = new clsPmiacoesCategoria($this->cod_categoria, $this->pessoa_logada, null, $this->nm_categoria, null, null, 0);	
		$obj->excluir();
		header("Location: acoes_categoria_lst.php");
		return true;
	}
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();
?>
