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
require_once ("include/clsListagem.inc.php");
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

class indice extends clsListagem
{
	function Gerar()
	{
		$this->titulo = "Detalhe da Reunião";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet", false);
		
		@session_start();
		$id_visualiza = $_SESSION['id_pessoa'];
		@session_write_close();
	
		$this->titulo = "Reunião";
		$this->addBanner(false,false,false,false );
		
		$cod_membro = $_GET['cod_membro'];
		$cod_grupo = $_GET['cod_grupo'];
		$cod_reuniao = $_GET['cod_reuniao'];
		
		$this->addCabecalhos( array( "Imprimir") );
		
		//pdf
		$objRelatorio = new relatorios("Pauta",80,false,false,"A4","Prefeitura de Itajaí\nCentro Tecnologico de Informação e Modernização Administrativa.\nRua Alberto Werner, 100 - Vila Operária\nCEP. 88304-053 - Itajaí - SC","#FFFFFF","#000000", "#FFFFFF", "#FFFFFF");
		$objRelatorioCor = new relatorios("Pauta",80,false,false,"A4","Prefeitura de Itajaí\nCentro Tecnologico de Informação e Modernização Administrativa.\nRua Alberto Werner, 100 - Vila Operária\nCEP. 88304-053 - Itajaí - SC");
		$objRelatorio->novalinha(array("Informações Gerais:"), 0, 16, true,"arial",false,"#FFFFFF",false,"#000000");
		$objRelatorioCor->novalinha(array("Informações Gerais:"), 0, 16, true);
		
		$obj = new clsReuniao($cod_reuniao); 
		$detalhe = $obj->detalhe();
		
		//$this->addDetalhe(array("Descrição", $detalhe['descricao']));
		//pdf
    	$objRelatorio->novalinha(array("Descrição:", quebra_linhas_pdf($detalhe['descricao'], 70) ), 0, 13*(count(explode("\n",quebra_linhas_pdf($detalhe['descricao'], 70) ))) , false, false, 107,false,"#FFFFFF");
    	$objRelatorioCor->novalinha(array("Descrição:", quebra_linhas_pdf($detalhe['descricao'], 70) ), 0, 13*(count(explode("\n",quebra_linhas_pdf($detalhe['descricao'], 70) ))), false, false, 107);
    	
		//$this->addDetalhe(array("Data Inicio", date("d/m/Y H:i", strtotime(substr($detalhe['data_inicio_marcado'],0,19)))  ));
		//pdf
		if ((substr( $detalhe['data_inicio_marcado'], 0, 10 ) == substr( $detalhe['data_fim_marcado'], 0, 10 ) ))
		{
			$objRelatorio->novalinha(array("Data Marcada:", date( "d/m/Y H:i:s", strtotime( substr( $detalhe['data_inicio_marcado'], 0) ) )." as ".date("H:i:s", strtotime(substr($detalhe['data_fim_marcado'],10))) ), 0, 13 , false, false, 108,false,"#FFFFFF");
	    	$objRelatorioCor->novalinha(array("Data Marcada:", date( "d/m/Y H:i:s", strtotime( substr( $detalhe['data_inicio_marcado'], 0 ) ) )." as ".date("H:i:s", strtotime(substr($detalhe['data_fim_marcado'],10,19))) ), 0, 13 , false, false, 108);
		}
		else
		{
			$objRelatorio->novalinha(array("Data Marcada:", date( "d/m/Y H:i:s", strtotime( substr( $detalhe['data_inicio_marcado'],0 ) ))." - ".date("d/m/Y H:i:s", strtotime(substr($detalhe['data_fim_marcado'],0)) ) ), 0, 13 , false, false, 109,false,"#FFFFFF");
        	$objRelatorioCor->novalinha(array("Data Marcada:", date( "d/m/Y H:i:s", strtotime( substr( $detalhe['data_inicio_marcado'],0 ) ))." - ".date("d/m/Y H:i:s", strtotime(substr($detalhe['data_fim_marcado'],0)) ) ), 0, 13 , false, false, 109);		
		}
		
		if(($detalhe['data_inicio_marcado'] != $detalhe['data_inicio_real']) || ($detalhe['data_fim_marcado'] != $detalhe['data_fim_real']))
		{
			if ((substr( $detalhe['data_inicio_real'], 0, 10 ) == substr( $detalhe['data_fim_real'], 0, 10 ) ))
			{
				if($detalhe['data_fim_real'])
				{	
					$objRelatorio->novalinha(array("Data da execução:", date( "d/m/Y H:i:s", strtotime( substr( $detalhe['data_inicio_real'],0 ) ))." as ".date("H:i:s", strtotime(substr($detalhe['data_fim_real'],10)) ) ), 0, 13 , false, false, 109,false,"#FFFFFF");
	        		$objRelatorioCor->novalinha(array("Data da execução:", date( "d/m/Y H:i:s", strtotime( substr( $detalhe['data_inicio_real'],0 ) ))." as ".date("H:i:s", strtotime(substr($detalhe['data_fim_real'],10)) ) ), 0, 13 , false, false, 109);			
				}
			}
			else 
			{
				if($detalhe['data_fim_real'])
				{
					$objRelatorio->novalinha(array("Data da execução:", date( "d/m/Y H:i:s", strtotime( substr( $detalhe['data_inicio_real'],0 ) ))." - ".date("d/m/Y H:i", strtotime(substr($detalhe['data_fim_real'],0)) ) ), 0, 13 , false, false, 109,false,"#FFFFFF");
		        	$objRelatorioCor->novalinha(array("Data da execução:", date( "d/m/Y H:i:s", strtotime( substr( $detalhe['data_inicio_real'],0 ) ))." - ".date("d/m/Y H:i", strtotime(substr($detalhe['data_fim_real'],0)) ) ), 0, 13 , false, false, 109);
				}
			}
		}
		
		//$this->addDetalhe(array("Data Fim", date("d/m/Y H:i", strtotime(substr($detalhe['data_fim_marcado'],0,19)))  ));
		
		//pdf
		if(!$detalhe["data_fim_real"])
		{
			$notificacaoPorEmail = ($detalhe['email_enviado']) ? "Sim" : "Não";
	        $objRelatorio->novalinha(array("Notificado por e-mail:", $notificacaoPorEmail), 0, 13 , false, false, 110,false,"#FFFFFF");
	        $objRelatorioCor->novalinha(array("Notificado por e-mail:", $notificacaoPorEmail), 0, 13 , false, false, 110,false,"#FFFFFF");
		}
		else 
		{
			/*$dif = strtotime(substr($detalhe['data_fim_real'],0,19)) - strtotime(substr($detalhe['data_inicio_real'],0,19));
			$notificacaoPorEmail = ($detalhe['email_enviado']) ? "Sim" : "Não";
        	$objRelatorio->novalinha(array("Tempo de Duração:", $dif."   "."Notificado por e-mail: ".$notificacaoPorEmail ), 0, 13 , false, false, 109,false,"#FFFFFF");
        	$objRelatorioCor->novalinha(array("Tempo de Duração:", $dif."   "."Notificado por e-mail: ".$notificacaoPorEmail ), 0, 13 , false, false, 109);*/
		}
		
		if($detalhe['data_inicio_real'] && !$detalhe['data_fim_real'])
		{
			$data_inicial = strtotime(substr($detalhe['data_inicio_real'],0,19));
			$data_final = time();
			$dif = $data_final - $data_inicial;
			//$this->addDetalhe(array("Tempo de Duração", "<div id='tempo'></div>"  ));
			//pdf
			$notificacaoPorEmail = ($detalhe['email_enviado']) ? "Sim" : "Não";
            $objRelatorio->novalinha(array("Notificado por e-mail:", $notificacaoPorEmail), 0, 13 , false, false, 110,false,"#FFFFFF");
        	$objRelatorio->novalinha(array("Tempo de Duração:", $dif."   "."Notificado por e-mail: ".$notificacaoPorEmail ), 0, 13 , false, false, 109,false,"#FFFFFF");
        	$objRelatorioCor->novalinha(array("Tempo de Duração:", $dif."   "."Notificado por e-mail: ".$notificacaoPorEmail ), 0, 13 , false, false, 109);
			
			echo "<script>var tempo = $dif;  setInterval( 'trocaHora();', 1000 );</script>";
		}
		if($detalhe['data_inicio_real'])
		{
			//$this->addDetalhe(array("Data Inicio Real", date("d/m/Y H:i", strtotime(substr($detalhe['data_inicio_real'],0,19)))  ));
			//pdf
        	//$objRelatorio->novalinha(array("Data Inicio Real:", date( "d/m/Y H:i", strtotime( substr( $detalhe['data_inicio_real'],0,19 ) ) ) ), 0, 13 , false, false, 109,false,"#FFFFFF");			
		}
		if($detalhe['data_fim_real'])
		{
			$data_inicial =   strtotime(substr($detalhe['data_inicio_real'],0,19));
			$data_final = strtotime(substr($detalhe['data_fim_real'],0,19));
			//$this->addDetalhe(array("Data Fim Real", date("d/m/Y H:i", strtotime(substr($detalhe['data_fim_real'],0,19)))  ));
			//pdf
        	//$objRelatorio->novalinha(array("Data da execução:", date( "d/m/Y H:i", strtotime( substr( $detalhe['data_inicio_real'],0,19 ) ))." - ".date("d/m/Y H:i", strtotime(substr($detalhe['data_fim_real'],0,19)) ) ), 0, 13 , false, false, 109,false,"#FFFFFF");
        	//$objRelatorioCor->novalinha(array("Data da execução:", date( "d/m/Y H:i", strtotime( substr( $detalhe['data_inicio_real'],0,19 ) ))." - ".date("d/m/Y H:i", strtotime(substr($detalhe['data_fim_real'],0,19)) ) ), 0, 13 , false, false, 109);
			
			//$this->addDetalhe(array("Tempo de Duração", "<div id='tempo'>".difTempo($data_inicial,$data_final."</div>")  ));
			//pdf
			$notificacaoPorEmail = ($detalhe['email_enviado']) ? "Sim" : "Não";
        	$objRelatorio->novalinha(array("Tempo de Duração:", difTempo($data_inicial,$data_final)."   "."Notificado por e-mail: ".$notificacaoPorEmail ), 0, 13 , false, false, 109,false,"#FFFFFF");
        	$objRelatorioCor->novalinha(array("Tempo de Duração:", difTempo($data_inicial,$data_final)."   "."Notificado por e-mail: ".$notificacaoPorEmail ), 0, 13 , false, false, 109);

		}
		//$this->addDetalhe(array("Notificado por e-mail", ($detalhe['email_enviado']) ? "Sim" : "Não" ));
		$objRelatorio->novalinha(false,0,8,false,false,false,false,false,false,true);
		
		//$this->addDetalhe(array("<b><i>Tópicos Relacionados</i></b>", "" ));
		//pdf
        $objRelatorio->novalinha(array("Tópicos Relacionados:"), 0, 13, true,"arial",false,"#FFFFFF",false,"#000000");
        $objRelatorioCor->novalinha(array("Tópicos Relacionados:"), 0, 13, true);

		$obj = new clsTopicoReuniao();
		$lista  = $obj->lista(false,false,false,false,false,false,false,$cod_reuniao);
		foreach ($lista as $topicos) 
		{
			$obj = new clsTopico($topicos['ref_cod_topico']);
			$detalhe_topico = $obj->detalhe();
			$assunto = $detalhe_topico['assunto'];
			//pdf
			$auxAssunto = $detalhe_topico['assunto'];
        	$objRelatorio->novalinha(array("Assunto:", quebra_linhas_pdf( $auxAssunto, 70 ) ), 0, 13*(count(explode("\n",quebra_linhas_pdf($auxAssunto, 70) )))  , false, false, 108,false,"#FFFFFF");
        	$objRelatorioCor->novalinha(array("Assunto:", quebra_linhas_pdf( $auxAssunto, 70 ) ), 0, 13*(count(explode("\n",quebra_linhas_pdf($auxAssunto, 70) )))  , false, false, 108);
        	//$finalizado = ($topicos["finalizado"]) ? "Sim" : "Não";
       		//$objRelatorio->novalinha(array("Finalizado:", $finalizado), 15, 13 , false, false, 96,false,"#FFFFFF");
       		//$objRelatorioCor->novalinha(array("Finalizado:", $finalizado), 15, 13 , false, false, 96);
        	
       		$finalizado = "";
			if($topicos['finalizado'])
			{
				$finalizado = "<br>Finalizado";
			}
			$assunto = ($topicos['parecer']) ? "$assunto <br><b><i>Parecer Atual: {$topicos['parecer']} $finalizado</i></b>" : $assunto;
			$auxAssunto = ($topicos['parecer']) ? $topicos['parecer'] : "";
			if($auxAssunto)
			{
				//pdf
	        	$objRelatorio->novalinha(array("Parecer Atual:", quebra_linhas_pdf( $auxAssunto, 60 ) ), 15, 13*(count(explode("\n",quebra_linhas_pdf($auxAssunto, 70) )))  , false, false, 93,false,"#FFFFFF");
	        	$objRelatorioCor->novalinha(array("Parecer Atual:", quebra_linhas_pdf( $auxAssunto, 60 ) ), 15, 13*(count(explode("\n",quebra_linhas_pdf($auxAssunto, 70) )))  , false, false, 93);
			}
			
			$obj = new clsTopicoReuniao();
			$lista_topico_reuniao = $obj->lista(false,false,false,false,false,false,false,false,$topicos['ref_cod_topico']);
			if(count($lista_topico_reuniao)> 1 && is_array($lista_topico_reuniao) )
			{
				foreach ($lista_topico_reuniao as $parecer) 
				{
					if($parecer['parecer'] && $parecer['ref_cod_reuniao'] != $cod_reuniao )
					{
						
						$assunto = "$assunto <br><i> Outros Pareceres: {$parecer['parecer']}</i>";
						//pdf
						$auxAssunto =  $parecer['parecer'];
        	            $objRelatorio->novalinha(array("Outros Pareceres:", quebra_linhas_pdf( $auxAssunto, 60 ) ), 15, 13*(count(explode("\n",quebra_linhas_pdf($auxAssunto, 70) )))  , false, false, 93,false,"#FFFFFF");
        	            $objRelatorioCor->novalinha(array("Outros Pareceres:", quebra_linhas_pdf( $auxAssunto, 60 ) ), 15, 13*(count(explode("\n",quebra_linhas_pdf($auxAssunto, 70) )))  , false, false, 93);
					}
				}
				
			}
			if(!$detalhe["data_fim_real"])
			{
				$objRelatorio->novalinha(array(""), 15, 13*(count(explode("\n",quebra_linhas_pdf($auxAssunto, 70) )))  , false, false, 93,false,"#FFFFFF");
				$objRelatorio->novalinha(array(""), 15, 13*(count(explode("\n",quebra_linhas_pdf($auxAssunto, 70) )))  , false, false, 93,false,"#FFFFFF");
				$objRelatorio->novalinha(array(""), 15, 13*(count(explode("\n",quebra_linhas_pdf($auxAssunto, 70) )))  , false, false, 93,false,"#FFFFFF");
	        	$objRelatorioCor->novalinha(array(""), 15, 13*(count(explode("\n",quebra_linhas_pdf($auxAssunto, 70) )))  , false, false, 93);
	        	$objRelatorioCor->novalinha(array(""), 15, 13*(count(explode("\n",quebra_linhas_pdf($auxAssunto, 70) )))  , false, false, 93);
	        	$objRelatorioCor->novalinha(array(""), 15, 13*(count(explode("\n",quebra_linhas_pdf($auxAssunto, 70) )))  , false, false, 93);
			}
			//$this->addDetalhe(array("Tópicos", $assunto));
			
		}
		
		/*
			Escreve na pauta (pdf) a lista de participantes
		*/
		if($detalhe['data_fim_real'])
		{
			$objParticipantes = new clsParticipante();
			$listaParticipantes = $objParticipantes->lista(false, $cod_grupo, $cod_reuniao);
			
			$objRelatorio->fillText();
			$objRelatorioCor->fillText();
	
			if($listaParticipantes)
			{
				//pdf
	     		$objRelatorio->novalinha(array(), 0, 5, true,"arial",false,"#FFFFFF",false,"#000000");
				$objRelatorio->novalinha(array("Participantes:"), 0, 16, true,"arial",false,"#FFFFFF",false,"#000000");
				$objRelatorioCor->novalinha(array("Participantes:"), 0, 16, true,"arial");
				foreach ($listaParticipantes AS $participante)
				{
					$objPessoaFisica = new clsPessoaFisica($participante["ref_ref_idpes"]);
					$detPessoaFisica = $objPessoaFisica->detalhe();
		        	//pdf
					if(substr( $participante["data_chegada"], 0, 10 ) == substr( $participante["data_saida"], 0, 10 ))
					{
						$objRelatorio->novalinha(array($detPessoaFisica["nome"], date ( "d/m/Y H:i:s", strtotime( substr( $participante["data_chegada"], 0, 18 ) ) )." as ".date ( "H:i:s", strtotime( substr( $participante["data_saida"], 10 ) ) ) ), 15, 13 , false, false, 205,false,"#FFFFFF");
						$objRelatorioCor->novalinha(array($detPessoaFisica["nome"], date ( "d/m/Y H:i:s", strtotime( substr( $participante["data_chegada"], 0, 18 ) ) )." as ".date ( "H:i:s", strtotime( substr( $participante["data_saida"], 10 ) ) ) ), 15, 13 , false, false, 205);					
					}
					else 
					{
			        	$objRelatorio->novalinha(array($detPessoaFisica["nome"], date ( "d/m/Y H:i:s", strtotime( substr( $participante["data_chegada"], 0, 18 ) ) )." - ".date ( "d/m/Y H:i:s", strtotime( substr( $participante["data_saida"], 0, 18 ) ) ) ), 15, 13 , false, false, 205,false,"#FFFFFF");
						$objRelatorioCor->novalinha(array($detPessoaFisica["nome"], date ( "d/m/Y H:i:s", strtotime( substr( $participante["data_chegada"], 0, 18 ) ) )." - ".date ( "d/m/Y H:i:s", strtotime( substr( $participante["data_saida"], 0, 18 ) ) ) ), 15, 13 , false, false, 205);
					}
				}
			}
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
				//$this->addDetalhe(array("<b><i>Membro(s)</i></b>", ""));
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
						//$this->addDetalhe(array($nome,"<div id='$numero'><a href='#' onclick='move_pessoa_reuniao({$moderadores['ref_ref_cod_pessoa_fj']},1,$cod_reuniao,$cod_grupo,$numero)'><img src='imagens/nvp_bot_entra_reuniao.gif' border='0'></a></div>"));
					}else	
					{
						//$this->addDetalhe(array($nome,"<div id='$numero'><a href='#' onclick='move_pessoa_reuniao({$moderadores['ref_ref_cod_pessoa_fj']},2,$cod_reuniao,$cod_grupo,$numero)'><img src='imagens/nvp_bot_sai_reuniao.gif' border='0'></a></div>"));
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
						//$this->addDetalhe(array($nome,"<div id='$numero'><a href='#' onclick='move_pessoa_reuniao({$mebros['ref_idpes']},1,$cod_reuniao,$cod_grupo,$numero)'><img src='imagens/nvp_bot_entra_reuniao.gif' border='0'></a></div>"));
					}else	
					{
						//$this->addDetalhe(array($nome,"<div id='$numero'><a href='#' onclick='move_pessoa_reuniao({$mebros['ref_idpes']},2,$cod_reuniao,$cod_grupo,$numero)'><img src='imagens/nvp_bot_sai_reuniao.gif' border='0'></a></div>"));
					}				$numero++;
				}
			} 
		}
		//fecha o pdf
		$link = $objRelatorio->fechaPdf();
		$linkCor = $objRelatorioCor->fechaPdf();
		if($_GET['imprimir'] == "jato")
		{
			$this->addLinhas(array("<a href=$link>Clique aqui para abrir o arquivo</a>"));
		}
		else 
		{
			$this->addLinhas(array("<a href=$linkCor>Clique aqui para abrir o arquivo</a>"));
		}
		$this->array_botao = array("Cancelar");
		$this->array_botao_url = array("otopic_reunioes_det.php?cod_reuniao=$cod_reuniao&cod_grupo=$cod_grupo");
				
		$this->largura = "100%";
		$objReuniao = new clsReuniao($cod_reuniao);
		$detReuniao = $objReuniao->detalhe();
	}
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>