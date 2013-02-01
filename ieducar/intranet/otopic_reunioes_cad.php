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
require_once ("include/otopic/otopicGeral.inc.php");
require_once ("include/clsEmail.inc.php");



class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Pauta - Cadastro de Reunião" );
		$this->processoAp = "294";
	}
}

class indice extends clsCadastro
{
	var $cod_reuniao;
	var $cod_grupo;
	var $descricao;
	var $id_pessoa;
	var $email_enviado;
	var $data;
	var $data_final;
	var $hora_inicial;
	var $hora_final;

	
	function Inicializar()
	{
		@session_start();
		$this->id_pessoa = $_SESSION['id_pessoa'];
		session_write_close();
		$retorno = "Novo";

		$this->cod_grupo = $_GET['cod_grupo'];
		$this->cod_reuniao = $_GET['cod_reuniao'];
		
		if($this->cod_reuniao)
		{
			$obj = new clsReuniao($this->cod_reuniao);
			$detalhe = $obj->detalhe();
			if($detalhe)
			{
				$obj_moderador = new clsGrupoModerador($this->id_pessoa,$this->cod_grupo);
				$detalhe_moderador = $obj_moderador->detalhe();
				if(!$detalhe_moderador)
				{
					header("Location: otopic_meus_grupos_det2.php?cod_grupo=$this->cod_grupo");
				}
				$this->descricao = $detalhe['descricao'];
				$this->data = date("d/m/Y",strtotime(substr($detalhe['data_inicio_marcado'],0,19)));
				$this->hora_inicial = date("H:i",strtotime(substr($detalhe['data_inicio_marcado'],0,19)));
				$this->data_final = date("d/m/Y",strtotime(substr($detalhe['data_fim_marcado'],0,19)));
				$this->hora_final = date("H:i",strtotime(substr($detalhe['data_fim_marcado'],0,19)));
				$this->email_enviado = $detalhe['email_enviado'];
				$retorno = "Editar";
				$this->fexcluir = true;
	
			}else 
			{					
				header("Location: otopic_meus_grupos_det2.php?cod_grupo=$this->cod_grupo");
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
		$this->campoOculto("cod_reuniao",$this->cod_reuniao);
		$this->campoMemo("descricao","Descrição",$this->descricao,50,5,true);
		$obj = new clsReuniao();
		/*  Pega lista de Reunioes Finalizadas, Verifica e mostra os Topicos Finalizados 
			que nao foram finalizados nessa reuniao 
		*/
		$lista = $obj->lista(false,$cod_grupo,false,false,false,false,false,false,true);
		if($lista)
		{
			foreach ($lista as $reuniao) {
				$obj = new clsTopicoReuniao();
				$lista = $obj->lista(false,false,false,false,false,false,false,$reuniao['cod_reuniao']);
				if($lista)
				{
					foreach ($lista as $topicos) {
						if($topicos['finalizado'])
						{
							$topico_comprometidos[] = $topicos['ref_cod_topico'];
						}
					}
				}
			}
		}
		/*  Pega lista de Reunioes não Finalizadas, Verifica que estão nessa reuniao e marca como 
			comprometido
		*/	
		$obj = new clsReuniao();
		$lista = $obj->lista(false,$cod_grupo,false,false,false,false,false,true);
		if($lista)
		{
			foreach ($lista as $reuniao) {
				$obj = new clsTopicoReuniao();
				$lista = $obj->lista(false,false,false,false,false,false,false,$reuniao['cod_reuniao']);
				if($lista)
				{
					foreach ($lista as $topicos) {
							$topico_comprometidos[] = $topicos['ref_cod_topico'];
					}
				}
			}
		}
		$this->campoData("data","Data Inicial",$this->data,true);
		$this->campoData("data_final","Data Final",$this->data_final,true);
		$this->campoHora("hora_inicial","Hora de Início",$this->hora_inicial,true);		
		$this->campoHora("hora_final","Hora de Fim",$this->hora_final,true);		
		if(!$this->email_enviado)
		{
			$this->campoCheck("email_enviado","Notificar por e-mail",$this->email_enviado,"");
		}

		$this->campoCheck("marcar","Marcar Todos",0,"",false,"javascript: marcar_todos(); ");
		$this->campoCheck("desmarcar","Desmarcar Todos",0,"",false,"javascript: desmarcar_todos(); ");

		// Array de valores parar o botao marcar todos
		$array_marcar_todos = false;
		
		
		$obj = new clsTopico();
		$lista = $obj->lista(false,$this->cod_grupo,false,false,false,false,false,1,false,false,false,$topico_comprometidos);

		if($lista)
		{
			foreach ($lista as $topicos) 
			{
				$obj = new clsTopicoReuniao($topicos['cod_topico']);
				$checked = $obj->detalhe() ? "Pendente" : "";
				$this->campoCheck("top_{$topicos['cod_topico']}","Tópicos",$checked,"{$topicos['assunto']}",false,"javascript: desmarcar_marcar('top_{$topicos['cod_topico']}'); ");
				$array_marcar_todos[] = $topicos['cod_topico'];
			}
		}	

		if($this->cod_reuniao)
		{
			$obj = new clsTopicoReuniao();
			$lista = $obj->lista(false,false,false,false,false,false,false,$this->cod_reuniao);
			if($lista)
			{
				foreach ($lista as $topico_reuniao) {
					$obj = new clsTopico($topico_reuniao['ref_cod_topico']);
					$detalhe = $obj->detalhe();
					$this->campoCheck("top_{$topico_reuniao['ref_cod_topico']}","Tópicos",1,"{$detalhe['assunto']}",false,"javascript: desmarcar_marcar('top_{$topicos['cod_topico']}'); ");
					$array_marcar_todos[] =$topico_reuniao['ref_cod_topico'];
				}
			}
		}
		if($array_marcar_todos)
		{
			echo "<script> marcar = [".implode(",",$array_marcar_todos)."]</script>";	
		}else 
		{
			echo "<script> marcar = [];</script>";	
		}
	}
	
	
	function Novo() 
	{
		$data = $this->data;
		$data2 = $this->data_final;
		$this->data = explode("/",$this->data);
		$data_inicial 	= "{$this->data[2]}/{$this->data[1]}/{$this->data[0]} $this->hora_inicial";
		$this->data_final =  explode("/",$this->data_final);
		$data_final 	= "{$this->data_final[2]}/{$this->data_final[1]}/{$this->data_final[0]} $this->hora_final";

		$this->email_enviado = $this->email_enviado ? 1 : "";
		
		$obj = new clsReuniao(false,$this->id_pessoa,$this->cod_grupo,$this->descricao,$this->email_enviado,$data_inicial,$data_final);
		$cod_reuniao = $obj->cadastra();

		foreach ($_POST as $id => $campo) {
			if(substr($id,0,4) == "top_")
			{
				$cod_topico = substr($id,4);
				$obj = new clsTopicoReuniao($cod_topico,$cod_reuniao);
				$lista_topicos[] = $cod_topico;
				$obj->cadastra();
			}
		}
		
		if($this->email_enviado)
		{
			// Busca Email dos Moderadores e Membros do Grupo
			$obj = new clsGrupoPessoa();
			$lista = $obj->lista(false,$this->cod_grupo);
			$grupo_pessoas = "";

			if($lista)
			{
				foreach ($lista as $pessoa) {
					$obj = new clsPessoaFisica($pessoa['ref_idpes']);
					$detalhe = $obj->detalhe();
					if($detalhe['email'])
					{
						$grupo_pessoas[] = $detalhe['email'];
					}
				}
			}
			
			$obj = new clsGrupoModerador();
			$lista = $obj->lista(false,$this->cod_grupo);
			if($lista)
			{
				foreach ($lista as $pessoa) {
					$obj = new clsPessoaFisica($pessoa['ref_ref_cod_pessoa_fj']);
					$detalhe = $obj->detalhe();
					if($detalhe['email'])
					{
						$grupo_pessoas[] = $detalhe['email'];
					}
				}
			}
			
			$obj = new clsReuniao($this->cod_reuniao);
			$detalhe_reuniao = $obj->detalhe(); 
			
			$corpo_email .= "<br><table summary=\"\" border=0 cellspacing=3 cellpadding=3><tr><td colspan='2'  style=\"border-bottom: 2px solid #024492\"><span class='titulo'><b>Descrição da Reunião</b>.</span><br>\n<br>\n";
			$corpo_email .= "<tr><td><b>{$detalhe_reuniao['descricao']}</b></td><td width=250><br><br></td></tr>"; 

			if($data == $data2)
			{
				$corpo_email .= "<tr><td><b><br>Data: $data das $this->hora_inicial as $this->hora_final</b></td><td width=250><br></td></tr>";
			}else 
			{
				$corpo_email .= "<tr><td><b><br>Data de Inicio: $data $this->hora_inicial - Data de Fim: $data2 $this->hora_final</b></td><td width=250><br></td></tr>";
			}
			
			$corpo_email .= "<tr><td><b><span class='titulo'><br>Tópicos</span></b></td><td width=250></td></tr>";
			foreach ($lista_topicos as $topico) {
				$obj = new clsTopico($topico);
				$detalhe = $obj->detalhe();
				$corpo_email .= "<tr><td><br><br>{$detalhe['assunto']}</td></tr></table>";
			}
			
			
			$cabecalho = "From: PMI(itajai.com.br)\nReply-To: itajai.com.br";
			$objEmail = new clsEmail( $grupo_pessoas, "[OpenTopic] - Nova Reunião", $corpo_email,"email_mailling_topic" );
			$objEmail->envia();
		}
		header("Location: otopic_meus_grupos_det2.php?cod_grupo=$this->cod_grupo");
		return false;
	}

	function Editar() 
	{
		$obj = new clsTopicoReuniao();
		$obj->exclui_todos($this->cod_reuniao);

		$data = $this->data;
		$data2 = $this->data_final;
		$this->data = explode("/",$this->data);
		$data_inicial 	= "{$this->data[2]}/{$this->data[1]}/{$this->data[0]} $this->hora_inicial";
		$this->data_final =  explode("/",$this->data_final);
		$data_final 	= "{$this->data_final[2]}/{$this->data_final[1]}/{$this->data_final[0]} $this->hora_final";

		$this->email_enviado = $this->email_enviado ? 1 : "";
		$obj = new clsReuniao($this->cod_reuniao,$this->id_pessoa,$this->cod_grupo,$this->descricao,$this->email_enviado,$data_inicial,$data_final);
		$obj->edita();
		$lista_topicos = "";
		foreach ($_POST as $id => $campo) {
			if(substr($id,0,4) == "top_")
			{
				$cod_topico = substr($id,4);
				$lista_topicos[] = $cod_topico;
				$obj = new clsTopicoReuniao($cod_topico,$this->cod_reuniao);
				$obj->cadastra();
			}
		}		
		
		
		if($this->email_enviado)
		{	
			// Busca Email dos Moderadores e Membros do Grupo
			$obj = new clsGrupoPessoa();
			$lista = $obj->lista(false,$this->cod_grupo);
	
			if($lista)
			{
				$grupo_pessoas = "";
				foreach ($lista as $pessoa) {
					$obj = new clsPessoaFisica($pessoa['ref_idpes']);
					$detalhe = $obj->detalhe();
					if($detalhe['email'])
					{
						$grupo_pessoas[] = $detalhe['email'];
					}
				}
			}
			
			$obj = new clsGrupoModerador();
			$lista = $obj->lista(false,$this->cod_grupo);
			if($lista)
			{
				$grupo_pessoas = "";
				foreach ($lista as $pessoa) {
					$obj = new clsPessoaFisica($pessoa['ref_ref_cod_pessoa_fj']);
					$detalhe = $obj->detalhe();
					if($detalhe['email'])
					{
						$grupo_pessoas[] = $detalhe['email'];
					}
				}
			}
	
			$obj = new clsReuniao($this->cod_reuniao);
			$detalhe_reuniao = $obj->detalhe(); 
			
			$corpo_email .= "<br><table summary=\"\" border=0 cellspacing=3 cellpadding=3><tr><td colspan='2'  style=\"border-bottom: 2px solid #024492\"><span class='titulo'><b>Descrição da Reunião</b>.</span><br>\n<br>\n";
			$corpo_email .= "<tr><td><b>{$detalhe_reuniao['descricao']}</b></td><td width=250><br><br></td></tr>"; 

			if($data == $data2)
			{
				$corpo_email .= "<tr><td><b><br>Data: $data das $this->hora_inicial as $this->hora_final</b></td><td width=250><br></td></tr>";
			}else 
			{
				$corpo_email .= "<tr><td><b><br>Data de Inicio: $data $this->hora_inicial - Data de Fim: $data2 $this->hora_final</b></td><td width=250><br></td></tr>";
			}

			$corpo_email .= "<tr><td><b><span class='titulo'><br>Tópicos</span></b></td><td width=250></td></tr>";

			foreach ($lista_topicos as $topico) {
				$obj = new clsTopico($topico);
				$detalhe = $obj->detalhe();
					$corpo_email .= "<tr><td><br><br>{$detalhe['assunto']}</td></tr></table>";
			}
			$cabecalho = "From: PMI(itajai.com.br)\nReply-To: itajai.com.br";
			$objEmail = new clsEmail( $grupo_pessoas, "[OpenTopic] - Nova Reunião", $corpo_email,"email_mailling_topic" );
			$objEmail->envia();
		}
		header("Location: otopic_meus_grupos_det2.php?cod_grupo=$this->cod_grupo");
		return false;
	}

	function Excluir()
	{
		$obj = new clsTopicoReuniao();
		$obj->exclui_todos($this->cod_reuniao);
		$obj = new clsReuniao($this->cod_reuniao);
		if($obj->exclui())
		{
			header("Location: otopic_meus_grupos_det2.php?cod_grupo=$this->cod_grupo");
		}		
		
	}

}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
