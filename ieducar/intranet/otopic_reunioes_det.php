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
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/otopic/otopicGeral.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/time.inc.php");
require_once ("include/relatorio.inc.php");

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Pauta - Detalhe da Reunião" );
		$this->processoAp = "294";
	}
}

class indice extends clsDetalhe
{
	function Gerar()
	{
		$this->titulo = "Detalhe da Reunião";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet", false);
		
		@session_start();
		$id_visualiza = $_SESSION['id_pessoa'];
		@session_write_close();
	
		$cod_grupo = $_GET['cod_grupo'];
		$cod_reuniao = $_GET['cod_reuniao'];
		$obj = new clsReuniao($cod_reuniao);
		$detalhe = $obj->detalhe();
		if( !$detalhe || $detalhe['ref_grupos_moderador']!= $cod_grupo)
		{
			header("Location: otopic_meus_grupos_det2.php?cod_grupo=$cod_grupo");
			die();
		}
		
		/* 
			Verifica se o Usuário atual está cadastrado no grupo,
			caso nao esteja, redireciona para entrada
		*/
		$obj = new clsGrupoPessoa($id_visualiza,$cod_grupo);
		$detalhe_pessoa = $obj->detalhe();
		$obj = new clsGrupoModerador($id_visualiza,$cod_grupo);
		$detalhe_moderador = $obj->detalhe();
		
		$obj = new clsFuncionarioSu($id_visualiza);
		
		if(!$obj->detalhe())
		{
			
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
		}
		
		$obj = new clsReuniao($cod_reuniao); 
		$detalhe = $obj->detalhe();
		
		$this->addDetalhe(array("Descrição", $detalhe['descricao']));
    	
		$this->addDetalhe(array("Data Inicio", date("d/m/Y H:i", strtotime(substr($detalhe['data_inicio_marcado'],0,19)))  ));
				
		$this->addDetalhe(array("Data Fim", date("d/m/Y H:i", strtotime(substr($detalhe['data_fim_marcado'],0,19)))  ));
		
		if($detalhe['data_inicio_real'] && !$detalhe['data_fim_real'])
		{
			$data_inicial = strtotime(substr($detalhe['data_inicio_real'],0,19));
			$data_final = time();
			$dif = $data_final - $data_inicial;
			$this->addDetalhe(array("Tempo de Duração", "<div id='tempo'></div>"  ));
			
			$db = new clsBanco();
			$total = $db->CampoUnico( "SELECT COUNT(0) FROM pmiotopic.topicoreuniao WHERE ref_cod_reuniao = '{$cod_reuniao}'" );
			
			echo "<script>var tempo = $dif;setInterval( 'trocaHora();', 1000 );setInterval( 'otopic_qtd_topicos( $cod_grupo, $cod_reuniao);', 30000 );</script><input type=\"hidden\" id=\"qtd_topicos\" value=\"{$total}\">";
		}
		if($detalhe['data_inicio_real'])
		{
			$this->addDetalhe(array("Data Inicio Real", date("d/m/Y H:i", strtotime(substr($detalhe['data_inicio_real'],0,19)))  ));
		}
		if($detalhe['data_fim_real'])
		{
			$data_inicial =   strtotime(substr($detalhe['data_inicio_real'],0,19));
			$data_final = strtotime(substr($detalhe['data_fim_real'],0,19));
			$this->addDetalhe(array("Data Fim Real", date("d/m/Y H:i", strtotime(substr($detalhe['data_fim_real'],0,19)))  ));
			
			$this->addDetalhe(array("Tempo de Duração", "<div id='tempo'>".difTempo($data_inicial,$data_final."</div>")  ));
		}
		$this->addDetalhe(array("Notificado por e-mail", ($detalhe['email_enviado']) ? "Sim" : "Não" ));
		
		$this->addDetalhe(array("<b><i>Tópicos Relacionados</i></b>", "" ));

		$obj = new clsTopicoReuniao();
		$lista  = $obj->lista(false,false,false,false,false,false,false,$cod_reuniao);
		foreach ($lista as $topicos) 
		{
			$obj = new clsTopico($topicos['ref_cod_topico']);
			$detalhe_topico = $obj->detalhe();
			$assunto = $detalhe_topico['assunto'];
        	
       		$finalizado = "";
			if($topicos['finalizado'])
			{
				$finalizado = "<br>Finalizado";
			}
			$assunto = ($topicos['parecer']) ? "$assunto <br><b><i>Parecer Atual: {$topicos['parecer']} $finalizado</i></b>" : $assunto;
			$auxAssunto = ($topicos['parecer']) ? $topicos['parecer'] : "";
			
			$obj = new clsTopicoReuniao();
			$lista_topico_reuniao = $obj->lista(false,false,false,false,false,false,false,false,$topicos['ref_cod_topico']);
			if(count($lista_topico_reuniao)> 1 && is_array($lista_topico_reuniao) )
			{
				foreach ($lista_topico_reuniao as $parecer) 
				{
					if($parecer['parecer'] && $parecer['ref_cod_reuniao'] != $cod_reuniao )
					{
						$assunto = "$assunto <br><i> Outros Pareceres: {$parecer['parecer']}</i>";
					}
				}
				
			}
			$this->addDetalhe(array("Tópicos", $assunto));
		}
		
		$obj_moderador = new clsGrupoModerador($id_visualiza,$cod_grupo);
		$detalhe_moderador = $obj_moderador->detalhe();
		if( $detalhe_moderador && $detalhe_moderador['ativo'] == 1 && !$detalhe['data_inicio_real'] && !$detalhe['data_fim_real'] )
		{
			$this->url_novo = "otopic_reunioes_cad.php?cod_grupo=$cod_grupo";
			$this->url_editar = "otopic_reunioes_cad.php?cod_grupo=$cod_grupo&cod_reuniao=$cod_reuniao";
		}
		$this->url_cancelar = "otopic_meus_grupos_det2.php?cod_grupo=$cod_grupo";
		
		if($detalhe['data_inicio_real'] && !$detalhe['data_fim_real'] & $detalhe_moderador && $detalhe_moderador['ativo'] == 1 )
		{
			/* 
				Lista de Membros do Grupo
			*/
			$obj = new clsGrupoModerador();
			$lista = $obj->lista(false,$cod_grupo);
			$numero = 1;
			if($lista)
			{
				$this->addDetalhe(array("<b><i>Membro(s)</i></b>", ""));
				foreach ($lista as $moderadores) {
					$obj = new clsPessoaFisica($moderadores['ref_ref_cod_pessoa_fj']);
					$detalhe_mod = $obj->detalhe();
					$nome = explode(" ",$detalhe_mod['nome']);
					if(count($nome) >2)
					{
						if(strlen($nome[1]) > 3)
						{
							$nome = "{$nome[0]} {$nome[1]}";
						}else 
						{
							$nome = "{$nome[0]} {$nome[1]} {$nome[2]}";
						}
					}else 
					{
						$nome = $detalhe_mod['nome'];
					}
					
					// Retorna o ultimo sequencial da pessoa no grupo e reuniao em questao
					$obj = new clsParticipante();
					$lista = $obj->lista($moderadores['ref_ref_cod_pessoa_fj'],$cod_grupo,$cod_reuniao,false,false,"data_saida DESC,sequencial DESC");
					$seq = $lista[0]['sequencial'];
	
					$obj = new clsParticipante($moderadores['ref_ref_cod_pessoa_fj'],$cod_grupo,$cod_reuniao,$seq);
					$detalhe_participante = $obj->detalhe();
					if($detalhe_participante['data_saida'] || !$detalhe_participante)
					{
						$this->addDetalhe(array($nome,"<div id='$numero'><a href='#' onclick='move_pessoa_reuniao({$moderadores['ref_ref_cod_pessoa_fj']},1,$cod_reuniao,$cod_grupo,$numero)'><img src='imagens/nvp_bot_entra_reuniao.gif' border='0'></a></div>"));
					}else	
					{
						$this->addDetalhe(array($nome,"<div id='$numero'><a href='#' onclick='move_pessoa_reuniao({$moderadores['ref_ref_cod_pessoa_fj']},2,$cod_reuniao,$cod_grupo,$numero)'><img src='imagens/nvp_bot_sai_reuniao.gif' border='0'></a></div>"));
					}
					$numero++;
				}
			}
			
			$obj = new clsGrupoPessoa();
			$lista = $obj->lista(false,$cod_grupo);
			if($lista)
			{
				foreach ($lista as $mebros) {
					$obj = new clsPessoaFisica($mebros['ref_idpes']);
					$detalhe_membro = $obj->detalhe();
					$nome = explode(" ",$detalhe_membro['nome']);
					if(count($nome) >2)
					{
						if(strlen($nome[1]) > 3)
						{
							$nome = "{$nome[0]} {$nome[1]}";
						}else 
						{
							$nome = "{$nome[0]} {$nome[1]} {$nome[2]}";
						}
					}else 
					{
						$nome = $detalhe_membro['nome'];
					}
					// Retorna o ultimo sequencial da pessoa no grupo e reuniao em questao
					$obj = new clsParticipante();
					$lista = $obj->lista($mebros['ref_idpes'],$cod_grupo,$cod_reuniao,false,false,"data_saida DESC,sequencial DESC");
					$seq = $lista[0]['sequencial'];
					$obj = new clsParticipante($mebros['ref_idpes'],$cod_grupo,$cod_reuniao,$seq);
					$detalhe_participante = $obj->detalhe();
					if($detalhe_participante['data_saida'] || !$detalhe_participante)
					{
						$this->addDetalhe(array($nome,"<div id='$numero'><a href='#' onclick='move_pessoa_reuniao({$mebros['ref_idpes']},1,$cod_reuniao,$cod_grupo,$numero)'><img src='imagens/nvp_bot_entra_reuniao.gif' border='0'></a></div>"));
					}else	
					{
						$this->addDetalhe(array($nome,"<div id='$numero'><a href='#' onclick='move_pessoa_reuniao({$mebros['ref_idpes']},2,$cod_reuniao,$cod_grupo,$numero)'><img src='imagens/nvp_bot_sai_reuniao.gif' border='0'></a></div>"));
					}				$numero++;
				}
			} 
		}
		
		$link = "otopic_reunioes_imprime.php?cod_grupo=$cod_grupo&cod_reuniao=$cod_reuniao&imprimir=jato";
		$linkCor = "otopic_reunioes_imprime.php?cod_grupo=$cod_grupo&cod_reuniao=$cod_reuniao&imprimir=laser";
		
		if(!$detalhe['data_inicio_real'] && $detalhe_moderador && $detalhe_moderador['ativo'] == 1 )
		{
			$this->array_botao = array("Iniciar Reunião", "Imprimir (Jato)", "Imprimir (Laser)");
			$this->array_botao_url_script = array(" if (confirm(\"Deseja Iniciar a Reunião em Tempo Real?\")) { document.location=\"otopic_reuniao_tempo_real.php?cod_reuniao=$cod_reuniao&cod_grupo=$cod_grupo\"} else { document.location=\"otopic_reunioes_nao_tempo_real_cad.php?cod_reuniao=$cod_reuniao&cod_grupo=$cod_grupo\" }","javascript: go(\"$link\");", "javascript: go(\"$linkCor\")");
		}
		elseif (!$detalhe['data_fim_real'] && $detalhe_moderador && $detalhe_moderador['ativo'] == 1)	
		{
			$this->array_botao = array("Finalizar Reunião", "Imprimir (Jato)", "Imprimir (Laser)");
			$this->array_botao_url = array("otopic_reuniao_finaliza.php?cod_reuniao=$cod_reuniao&cod_grupo=$cod_grupo", "$link", "$linkCor");
		}
		elseif($detalhe["data_inicio_real"] && $detalhe["data_fim_real"])
		{
			$this->array_botao = array("Imprimir (Jato)", "Imprimir (Laser)");
			$this->array_botao_url = array("$link", "$linkCor");		
		}
		$this->largura = "100%";
		$objReuniao = new clsReuniao($cod_reuniao);
		$detReuniao = $objReuniao->detalhe();
	}
}

class cadastro extends clsCadastro
{
	var $cod_grupo;
	var $cod_reuniao;
	var $id_pessoa;

	
	function Inicializar()
	{
		$this->addBanner( );

		@session_start();
		$this->id_pessoa = $_SESSION['id_pessoa'];
		session_write_close();
		$retorno = "Novo";

		$this->cod_reuniao = $_GET['cod_reuniao'];
		$this->cod_grupo = $_GET['cod_grupo'];
		
		$this->url_cancelar =  "otopic_membro_det.php?cod_grupo=$this->cod_grupo&cod_membro=$this->cod_membro";
		$this->nome_url_cancelar = "Cancelar";


		return $retorno;
	}

	function Gerar()
	{
		$this->campoOculto("id_pessoa",$this->id_pessoa);
		$this->campoOculto("cod_grupo",$this->cod_grupo);
		$this->campoOculto("cod_reuniao",$this->cod_reuniao);
		
		$obj = new clsTopicoReuniao();
		$lista  = $obj->lista(false,false,false,false,false,false,false,$this->cod_reuniao);
		foreach ($lista as $topicos) {
			$obj = new clsTopico($topicos['ref_cod_topico']);
			$detalhe_topico = $obj->detalhe();
			$this->campoRotulo("assunto_{$topicos['ref_cod_topico']}","<b>Assunto</b>","<b>{$detalhe_topico['assunto']}</b>");
			$this->campoMemo("par_{$topicos['ref_cod_topico']}","Parecer",$topicos['parecer'],50,2);
			
			//escreve os pareceres antigos
			$obj = new clsTopicoReuniao();
			$lista_topico_reuniao = $obj->lista(false,false,false,false,false,false,false,false,$topicos['ref_cod_topico']);
			if(count($lista_topico_reuniao)> 1 && is_array($lista_topico_reuniao) )
			{
				foreach ($lista_topico_reuniao as $parecer) {
					
					if($parecer['parecer'] && $parecer['ref_cod_reuniao'] != $cod_reuniao)
					{
						$this->campoRotulo("outrosPareceres_{$parecer["data_parecer"]}","Outros Pareceres", $parecer['parecer']);
					}
				}
				
			}
			
			$this->campoCheck("fin_{$topicos['ref_cod_topico']}","Finalizado",$topicos['finalizado']);
			$this->campoCheck("del_{$topicos['ref_cod_topico']}", "Apagar esse tópico", "");
			
		}
	
	}
	
	function Novo() 
	{
		foreach ($_POST as $id=>$campo)
		{
			//deleta os tópicos que estão selecionados
			if(substr($id,0,4) == "del_")
			{
				$cod = substr($id,4);
				if($_POST["del_$cod"])
				{
					$objTopicoReuniao = new clsTopicoReuniao($cod, $this->cod_reuniao);
					$objTopicoReuniao->exclui();
				}
			}
			elseif(substr($id,0,4) == "par_")
			{
				$cod = substr($id,4);
				$finalizado = $_POST["fin_$cod"] ? "1" : "";
				$obj = new clsTopicoReuniao($cod,$this->cod_reuniao,$campo,$finalizado);
				$obj->edita();
			}
		}
		die("<script> document.location='otopic_reunioes_det.php?cod_reuniao=$this->cod_reuniao&cod_grupo=$this->cod_grupo'</script>");
	}


}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );
$obj = new clsReuniao($_GET['cod_reuniao']);
$detalhe = $obj->detalhe();
@session_start();
$id_visualiza = $_SESSION['id_pessoa'];
session_write_close();
$obj_moderador = new clsGrupoModerador($id_visualiza,$_GET['cod_grupo']);
$detalhe_moderador = $obj_moderador->detalhe();

if($detalhe['data_inicio_real'] && !$detalhe['data_fim_real'] && $detalhe_moderador && $detalhe_moderador['ativo']==1)
{
	$miolo = new cadastro();
	$pagina->addForm( $miolo );
}
$pagina->MakeAll();

?>