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
require_once ("include/relatorio.inc.php");
require_once ("include/clsCadastro.inc.php");

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Cadastro de Setores" );
		$this->processoAp = "375";
	}
}

class indice extends clsCadastro
{
	var $cod_setor;
	var $nm_setor;
	var $sgl_setor;
	var $cod_pessoa;
	var $ativo;
	var $no_paco;
	var $end;
	var $tipo;
	var $secretario;
	
	function Inicializar()
	{
		@session_start();
		$this->cod_pessoa = $_SESSION['id_pessoa'];
		session_write_close();
		
		$this->nivel = 0;
		$retorno = "Novo";
		
		if( $_GET['cod_setor'] )
		{
			$this->cod_setor = $_GET['cod_setor'];
			
			$objSetor = new clsSetor($this->cod_setor);
			$detSetor = $objSetor->detalhe();
			
			$this->nm_setor = $detSetor["nm_setor"];
			$this->sgl_setor = $detSetor["sgl_setor"];
			$this->secretario = $detSetor['ref_idpes_resp'];
			
			$retorno = "Editar";
			$this->fexcluir = true;
		}
		
		$this->url_cancelar = "oprot_setor_lst.php";
		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto( "cod_setor", $_GET["cod_setor"] );
		$this->campoOculto( "cod_pessoa", $this->cod_pessoa );
		
		$nivelAtual = 0;
		$objSetor = new clsSetor();
		$strSetorAtual = "";

		if( isset( $_GET["setor_atual"] ) && $_GET["setor_atual"] )
		{
			$codPai = null;
			$strSetorAtual = "setor_atual={$_GET["setor_atual"]}&";
			$niveis = $objSetor->getNiveis( $_GET["setor_atual"] );
			$nivelAtual = count( $niveis );
			for( $i = 0; $i < count( $niveis ); $i++ )
			{
				$listaSetores = $objSetor->lista( $codPai, null, null, null, null, null, null, null, null, null, $i );
				$nomeVar = "setor_$i";
				
				$setores = array( "" => "Selecione" );
				foreach ( $listaSetores AS $setor )
				{
					$setores[$setor["cod_setor"]] = $setor["nm_setor"];
				}
				$this->campoLista( $nomeVar, "Setor nivel " . ( $i + 1 ), $setores, $niveis[$i], "if( this.value ) { document.location.href='oprot_setor_cad.php?cod_setor=$this->cod_setor&setor_atual=' + this.value } else { document.location.href='oprot_setor_cad.php?cod_setor=$this->cod_setor&setor_atual={$codPai}' }" );
				$codPai = $niveis[$i];
			}
		}
		
		if( isset( $_GET["selecionar"] ) )
		{
			$codPai = ( isset( $_GET["setor_atual"] ) ) ? $_GET["setor_atual"]: null;
			$listaSetores = $objSetor->lista( $codPai, null, null, null, null, null, null, null, null, null, $nivelAtual );
			if( is_array( $listaSetores ) && count( $listaSetores ) )
			{
				$setores = array( "" => "Selecione" );
				foreach ( $listaSetores AS $setor )
				{
					$setores[$setor["cod_setor"]] = $setor["nm_setor"];
				}
				$nomeVar = "setor_$nivelAtual";
				$this->campoLista( $nomeVar, "Setor nivel " . ( $i + 1 ), $setores, false, "if( this.value ) { document.location.href='oprot_setor_cad.php?cod_setor=$this->cod_setor&setor_atual=' + this.value }" );
			}
			else 
			{
				$this->campoRotulo( "aviso", "Alerta", "<a href=\"oprot_setor_cad.php?cod_setor=$this->cod_setor&{$strSetorAtual}\">Nenhum setor neste nivel</a>" );
			}
		}
		else 
		{
			if( $nivelAtual < 5 )
			{
				if( $nivelAtual < 4 )
				{
					$this->campoRotulo( "adicionar", "Selecionar", "<a href=\"oprot_setor_cad.php?cod_setor=$this->cod_setor&{$strSetorAtual}selecionar=1\">Selecionar um setor já cadastrado</a>");
				}
				$this->campoTexto( "nm_setor", "Nome do Setor", $this->nm_setor, 30, 255, true );
				$this->campoTexto( "sgl_setor", "Sigla do Setor", $this->sgl_setor, 15, 15, true );
				$this->campoCheck("no_paco", "No Paço", 0);
				$this->campoMemo("end", "Endereço", "", 55, 5);
				$lista = array();
				$lista = array(0=>"Selecione", "s"=>"Secretaria", "a"=>"Altarquia", "f"=>"Fundação");
				$this->campoLista("tipo", "Tipo", $lista, $this->tipo);
				$lista = array();
				$lista[0] = "Selecione";
				
				$parametros = new clsParametrosPesquisas();
				$parametros->setSubmit( 0 );
				$parametros->adicionaCampoSelect( "secretario", "idpes", "nome" );
				
				$sec = array( "Para procurar, clique na lupa ao lado" );
				if($this->secretario)
				{
					$cls_pessoa = new clsPessoa_($this->secretario);
					$detalhe_pessoa = $cls_pessoa->detalhe();
					$sec = array( "Para procurar, clique na lupa ao lado", $this->secretario => $detalhe_pessoa['nome'] );
				}
				
				$this->campoListaPesq( "secretario", "Secretário", $sec, $this->secretario, "pesquisa_funcionario_lst.php", "", false, "", "", null, null, "", false, $parametros->serializaCampos() );
		
	
			
				
				//$this->campoListaPesq("secretario", "Secretario Responsável", $lista, $this->secretario, "pesquisa_pessoa.php");
				
				if($this->cod_setor)
				{				
					$obj = new clsSetor($this->cod_setor);
					$det = $obj->detalhe();
					$status = $det["ativo"] == 1 ? 1 : 0;
					$ativo = $this->ativo ? $this->ativo : $status;
					$this->campoCheck("ativo", "Ativo", $ativo);
				}
			}
		}
		
	}

	function Novo() 
	{
		$this->no_paco = $this->no_paco ? 1 : 0;
		$this->end = $this->end ? $this->end : null;
		$this->tipo = $this->tipo ? $this->tipo : null;
		$this->secretario = $this->secretario ? $this->secretario : null;
		
		$ref_cod_setor = $_GET["setor_atual"] ? $_GET["setor_atual"] : null;
		
		$obj_setor = new clsSetor(null, $ref_cod_setor, null, $this->cod_pessoa, $this->nm_setor, $this->sgl_setor, null, null, 1, null, $this->no_paco, $this->end, $this->tipo, $this->secretario );
		$cod_setor = $obj_setor->cadastra();
		
		if(isset($cod_setor))
		{
			header("Location: oprot_setor_det.php?cod_setor=$cod_setor");
		}
		
		return false;
	}

	function Editar() 
	{
		$ref_cod_setor = $_GET["setor_atual"] ? $_GET["setor_atual"] : null;

		$ativo = $this->ativo ? 1 : 2;
		
		$obj_setor = new clsSetor($this->cod_setor, $ref_cod_setor, null, $this->cod_pessoa, $this->nm_setor, $this->sgl_setor, null, null, $ativo, null,null,null,null,$this->secretario);
		
		if($obj_setor->edita())
		{
			header("Location: oprot_setor_det.php?cod_setor=$this->cod_setor");
		}
		
		return false;
	}

	function Excluir()
	{
		$obj_setor = new clsSetor($this->cod_setor, null, $this->cod_pessoa);
		if($obj_setor->exclui())
		{
			header("Location: oprot_setor_lst.php");
			die();
			return true;
		}
		
		return false;
	}

}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();
?>