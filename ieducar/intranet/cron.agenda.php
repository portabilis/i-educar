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
	// script executado na cronTab as 07:00 e as 13:00 hs
	chdir( "/home/pagina/public_html/intranet/" );
	require_once( "include/clsBanco.inc.php" );
	require_once( "include/clsEmail.inc.php" );
	require_once( "include/clsAgenda.inc.php" );
	require_once( "include/Geral.inc.php" );
	
	// Configuracoes
	$verbose = true;
	
	$data = date( "d/m/Y", time() );
	$data_db = date( "Y-m-d", time() );
	$enviados = 0;
	$db = new clsBanco();
	$db2 = new clsBanco();
	$db->Consulta( "SELECT cod_agenda, ref_ref_cod_pessoa_own FROM agenda" );
	while ( $db->ProximoRegistro() ) 
	{
		list( $cod_agenda, $cod_pessoa ) = $db->Tupla();
		$conteudo = "";
		$objAgenda = new clsAgenda( 0, false, $cod_agenda );
		
		if( $cod_pessoa )
		{
			$objPessoa = new clsPessoaFisica();
			list( $email ) = $objPessoa->queryRapida( $cod_pessoa, "email" );
			
			if( date( "H", time() ) < 8 )
			{
				// compromissos da manha
				$compromissos = $objAgenda->listaCompromissos( "$data_db 00:00", "$data_db 13:00" );
				$periodo = "Manhã";
			}
			else 
			{
				// compromissos da tarde
				$compromissos = $objAgenda->listaCompromissos( "$data_db 13:00", "$data_db 23:59" );
				$periodo = "Tarde";
			}
			
			$conteudo = "Compromissos do dia $data, no periodo da $periodo.<br><br>\n\n";
			
			if( $email && is_array( $compromissos ) && count( $compromissos ) )
			{
				$qtd_tit_copia_desc = 5;
				$assunto = "[PMI AGENDA] - Compromissos da agenda " . $objAgenda->getNome();
			
				foreach ( $compromissos AS $compromisso )
				{
					// preenche o conteudo com os compromissos
					$data_inicio = $compromisso["data_inicio"];
					$cod_agenda_compromisso = $compromisso["cod_agenda_compromisso"];
					$data_fim = $compromisso["data_fim"];
					$titulo = $compromisso["titulo"];
					$descricao = $compromisso["descricao"];
					
					$hora_inicio = date( "H:i", strtotime( $data_inicio ) );
					$hora_fim = date( "H:i", strtotime( $data_fim ) );
					if( $titulo )
					{
						$disp_titulo = $titulo;
					}
					else 
					{
						// se nao tiver titulo pega as X primeiras palavras da descricao ( X = $qtd_tit_copia_desc )
						$disp_titulo = implode( " ", array_slice( explode( " ", $descricao ), 0, $qtd_tit_copia_desc ) );
					}
					$disp_titulo = "{$hora_inicio} - {$disp_titulo} - {$hora_fim}";
					
					$conteudo .= "<b>$disp_titulo</b><br>\n$descricao<br><br>\n\n";
				}
				$objEmail = new clsEmail( $email, $assunto, $conteudo );
				$objEmail->envia();
				
				$enviados++;
				if( $verbose )
				{
					echo "-";
					if( ! ( $enviados % 50 ) ) echo "| ( $enviados )\n";
				}
			}
		}
	}
	if( $enviados )
	{
		echo "\nCron.Agenda - $enviados e-mails enviados.\n";
	}
?>