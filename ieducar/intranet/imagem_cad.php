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
require_once ("include/imagem/clsPortalImagemTipo.inc.php");
require_once ("include/imagem/clsPortalImagem.inc.php");

class clsIndex extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Banco de Imagens" );
		$this->processoAp = "473";
	}
}

class indice extends clsCadastro
{
	var $pessoa_logada;
	var $nome_reponsavel;
	
	var $cod_imagem;
	var $ref_cod_imagem_tipo;
	var $caminho;
	var $nm_imagem;
	var $extensao;
	var $altura;
	var $largura;
	var $data_cadastro;
	var $ref_cod_pessoa_cad;
	var $data_exclusao;
	var $ref_cod_pessoa_exc;
		
	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$this->cod_imagem = $_GET['cod_imagem'];
		
		if($this->cod_imagem)
		{
			$obj = new clsPortalImagem($this->cod_imagem);
			
			$detalhe  = $obj->detalhe();
			$this->nm_tipo = $detalhe['nm_tipo'];
			$this->ref_cod_imagem_tipo = $detalhe['ref_cod_imagem_tipo'];
			$this->caminho = $detalhe['caminho'];
			$this->nm_imagem = $detalhe['nm_imagem'];
			$this->extensao = $detalhe['extensao'];
			$this->altura = $detalhe['altura'];
			$this->largura = $detalhe['largura'];
			$this->data_cadastro = date("d/m/Y", strtotime(substr($detalhe['data_cadastro'],0,19)) );
			$this->ref_cod_pessoa_cad = $detalhe['ref_cod_pessoa_cad'];
			$this->data_exclusao = date("d/m/Y", strtotime(substr($detalhe['data_exclusao'],0,19)) );
			$this->ref_cod_pessoa_exc = $detalhe['ref_cod_pessoa_exc'];
			$this->fexcluir = true;		
			$retorno = "Editar";
		}
		$this->url_cancelar = ($retorno == "Editar") ? "imagem_det.php?cod_imagem={$this->cod_imagem}" : "imagem_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto( "cod_imagem", $this->cod_imagem_tipo);
		$ObjTImagem = new clsPortalImagemTipo();
		$TipoImagem = $ObjTImagem->lista();
		$listaTipo = array();
		if($TipoImagem)
		{
			foreach ($TipoImagem as $dados)
			{
				$listaTipo[$dados['cod_imagem_tipo']] = $dados['nm_tipo']; 
			}
		}
		$this->campoOculto("cod_imagem", $this->cod_imagem);
		$this->campoOculto("altura", $this->altura);	
		$this->campoOculto("largura", $this->largura);	
		$this->campoOculto("extensao", $this->extensao);	
		$this->campoLista("ref_cod_imagem_tipo", "Tipo da Imagem", $listaTipo, $this->ref_cod_imagem_tipo);
		$this->campoTexto("nm_imagem", "Nome da Imagem", $this->nm_imagem,30,255,true);
		$this->campoArquivo("caminho", "Imagem", $this->caminho, 30);	
	}

	function Novo() 
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		$obj = new clsPortalImagem(false, $this->ref_cod_imagem_tipo, false, $this->nm_imagem, false,false,  false,false, $this->pessoa_logada, false, false);
		if($obj->cadastra())
		{			
			header("Location: imagem_lst.php");			
		}
		return false;
	}

	function Editar() 
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();					
			 
  		$obj = new clsPortalImagem($this->cod_imagem, $this->ref_cod_imagem_tipo, false, $this->nm_imagem, false, false, false,false, $this->pessoa_logada, false, false);
		if($obj->edita())
		{								
			header("Location: imagem_det.php?cod_imagem={$this->cod_imagem}");					
		}
 			
		return true;
	}

	function Excluir()
	{
		$ObjImg = new clsPortalImagem($this->cod_imagem);	
	    $ObjImg->excluir();											
	    header("Location: imagem_lst.php");
	}
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();
?>
