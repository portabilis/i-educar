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
		$this->SetTitulo( "{$this->_instituicao} Sistema de Cadastro de Aï¿½ï¿½es do Governo - Cadastro de a&ccedil;&otilde;es do Governo" );
		$this->processoAp = "551";

	}
}

class indice extends clsCadastro
{
	var $pessoa_logada;
	var $cod_acao_governo;
	var $nm_acao;
	var $descricao;
	var $data_inauguracao;
	var $valor;
	var $destaque;
	var $categorias = array();
	var $categoria;
	var $idbai;
	var $fotos_portal = array();
	var $noticias = array();
	var $nm_arquivo = array();
	var $secretarias = array();
	var $nm_fotos = array();
	var $arquivos = array();
	var $fotos = array();
	var $data_fotos = array();
	
	//edicao
	var $edit_categorias = array();
	var $edit_fotos_portal = array();
	var $edit_noticias = array();
	var $edit_nm_arquivo = array();
	var $edit_secretarias = array();
	//var $edit_nm_fotos = array();
	var $edit_arquivos = array();
	//var $edit_nm_arquivos = array();
	var $edit_fotos = array();
	var $edit_data_fotos = array();	
	var $edit_nm_fotos = array();	
	
	
	var $valida = "acao();";
	
	function Inicializar()
	{
		$retorno = "Novo";
		
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		
		@session_write_close();
		
		$this->cod_acao_governo = $_GET['cod_acao_governo'];
		
		$obj = new clsPmiacoesAcaoGoverno($this->cod_acao_governo,null,null,null,null,null,null,null,null,null,1);
		$detalhe  = $obj->detalhe();
		if(!$detalhe && $this->cod_acao_governo)
			header("location: acoes_acao_lst.php");
		if($detalhe)
		{			

			$this->nm_acao = $detalhe['nm_acao'];
			$this->descricao = $detalhe['descricao'];
			$this->numero = $detalhe['numero'];
			$this->categoria = $detalhe['categoria'];
			$this->data_inauguracao = $detalhe['data_inauguracao'];
			$this->idbai = $detalhe['idbai'];
			$this->valor = $detalhe['valor'];
			$this->destaque = $detalhe['destaque'];
						
			$this->fexcluir = true;		
			$retorno = "Editar";

			$obj_funcionario = new clsFuncionario($this->pessoa_logada);
			$detalhe_func = $obj_funcionario->detalhe();
			$setor_funcionario = $detalhe_func["ref_cod_setor_new"];
			
			//*
			$obj = new clsSetor();
			$setor_pai = array_shift(array_reverse($obj->getNiveis($setor_funcionario)));
			//*
			$obj_secretaria_responsavel = new clsPmiacoesSecretariaResponsavel($setor_pai);
			$obj_secretaria_responsavel_det = $obj_secretaria_responsavel->detalhe();
			if($obj_secretaria_responsavel_det == false && $detalhe["status_acao"] == 1)
				header("location: acoes_acao_lst.php");		
				
		}
		$this->url_cancelar = ($retorno == "Editar") ? "acoes_acao_det.php?cod_acao_governo={$this->cod_acao_governo}" : "acoes_acao_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		$this->acao_enviar =$this->valida;
		return $retorno;
	}

	function Gerar()
	{
		
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();		
		
		$this->form_enctype = " enctype='multipart/form-data'";	
		$this->cod_acao_governo ? $this->campoOculto("cod_acao_governo",$this->cod_acao_governo) : null;
		
		$this->campoTexto( "nm_acao", "Nome da ação", $this->nm_acao, 30, 255, true );	
		$this->campoMemo( "descricao", "Descrição", $this->descricao,100,5,false);
		$this->campoData( "data_inauguracao", "Data da inauguração", dataToBrasil($this->data_inauguracao),false);
		$this->campoMonetario( "valor", "Valor",$this->valor,10,16,false);
		$this->campoRadio("categoria", "Categoria", array("Obras", "Ações"), $this->categoria);
		$objBairo = new clsBairro();
		$listaBai['0'] = "Selecione";
		$listaBairro = $objBairo->lista(8507);
		if($listaBairro)
		{
			foreach ($listaBairro as $valores) 
			{
				$listaBai[$valores['idbai']] = $valores['nome']	;
				
			}
		}
		$this->campoLista("idbai", "Bairro", $listaBai, $this->idbai);
		//*
	/*	$obj_funcionario = new clsFuncionario($this->pessoa_logada);
		$detalhe_func = $obj_funcionario->detalhe();
		$setor_funcionario = $detalhe_func["ref_cod_setor_new"];		
		$obj = new clsSetor();
		$setor_pai = array_shift(array_reverse($obj->getNiveis($setor_funcionario)));*/
		//$isSecom = $setor_pai == 4327 ? true : false;
		//*
		/*if($isSecom)
			$this->campoCheck("destaque","Destaque",$this->destaque, "Marcar como destaque");
		else
			$this->campoOculto("destaque","0");
		*/						
	
	}

	function Novo() 
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		/*$obj_funcionario = new clsFuncionario($this->pessoa_logada);
		$detalhe_func = $obj_funcionario->detalhe();
		$setor_funcionario = $detalhe_func["ref_cod_setor_new"];
		//*
		$obj = new clsSetor();
		$setor_pai = array_shift(array_reverse($obj->getNiveis($setor_funcionario)));
		//		
		$obj_secretaria_responsavel = new clsPmiacoesSecretariaResponsavel($setor_funcionario);
		$obj_secretaria_responsavel_det = $obj_secretaria_responsavel->detalhe();
		*/
		//if($obj_secretaria_responsavel_det == false)
		if(!$this->permiteEditar())
			$pendente  = 0;
		else 
			$pendente = 1;

			
		$this->destaque = $this->destaque == "on" ? 1 : 0;
		$obj_acao_governo = new clsPmiacoesAcaoGoverno(null,null,$this->pessoa_logada,$this->nm_acao,$this->descricao,$this->data_inauguracao,str_replace(array(".",","),array("","."),$this->valor),$this->destaque,$pendente,1,null, $this->categoria, $this->idbai);

		if(! $cod_acao_governo = $obj_acao_governo->cadastra())
			return false;
		
		header("location: acoes_acao_det.php?cod_acao_governo={$cod_acao_governo}");

		return false;
	}

	function Editar() 
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		/*$obj_funcionario = new clsFuncionario($this->pessoa_logada);
		$detalhe_func = $obj_funcionario->detalhe();
		$setor_funcionario = $detalhe_func["ref_cod_setor_new"];
		$obj_secretaria_responsavel = new clsPmiacoesSecretariaResponsavel($setor_funcionario);
		$obj_secretaria_responsavel_det = $obj_secretaria_responsavel->detalhe();*/
		
		//if($obj_secretaria_responsavel_det == false)
		if(!$this->permiteEditar())
			$pendente  = 0;
		else 
			$pendente = 1;

		//$this->destaque = $this->destaque == "on" ? 1 : 0;
		$obj_acao_governo = new clsPmiacoesAcaoGoverno($this->cod_acao_governo,null,$this->pessoa_logada,$this->nm_acao,$this->descricao,dataToBanco($this->data_inauguracao),str_replace(array(".",","),array("","."),$this->valor),null,$pendente,null,null, $this->categoria, $this->idbai);
		if(!$obj_acao_governo->edita())
			return false;
	
		header("location: acoes_acao_det.php?cod_acao_governo={$this->cod_acao_governo}");
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
	/*	$obj_funcionario = new clsFuncionario($this->pessoa_logada);
		$detalhe_func = $obj_funcionario->detalhe();
		$setor_funcionario = $detalhe_func["ref_cod_setor_new"];
		
		$obj_secretaria_responsavel = new clsPmiacoesSecretariaResponsavel($setor_funcionario);
		$obj_secretaria_responsavel_det = $obj_secretaria_responsavel->detalhe();
		if($obj_secretaria_responsavel_det == false)		
			header("Location: acoes_acao_lst.php");
	*/	

		$obj_acao = new clsPmiacoesAcaoGoverno($this->cod_acao_governo);
		if(!$obj_acao->excluir())
			return false;
			
		header("Location: acoes_acao_lst.php");
		return true;
	}
	
	function permiteEditar()
	{
		$retorno = false;
	
		$obj_funcionario = new clsFuncionario($this->pessoa_logada);
		$detalhe_func = $obj_funcionario->detalhe();
		$setor_funcionario = $detalhe_func["ref_cod_setor_new"];
		
		//*
		$obj = new clsSetor();
		$setor_pai = array_shift(array_reverse($obj->getNiveis($setor_funcionario)));
		//*
		
		$obj_secretaria_responsavel = new clsPmiacoesSecretariaResponsavel($setor_pai);
		$obj_secretaria_responsavel_det = $obj_secretaria_responsavel->detalhe();

		$obj_acao = new clsPmiacoesAcaoGoverno($this->cod_acao_governo);
		$obj_acao_det = $obj_acao->detalhe();
		$status = $obj_acao_det["status_acao"];
		
		
		//**
			$func_cad = $obj_acao_det["ref_funcionario_cad"];	
			$obj_funcionario = new clsFuncionario($func_cad);
			$detalhe_func = $obj_funcionario->detalhe();
			$setor_cad = $detalhe_func["ref_cod_setor_new"];			
			$setor_cad = array_shift(array_reverse($obj->getNiveis($setor_cad)));
		//**
		
	//	$isSecom = $setor_pai == 4327 ? true : false;
		$retorno = ($obj_secretaria_responsavel_det != false )? true : false;	
		return $retorno;
	}
		
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();
?>
<script>
function chama_expansivel(pagina,campo)
{
	var numero = /([0-9])+$/.exec(campo)[0];
	var parametros = "";
	var and = "";
	for(var ct = 2 ; ct <arguments.length ; ct++){
		parametros += and + "campo" + (ct - 1) + "=" + arguments[ct] + numero ;
		and = "&";
	}
	 
	showExpansivel( 500,450, "<iframe name='miolo' id='miolo' frameborder='0' height='100%' width='500' marginheight='0' marginwidth='0' src='" + pagina + ".php?" + parametros + "'></iframe>");
}


function valida(){
	//var categoria = arguments[0];
	var acao = document.getElementById("tipoacao").value;
	if(acao.toLowerCase() == "novo"){
		var tt = "contCategorias";
		for(var ct1 = 0 ;ct1 < eval(tt) ;ct1++){
			var temp = ("" + "Categorias" + ct1 + "").toLowerCase();
			if((window.parent.document.getElementById(temp) && window.parent.document.getElementById(temp).value) || 
			   (window.parent.document.getElementById(temp) && window.parent.document.getElementById(temp).text)){
				achou = true;
			}
		}
		if(achou == false){
			alert('Preencha o campo ' + arguments[ct].replace("_"," ") + " corretamente!");
			return;
		}		
		
	}else{ //editar
		//var tt = "edit_categorias[4]";
		var hidden_elements = document.getElementsByTagName('input');
		var achou = false;
		for(var ct1 = 0 ;ct1 < hidden_elements.length ;ct1++){
		
			if(hidden_elements[ct1].name.indexOf('edit_categorias[') == 0){
				achou = true;
				break;
			}
		}
		
		if(achou == false){
			var tt = "contCategorias";
			for(var ct1 = 0 ;ct1 < eval(tt) ;ct1++){
				var temp = ("" + "Categorias" + ct1 + "").toLowerCase();
				if((window.parent.document.getElementById(temp) && window.parent.document.getElementById(temp).value) || 
				   (window.parent.document.getElementById(temp) && window.parent.document.getElementById(temp).text)){
					achou = true;
				}
			}
			if(achou == false){
				alert("Preencha o campo Categoria corretamente!");
				return false;
			}					
		}	
				
	}
	
	var tt = "contNovas_Fotos";
	for(var ct1 = 0 ;ct1 < eval(tt) ;ct1++){
		if(!check("fotos" + ct1))			  
		  return false;		
	}
	return true;


}

function check(campo) {
  var ext = document.getElementById(campo).value;
  if(ext){
	  ext = ext.substring(ext.length-3,ext.length);
	  ext = ext.toLowerCase();
	  if(ext != 'jpg' && ext != 'jpeg' && ext != 'gif' && ext != 'png' && ext != 'bmp') {
	    alert('Favor selecionar um arquivo de imagem');
	    var set_focus=  document.getElementById(campo);
	    set_focus.focus();
	    return false; 
	  }
  }
  return true; 
}



//onload = setFocus();
</script>
