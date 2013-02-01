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
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/otopic/otopicGeral.inc.php");
require_once ("include/clsListagem.inc.php");


class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Pauta - Cadastro de Grupos!" );
		$this->processoAp = "296";
	}
}

class indice extends clsCadastro
{
	//Grupo
	var $cod_grupos;
	var $nm_grupo;
	//Controle da lista de moderadores
	var $todos_moderadores;
	var $qtd_moderadores;
	var $id_moderador;
	var $id_moderador_deletar;
	//Pessoas(membros)
	var $listaPessoas;
	
	function Inicializar()
	{
		@session_start();
		$this->id_pessoa = $_SESSION['id_pessoa'];
		session_write_close();

		$retorno = "Novo";
		$this->cod_grupos = @$_GET['cod_grupos'];
		$this->nm_grupo = $_POST['nm_grupo'];
		$this->todos_moderadores = @$_POST['todos_moderadores'];
		
		if($this->cod_grupos)
		{
			$objGrupos = new clsGrupos($this->cod_grupos);
			$detGrupos = $objGrupos->detalhe();
			$this->nm_grupo = $detGrupos['nm_grupo'];
			
			$objGrupoPessoa = new clsGrupoPessoa();
			$this->listaPessoas = $objGrupoPessoa->lista(false, $this->cod_grupos);
			
			$this->fexcluir = true;
			
			$retorno = "Editar";
		}
		if(!@$_POST['todos_moderadores'] && $this->cod_grupos )
		{
			$objGrupoModerador = new clsGrupoModerador(false, $this->cod_grupos);
			$lista = $objGrupoModerador->lista(false, $this->cod_grupos);
			if($lista)
			{
				$this->qtd_moderadores = 0;
				foreach ($lista as $moderadores) {
					$this->todos_moderadores[] = $moderadores['ref_ref_cod_pessoa_fj'];
					$this->qtd_moderadores++;
				}
			}
		}

		if(!empty($_POST["todos_moderadores"]))
		{
			$this->todos_moderadores = unserialize(urldecode($_POST["todos_moderadores"]));
		}
		if(!empty($_POST["qtd_moderadores"]))
		{
			$this->qtd_moderadores = $_POST["qtd_moderadores"];
		}
		else 
		{
			$this->qtd_moderadores = 0;
		}
		if( $_POST["id_moderador"] != "" && empty($_POST["id_moderador_deletar"]))
		{
			$conitnua = "true";
			if(is_array($this->todos_moderadores))
				foreach($this->todos_moderadores as $moderador)
				{
					if($_POST["id_moderador"] == $moderador)
						$conitnua = "false";
				}
			if($conitnua == "true")
				{
					$this->qtd_moderadores += 1;
					$this->todos_moderadores[] =  $_POST["id_moderador"];
				}
		}
		if(!empty($_POST["id_moderador_deletar"]))
		{
			foreach($this->todos_moderadores as $i=>$id_moderador)
			{
				if($id_moderador == $_POST["id_moderador_deletar"])
				{
					unset($this->todos_moderadores[$i] );
					$this->qtd_moderadores -= 1;
				}
			}
			$this->id_moderador_deletar="";
		}
		return $retorno;
	}

	function Gerar()
	{
		//Moderadores vinculados
		$this->campoOculto( "id_moderador_deletar", $this->id_moderador_deletar );
		$this->campoOculto( "qtd_moderadores", $this->qtd_moderadores);
		$this->campoOculto( "nm_grupo", $this->nm_grupo);
		$this->campoOculto( "cod_grupos", $this->cod_grupos);
		$this->campoOculto( "todos_moderadores", serialize($this->todos_moderadores));
		$this->campoOculto( "id_moderador", $this->id_moderador);
		
		$this->campoTexto( "nm_grupo", "Nome do Grupo",  $this->nm_grupo, "50", "100", true );
		if(is_array($this->todos_moderadores))
		foreach($this->todos_moderadores as $id=>$moderador)
		{
			
			$objPessoa = new clsPessoaFj($moderador);
			$detPessoa = $objPessoa->detalhe();
			$nome = $detPessoa['nome'];
			
			$this->campoTextoInv( "id_moderador_$id", "Moderador(es)", $nome,  "30", "30", true,false,false, "","<a href='#' onclick=\"javascript:excluirSumit({$moderador},'id_moderador_deletar') \">Clique aqui para Excluir</a>");
		}
		//$this->campoProcurarAdicionar("id_moderador_", "Incluir Moderador", "", 10, 5, "openurl('pesquisa_funcionario_otopic.php?campo=id_moderador')", "Procurar","insereSubmit()","");
		$parametros = new clsParametrosPesquisas();
		$parametros->setSubmit( 1 );
		$parametros->adicionaCampoSelect( "id_moderador", "ref_cod_pessoa_fj", "nome" );
		$this->campoListaPesq( "id_moderador", "Incluir Moderador", array( "Para procurar, clique na lupa ao lado" ), $this->ref_cod_pessoa_fj, "pesquisa_funcionario_lst.php", "", false, "", "", null, null, "", false, $parametros->serializaCampos() );
		//$this->campoLista( "id_moderador", "Incluir Moderador", array("Para procurar, clique na lupa ao lado"), $this->ref_cod_pessoa_fj, "", false, "", "<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'pesquisa_funcionario_lst.php?campos=$serializedcampos\'></iframe>' );\">", false, true );
		
		$this->url_cancelar = ($retorno == "Editar") ? "otopic_grupos_det.php?cod_grupo=$this->cod_grupos" : "otopic_grupos_lst.php";
		$this->nome_url_cancelar = "Cancelar";
	}
	
	
	function Novo() 
	{
		@session_start();
		$this->id_pessoa = @$_SESSION['id_pessoa'];
		session_write_close();
		$this->todos_moderadores = unserialize(urldecode($this->todos_moderadores));
		if(!empty($this->todos_moderadores))
		{
			$objGrupos = new clsGrupos(false, $this->id_pessoa, false, $this->nm_grupo, 1);
			$this->cod_grupos = $objGrupos->cadastra();
			if($this->cod_grupos)
			{
			   foreach ($this->todos_moderadores AS $id=>$moderador)
			   {
			      $objGrupoModerador = new clsGrupoModerador($moderador, $this->cod_grupos, $this->id_pessoa, false, 1);
			      $objGrupoModerador->cadastra();
			      $obj = new clsGrupoPessoa($moderador,$this->cod_grupos,$this->id_pessoa,false,$this->cod_grupos,false,2);
			      $obj->cadastra();
			   }
			   header("Location: otopic_grupos_lst.php");
			}
		}
		$this->mensagem = "Favor Selecionar ao menos um Moderador.";
		return false;		
		
	}

	function Editar() 
	{
		@session_start();
		$this->id_pessoa = @$_SESSION['id_pessoa'];
		session_write_close();
		
		$this->todos_moderadores = unserialize(urldecode($this->todos_moderadores));
		if(!empty($this->todos_moderadores))
		{
			$objGrupoModerador = new clsGrupoModerador(false, $this->cod_grupos, false, $this->id_pessoa, 2);
			$objGrupoModerador->excluiTodos();
		
			$objGrupos = new clsGrupos($this->cod_grupos, $this->id_pessoa, false, $this->nm_grupo, 1);
			
			if($objGrupos->edita())
			{
			   foreach ($this->todos_moderadores AS $id=>$moderador)
			   {
        		  $objGrupoModeradorVerificacao = new clsGrupoModerador($moderador, $this->cod_grupos);
			   	
			   	  if($objGrupoModeradorVerificacao->detalhe()) 
			   	  {	
			   	      $objGrupoPessoa = new clsGrupoPessoa($moderador, $this->cod_grupos, false, $this->id_pessoa );
			   	  	  if($objGrupoPessoa->detalhe())
			   	  	  {
			   	  	     $objGrupoPessoa->exclui();
			   	  	  }else {
	   	  	  		      $obj = new clsGrupoPessoa($moderador,$this->cod_grupos,$this->id_pessoa,false,$this->cod_grupos,false,2);
					      $obj->cadastra();
			   	  	  }
			   	  	  $objGrupoModerador = new clsGrupoModerador($moderador, $this->cod_grupos, $this->id_pessoa, false, 1);
				      $objGrupoModerador->edita();
			   	  }
			   	  else 
			   	  {
			   	  	$objGrupoPessoa = new clsGrupoPessoa($moderador, $this->cod_grupos, false, $this->id_pessoa );
			   	  	  if($objGrupoPessoa->detalhe())
			   	  	  {
			   	  	     $objGrupoPessoa->exclui();
			   	  	  }else {
	   	  	  		      $obj = new clsGrupoPessoa($moderador,$this->cod_grupos,$this->id_pessoa,false,$this->cod_grupos,false,2);
					      $obj->cadastra();
			   	  	  }
			   	  	  $objGrupoModerador = new clsGrupoModerador($moderador, $this->cod_grupos, $this->id_pessoa, false, 1);
				      $objGrupoModerador->cadastra();
			   	  }
			   }
			   header("Location: otopic_grupos_lst.php");
			}
		}
		$this->mensagem = "Favor Selecionar ao menos um Moderador.";
		return false;
	}

	function Excluir()
	{
		@session_start();
		$this->id_pessoa = @$_SESSION['id_pessoa'];
		session_write_close();
		
		$objGrupoPessoa = new clsGrupoPessoa(false, $this->cod_grupos, false, $this->id_pessoa);
		$objGrupoPessoa->exclui_todos();
		
		$objGrupoModerador = new clsGrupoModerador(false, $this->cod_grupos, false, $this->id_pessoa);
		$objGrupoModerador->excluiTodos();
		
		$objGrupos = new clsGrupos($this->cod_grupos, false, $this->id_pessoa, $this->nm_grupo,2);
		if($objGrupos->exclui())
		{
			 header("Location: otopic_grupos_lst.php");
		}
		return false;

	}
}
$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
