<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de ItajaÌ								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software P˙blico Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de ItajaÌ			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  È  software livre, vocÍ pode redistribuÌ-lo e/ou	 *
	*	modific·-lo sob os termos da LicenÁa P˙blica Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a vers„o 2 da	 *
	*	LicenÁa   como  (a  seu  critÈrio)  qualquer  vers„o  mais  nova.	 *
	*																		 *
	*	Este programa  È distribuÌdo na expectativa de ser ˙til, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia implÌcita de COMERCIALI-	 *
	*	ZA«√O  ou  de ADEQUA«√O A QUALQUER PROP”SITO EM PARTICULAR. Con-	 *
	*	sulte  a  LicenÁa  P˙blica  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	VocÍ  deve  ter  recebido uma cÛpia da LicenÁa P˙blica Geral GNU	 *
	*	junto  com  este  programa. Se n„o, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/otopic/otopicGeral.inc.php");


class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Pauta - Inserir TÛpicos" );
		$this->processoAp = "294";
	}
}

class indice extends clsCadastro
{
	var $cod_topico;
	var $cod_grupo;
	var $assunto;
	var $id_pessoa;

	
	function Inicializar()
	{
		@session_start();
		$this->id_pessoa = $_SESSION['id_pessoa'];
		session_write_close();
		$retorno = "Novo";

		$this->cod_grupo = $_GET['cod_grupo'];
		$this->cod_topico = $_GET['cod_topico'];
		
		/* 
			Verifica se o UsuÔøΩrio atual estÔøΩ cadastrado no grupo,
			caso nao esteja, redireciona para entrada
		*/
		$obj = new clsGrupoPessoa($this->id_pessoa ,$this->cod_grupo);
		$detalhe_pessoa = $obj->detalhe();
		$obj = new clsGrupoModerador($this->id_pessoa ,$this->cod_grupo);
		$detalhe_moderador = $obj->detalhe();
		
		if ($detalhe_moderador && $detalhe_pessoa['ativo']!= 1) 
		{
			if( $detalhe_moderador['ativo'] != 1)
			{
				header("Location: otopic_meus_grupos_det2.php?cod_grupo=$cod_grupo");
				die();
			}
		}elseif($detalhe_pessoa['ativo']!= 1)
		{
			header("Location: otopic_meus_grupos_det2.php?cod_grupo=$cod_grupo");
			die();
		}
		
		if($this->cod_topico)
		{
			$obj = new clsTopico($this->cod_topico);
			$detalhe = $obj->detalhe();
			if($detalhe)
			{
				$obj_moderador = new clsGrupoModerador($this->id_pessoa,$this->cod_grupo);
				$detalhe_moderador = $obj_moderador->detalhe();
				if($detalhe['ref_idpes_cad'] != $this->id_pessoa && !$detalhe_moderador)
				{
					header("Location: otopic_meus_grupos_det2.php?cod_grupo=$this->cod_grupo");
					die();
				}
				$this->assunto = $detalhe['assunto'];
				$retorno = "Editar";
				$this->fexcluir = true;
	
			}else 
			{					
				header("Location: otopic_meus_grupos_det2.php?cod_grupo=$this->cod_grupo");
				die();
			}
		}
		
		$this->url_cancelar =  "otopic_meus_grupos_det2.php?cod_grupo=$this->cod_grupo";
		$this->nome_url_cancelar = "Cancelar";


		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto("id_pessoa",$this->id_pessoa);
		$this->campoOculto("cod_grupo",$this->cod_grupo);
		$this->campoOculto("cod_topico",$this->cod_topico);
		$this->campoTexto("assunto","Asssunto",$this->assunto,50,255,true);
		//campoRadio( $nome, $campo, $valor, $default, $acao = "", $descricao="" )
		$objReuniao = new clsReuniao();
		$listaReuniao = $objReuniao->lista(false, $this->cod_grupo);
		/*
		echo "<pre>";
		print_r($listaReuniao);
		die();
		*/
		if($listaReuniao)
		{
			foreach ($listaReuniao AS $reuniao)
			{
				if($reuniao['data_inicio_real'] && !$reuniao['data_fim_real'])
				{
					$listaReuniaoAndamento[$reuniao["cod_reuniao"]] = $reuniao["descricao"]; 
				}
			}
		}
		/*
		echo "<pre>";
		print_r($listaReuniaoAndamento);
		die();
		*/
		if($listaReuniaoAndamento)
		{
			$this->campoRotulo("rt1", "<b>Selecione a reuni√£oo na qual o novo t√≥pico sera criado</b>", "");
			$this->campoRadio( "radio", "ReuniÔøΩes em andamento", $listaReuniaoAndamento, "" );
		}
		
	}
	
	
	function Novo() 
	{
		$obj = new clsTopico(false,$this->id_pessoa,$this->cod_grupo,false,false,$this->assunto);
		$cod_topico = $obj->cadastra();
		if($cod_topico)
		{
			$obj_grupo = new clsGrupoModerador();
			$lista = $obj_grupo->lista(false,$this->cod_grupo);
			if($lista)
			{
				$grupo_pessoas = "";
				foreach ($lista as $moderador) {
					$obj = $obj = new clsPessoaFisica($moderador['ref_ref_cod_pessoa_fj']);
					$detalhe = $obj->detalhe();
					$grupo_pessoas[] = $detalhe['email'];
				}
			}
			$obj_pessoa_criadora = $obj = new clsPessoaFisica($this->id_pessoa);
			$detalhe = $obj->detalhe();
			$nome_criador = $detalhe['nome'];
			$obj = new clsGrupos($this->cod_grupo);
			$detalhe = $obj->detalhe();
			$nome_grupo = $detalhe['nm_grupo'];
			$corpo_email = "<br><table summary=\"\" border=0 cellspacing=3 cellpadding=3><tr><td colspan='2'  style=\"border-bottom: 2px solid #024492\"><span class='titulo'><br><br><b>Novo TÔøΩpico Sugerido</b></span><br>\n<br>\n";
			$corpo_email .= "<tr><td><b><br>Grupo: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>$nome_grupo</td></tr><br><tr><td><b><br>Membro: &nbsp;&nbsp;</td><td>$nome_criador</td></tr><br><tr><td><b><br>Assunto: &nbsp;&nbsp;</td><td>$this->assunto</td></tr></table>";
			$cabecalho = "From: PMI(itajai.com.br)\nReply-To: itajai.com.br";
			$objEmail = new clsEmail( $grupo_pessoas, "[OpenTopic] - Novo TÔøΩpico", $corpo_email,"email_mailling_topic" );
			$objEmail->envia();
			
			$cod_reuniao = $this->radio;
			if($cod_reuniao)
			{
				$objTopicoReuniao = new clsTopicoReuniao($cod_topico, $cod_reuniao);
				$objTopicoReuniao->cadastra();
			}
			
			if($_SESSION['pagina'])
			{
				header("Location: $_SESSION[pagina]");
				die();
			}
			else 
			{
				header("Location: otopic_meus_grupos_det2.php?cod_grupo=$this->cod_grupo");
				die();
			}
		}
		return false;
	}

	function Editar() 
	{
		$obj = new clsTopico($this->cod_topico,$this->id_pessoa,$this->cod_grupo,false,false,$this->assunto);
		if($obj->edita())
		{
			$cod_reuniao = $this->radio;
			if($cod_reuniao)
			{
				$objTopicoReuniao = new clsTopicoReuniao($this->cod_topico, $cod_reuniao);
				$objTopicoReuniao->cadastra();
			}
			header("Location: otopic_meus_grupos_det2.php?cod_grupo=$this->cod_grupo");
			die();
		}		

	}

	function Excluir()
	{
		$obj = new clsTopico($this->cod_topico,false,false,$this->id_pessoa,$this->cod_grupo,$this->assunto);
		if($obj->exclui())
		{
			header("Location: otopic_meus_grupos_det2.php?cod_grupo=$this->cod_grupo");
			die();
		}		
		
	}

}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
