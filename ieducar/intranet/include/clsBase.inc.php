<?php
/**
 *
 * @version SVN: $Id$
 * @author  Prefeitura Municipal de Itajaí
 * @updated 29/03/2007
 * Pacote: i-PLB Software Público Livre e Brasileiro
 *
 * Copyright (C) 2006	PMI - Prefeitura Municipal de Itajaí
 *					ctima@itajai.sc.gov.br
 *
 * Este  programa  é  software livre, você pode redistribuí-lo e/ou
 * modificá-lo sob os termos da Licença Pública Geral GNU, conforme
 * publicada pela Free  Software  Foundation,  tanto  a versão 2 da
 * Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.
 *
 * Este programa  é distribuído na expectativa de ser útil, mas SEM
 * QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-
 * ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-
 * sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.
 *
 * Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU
 * junto  com  este  programa. Se não, escreva para a Free Software
 * Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA
 * 02111-1307, USA.
 *
 */

require_once('include/clsCronometro.inc.php');
require_once('clsConfigItajai.inc.php');
require_once('include/clsBanco.inc.php');
require_once('include/clsMenu.inc.php');
require_once('include/clsControlador.inc.php');
require_once('include/clsLogAcesso.inc.php');

require_once('include/Geral.inc.php');
require_once('include/pmicontrolesis/geral.inc.php');
require_once('include/funcoes.inc.php');

class clsBase extends clsConfig
{
	/*private*/var $titulo = "Prefeitura Cobra Tecnologia;";
	/*private*/var $clsForm = array();
	/*public*/ var $bodyscript = null;
	/*public*/ var $processoAp;
	var $refresh = false;

	var $convidado = false;
	var $renderMenu = true;
	var $renderMenuSuspenso = true;
	var $renderBanner = true;
	var $estilos;
	var $scripts;
	/**
	 * Adiciona um script na tag <head></head> do html
	 *
	 * @var unknown_type
	 */
	var $script_header;
	var $script_footer;
	var $prog_alert;

	/*protected */ function OpenTpl( $template )
	{
		$mudanca_template = "nvp_";
		$arquivo = $this->arrayConfig['strDirTemplates'].$mudanca_template.$template.".tpl";
		$ptrTpl = fopen($arquivo, "r");
		$strArquivo = fread($ptrTpl, filesize($arquivo));
		fclose ($ptrTpl);

		if ( !empty($strArquivo) )
			$arquivo = "<br>Arquivo de template ".$arquivo." aberto com sucesso...";
		else
			$arquivo = "<br>Arquivo de template ".$arquivo." n&atilde;o encontrado ou vazio...";

		$this->Depurar ( $arquivo );

		return $strArquivo;
	}

	/*protected*/ function SetTitulo( $titulo )
	{
		$this->titulo = $titulo;
	}

	/*protected*/ function AddForm( $form )
	{
		$this->clsForm[] = $form;
	}

	/*private */ function MakeHeadHtml ()
	{
		$saida = $this->OpenTpl("htmlhead");
		$saida = str_replace("<!-- #&TITULO&# -->", $this->titulo, $saida);
		if( $this->refresh )
		{
			$saida = str_replace("<!-- #&REFRESH&# -->", "<meta http-equiv='refresh' content='60'>", $saida);
		}

		if( is_array( $this->estilos ) && count( $this->estilos ) )
		{
			$estilos = "";
			foreach ( $this->estilos AS $estilo )
			{
				$estilos .= "<link rel=stylesheet type='text/css' href='styles/{$estilo}.css' />";
			}
			$saida = str_replace( "<!-- #&ESTILO&# -->", $estilos, $saida );
		}

		if( is_array( $this->scripts ) && count( $this->scripts ) )
		{
			$estilos = "";
			foreach ( $this->scripts AS $script )
			{
				$scripts .= "<script type='text/javascript' src='scripts/{$script}.js'></script>";
			}
			$saida = str_replace( "<!-- #&SCRIPT&# -->", $scripts, $saida );
		}
		if( $this->bodyscript )
		{
			$saida = str_replace( "<!-- #&BODYSCRIPTS&# -->", $this->bodyscript, $saida );
		}
		else {
			$saida = str_replace( "<!-- #&BODYSCRIPTS&# -->", "", $saida );
		}


		if( $this->script_header )
		{
			$saida = str_replace( "<!-- #&SCRIPT_HEADER&# -->", $this->script_header, $saida );
		}
		else {
			$saida = str_replace( "<!-- #&SCRIPT_HEADER&# -->", "", $saida );
		}

		return $saida;

		$this->Depurar ( "Cabecalho HTML feito" );
	}

	/*private */ function addEstilo( $estilo_nome )
	{
		$this->estilos[$estilo_nome] = $estilo_nome;
	}

	/*private */ function addScript( $script_nome )
	{
		$this->scripts[$script_nome] = $script_nome;
	}

	/*private */ function MakeFootHtml ()
	{
		$saida =  $this->OpenTpl("htmlfoot");

		if( $this->script_footer )
		{
			$saida = str_replace( "<!-- #&SCRIPT_FOOTER&# -->", $this->script_footer, $saida );
		}
		else {
			$saida = str_replace( "<!-- #&SCRIPT_FOOTER&# -->", "", $saida );
		}
		return $saida;
		$this->Depurar ( "Rodap&eacute; HTML feito" );
	}
//--Inï¿?cio Funï¿?ï¿?o Adicionada
	/*private */ function VerificaPermicao()
	{
		if(is_array($this->processoAp))
		{
			$permite = true;
			foreach($this->processoAp as $processo)
			{

				if(!$this->VerificaPermicaoNumerico($processo))
				{
					$permite = false;
				}
				else
				{
					$this->processoAp = $processo;
					$permite = true;
					break;
				}
			}
			if(!$permite)
			{
				header( "location: index.php?negado=1&err=1" );
				die( "Acesso negado para este usu&acute;rio" );
			}
		}
		else
		{
			if(!$this->VerificaPermicaoNumerico($this->processoAp))
			{
				header( "location: index.php?negado=1&err=2" );
				die( "Acesso negado para este usu&acute;rio" );
			}
		}
		return true;
	}
//--Fim Funï¿?ï¿?o Adicionada
	/*private */ function VerificaPermicaoNumerico($processo_ap)
	{
		if( is_numeric( $processo_ap ) )
		{
			//echo "-1.ERROR";
			@session_start();
			$id_usuario = $_SESSION['id_pessoa'];
			session_write_close();

			$sempermissao = true;

			if( $processo_ap == 0 )
			{
				$this->prog_alert .= "Processo AP == 0!";
			}

			if( $processo_ap != 0 )
			{
				$db = new clsBanco();
				$db->Consulta( "SELECT 1 FROM menu_funcionario WHERE ref_cod_menu_submenu=0 AND ref_ref_cod_pessoa_fj={$id_usuario}" );
				if ($db->ProximoRegistro())
				{
					list($aui) = $db->Tupla();
					$sempermissao = false;
				}

				$db->Consulta( "SELECT 1 FROM menu_funcionario WHERE (ref_cod_menu_submenu={$processo_ap} AND ref_ref_cod_pessoa_fj={$id_usuario}) OR (SELECT true FROM menu_submenu WHERE cod_menu_submenu={$processo_ap} AND nivel=2)" );
				if ($db->ProximoRegistro())
				{
					list($aui) = $db->Tupla();
					$sempermissao = false;

				}

				if ( $sempermissao )
				{
					$ip = empty($_SERVER['REMOTE_ADDR']) ? "NULL" : $_SERVER['REMOTE_ADDR'];
					$ip_de_rede = empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? "NULL" : $_SERVER['HTTP_X_FORWARDED_FOR'];
					$pagina = $_SERVER["PHP_SELF"];
					$posts = "";
					$gets = "";
					$sessions = "";
					foreach ( $_POST AS $key => $val ) $posts .= " - $key: $val\n";
					foreach ( $_GET AS $key => $val ) $gets .= " - $key: $val\n";
					foreach ( $_SESSION AS $key => $val ) $sessions .= " - $key: $val\n";
					$variaveis = "POST\n{$posts}GET\n{$gets}SESSION\n{$sessions}";

					if( $id_usuario )
					{
						$db->Consulta( "INSERT INTO intranet_segur_permissao_negada( ref_ref_cod_pessoa_fj, ip_externo, ip_interno, data_hora, pagina, variaveis) VALUES( '$id_usuario', '$ip', '$ip_de_rede', NOW(), '$pagina', '$variaveis' )" );
					}
					else
					{
						$db->Consulta( "INSERT INTO intranet_segur_permissao_negada( ref_ref_cod_pessoa_fj, ip_externo, ip_interno, data_hora, pagina, variaveis) VALUES( NULL, '$ip', '$ip_de_rede', NOW(), '$pagina', '$variaveis' )" );
					}
					return false;
				}
			}
			return true;
		}
	}

	/*private */ function MakeMenu()
	{
		$menu = $this->openTpl("htmlmenu");
		$menuObj = new clsMenu();
		$saida = $menuObj->MakeMenu( $this->openTpl("htmllinhamenu"), $this->openTpl("htmllinhamenusubtitulo") );

		$saida = str_replace("<!-- #&LINHAS&# -->", $saida, $menu);

		return $saida;
	}

	function makeMenuSuspenso()
	{
		@session_start();
		$idpes = $_SESSION['id_pessoa'];
		@session_write_close();

		$submenu = array();
		$menu_tutor = "";
		if ($this->processoAp)
		{
			$db = new clsBanco();
			$menu_atual = $db->UnicoCampo("SELECT ref_cod_menu_menu FROM menu_submenu WHERE cod_menu_submenu = '{$this->processoAp}'");
			if( $menu_atual )
			{
				$db->Consulta("SELECT cod_menu_submenu FROM menu_submenu WHERE ref_cod_menu_menu = '{$menu_atual}'");
				while ($db->ProximoRegistro()) {
					$tupla = $db->Tupla();
					$submenu[] = $tupla['cod_menu_submenu'];
				}
				$WHERE = implode(" OR ref_cod_menu_submenu = ", $submenu);
				$WHERE = "ref_cod_menu_submenu = $WHERE";
				$menu_tutor = $db->UnicoCampo("SELECT ref_cod_tutormenu FROM pmicontrolesis.menu WHERE  $WHERE limit 1 OFFSET 0");
			}
			else
			{
				$this->prog_alert .= "O menu pai do processo AP {$this->processoAp} está voltando vazio (cod_menu inexistente?).<br>";
			}
		}
		elseif($_SESSION['menu_atual'])
		{
			$db = new clsBanco();
			$db->Consulta("SELECT cod_menu_submenu FROM menu_submenu WHERE ref_cod_menu_menu = '{$_SESSION['menu_atual']}'");
			while ($db->ProximoRegistro()) {
				$tupla = $db->Tupla();
				$submenu[] = $tupla['cod_menu_submenu'];
			}
			$WHERE = implode(" OR ref_cod_menu_submenu = ", $submenu);
			$WHERE = "ref_cod_menu_submenu = $WHERE";
			$menu_tutor = $db->UnicoCampo("SELECT ref_cod_tutormenu FROM pmicontrolesis.menu WHERE  $WHERE limit 1 OFFSET 0");
		}

		if($menu_tutor)
		{
			$obj_menu_suspenso = new clsMenuSuspenso();
			$lista_menu = $obj_menu_suspenso->listaNivel($menu_tutor, $idpes);
			$lista_menu_suspenso = $lista_menu;


			if($lista_menu_suspenso)
			{
//				echo "<pre>";
//				print_r($lista_menu_suspenso);
//				die();

				for($i= count($lista_menu_suspenso)-1; $i >=0; $i-- ) {
					$achou = false;
					if(!$lista_menu_suspenso[$i]['ref_cod_menu_submenu'])
					{
						foreach ($lista_menu as $id => $menu) {
							if($menu['ref_cod_menu_pai'] == $lista_menu_suspenso[$i]['cod_menu'])
							{
								$achou = true;
							}
						}
						if(!$achou)
						{
							unset($lista_menu[$i]);
						}
					}
				}


				$saida ="<script type=\"text/javascript\">";
				$saida .= "array_menu = new Array();array_id = new Array();";
				$banco = new clsBanco();
				foreach ($lista_menu as $menu_suspenso) {
 					$ico_menu = "";

					if(is_numeric($menu_suspenso['ref_cod_ico']) )
		 			{
		 				$db=  new clsBanco();
		 				$db->Consulta("SELECT caminho FROM portal.imagem WHERE cod_imagem = {$menu_suspenso['ref_cod_ico']} ");
		 				if($db->ProximoRegistro())
		 				{
		 					list($ico_menu) = $db->Tupla();
		 					$ico_menu = "imagens/banco_imagens/$ico_menu";
		 				}
		 			}
					$alvo = $menu_suspenso['alvo'] ? $menu_suspenso['alvo'] : "_self";
					$saida .= "array_menu[array_menu.length] = new Array(\"{$menu_suspenso['tt_menu']}\",{$menu_suspenso['cod_menu']},'{$menu_suspenso['ref_cod_menu_pai']}','', '$ico_menu', '{$menu_suspenso['caminho']}', '{$alvo}');";
					if(!$menu_suspenso['ref_cod_menu_pai'])
					{
		  				$saida .= "array_id[array_id.length] = {$menu_suspenso['cod_menu']};";
					}
				}
				$saida .="</script>";
			}

			$saida .="<script type=\"text/javascript\">
					setTimeout(\"setXY();\",150);
					MontaMenu();
				</script>";
			return $saida;
		}
		return false;
	}

	/*private*/ function DataAtual()
	{
		$retorno = "";
		switch(date('w'))
		{
			case "0": $retorno .= "Domingo"; break;
			case "1": $retorno .= "Segunda-feira"; break;
			case "2": $retorno .= "Ter&ccedil;a-feira"; break;
			case "3": $retorno .= "Quarta-feira"; break;
			case "4": $retorno .= "Quinta-feira"; break;
			case "5": $retorno .= "Sexta-feira"; break;
			case "6": $retorno .= "S&aacute;bado"; break;
		}

		$retorno .= ", ".date('d')." de ";

		switch(date('n'))
		{
			case "1": $retorno .= "janeiro de "; break;
			case "2": $retorno .= "fevereiro de "; break;
			case "3": $retorno .= "mar&ccedil;o de "; break;
			case "4": $retorno .= "abril de "; break;
			case "5": $retorno .= "maio de "; break;
			case "6": $retorno .= "junho de "; break;
			case "7": $retorno .= "julho de "; break;
			case "8": $retorno .= "agosto de "; break;
			case "9": $retorno .= "setembro de "; break;
			case "10": $retorno .= "outubro de "; break;
			case "11": $retorno .= "novembro de "; break;
			case "12": $retorno .= "dezembro de "; break;
		}

		$retorno .= date('Y').".";

		return $retorno;
	}

	/*private*/ function MakeBody()
	{

		$corpo = "";
		foreach ($this->clsForm as $form)
		{
			$corpo .= $form->RenderHTML();
			if( is_string( $form->prog_alert ) && $form->prog_alert )
			{
				$this->prog_alert .= $form->prog_alert;
			}
		}

		$menu = "";

		if($this->renderMenu)
		{
			$menu = $this->MakeMenu();
		}

		$data = $this->DataAtual();


		if ($this->renderBanner)
		{
			if( $this->renderMenu )
			{
				$saida = $this->OpenTpl("htmlbody");
			}
			else
			{
				$saida = $this->OpenTpl("htmlbody_sem_menu");
			}
		}
		else
		{
			$saida = $this->OpenTpl("htmlbodys");
		}
		$saida = str_replace("<!-- #&DATA&# -->", $data, $saida);

		if ($this->renderMenu)
		{
			$saida = str_replace("<!-- #&MENU&# -->", $menu, $saida);
		}



		$menu_dinamico = $this->makeBanner();

		@session_start();
		$id_usuario = $_SESSION['id_pessoa'];
		session_write_close();
		$db = new clsBanco();

		$objPessoa = new clsPessoaFisica();
		list($nome_user) = $objPessoa->queryRapida( $id_usuario, "nome" );

		$ultimoAcesso = $db->UnicoCampo("SELECT data_hora FROM acesso WHERE cod_pessoa = $id_usuario ORDER BY data_hora DESC LIMIT 1,1");
		$nome_user = ($nome_user) ? $nome_user : "<span style='color: #DD0000; '>Convidado</span>";
		if($ultimoAcesso)
		{
			$ultimoAcesso = date("d/m/Y H:i", strtotime(substr($ultimoAcesso,0,19)));
		}

		/***********************/
		//Verificar se senha expira dentro de 5 dias.
		$expirando = false;
		$mensagem_expirar = "";
		$db = new clsBanco();
		$db->Consulta( "SELECT tempo_expira_senha, data_troca_senha FROM funcionario WHERE ref_cod_pessoa_fj = '{$id_usuario}' " );
		if($db->ProximoRegistro())
		{
			list($tempo_senha, $data_senha) = $db->Tupla();
			if( ! empty( $tempo_senha ) && ! empty( $data_senha ) )
			{
				if( time() - strtotime( $data_senha ) > ($tempo_senha-10) * 60 * 60 * 24 )
				{
					// senha vai expirar dentro de 10 dias
					$expirando = true;
					$days_left = $tempo_senha - (int)((time() - strtotime( $data_senha )) / 86400);
					$mensagem_expirar = "Sua senha expirará em $days_left dias, atualize sua senha em 'Meus dados' no menu 'Principal' !";
					$mensagem_expirar .= "<script>showExpansivelIframe(800, 270, 'troca_senha_pop.php', 1);</script>";
					
				}
			}
		}
		/***********************/
		
		
		// somente para programadores
//		$this->prog_alert = "teste";
		if( ( $id_usuario == 49659 || $id_usuario == 2151 ||  $id_usuario == 4637 || $id_usuario == 21330|| $id_usuario == 21317|| $id_usuario == 25109|| $id_usuario == 4702 ) )
		{
			if($expirando || $this->prog_alert)
			{
				$mensagem = $expirando ? "<b style='color:red'>$mensagem_expirar</b><br />" : "";
				$mensagem .= $this->prog_alert ? $this->prog_alert : "";
				$saida = str_replace("<!-- #&PROG_ALERT&# -->", "<div class=\"prog_alert\" align=\"center\">$mensagem</div>", $saida);
			}
		}elseif($expirando)
		{
			$saida = str_replace("<!-- #&PROG_ALERT&# -->", "<div class=\"prog_alert\" align=\"center\" style='color: red; font-weight:bold;'>{$mensagem_expirar}</div>", $saida);
		}

		$notificacao = "";
		$db = new clsBanco();
		$db->Consulta("SELECT cod_notificacao, titulo, conteudo, url FROM portal.notificacao WHERE ref_cod_funcionario = '{$id_usuario}' AND data_hora_ativa < NOW()");
		if( $db->numLinhas() )
		{
			while ( $db->ProximoRegistro() )
			{
				list( $cod_notificacao, $titulo, $conteudo, $url ) = $db->Tupla();

				$titulo = ( $url ) ? "<a href=\"{$url}\">{$titulo}</a>": $titulo;

				$notificacao .= "<div id=\"notificacao_{$cod_notificacao}\" class=\"prog_alert\" align=\"left\">
				<div class=\"controle_fechar\" title=\"Fechar\" onclick=\"fecha_notificacao( {$cod_notificacao} );\">x</div>
				<center><strong>Notifica&ccedil;&atilde;o</strong></center>
				<b>T&iacute;tulo</b>: {$titulo}<br />
				<b>Conte&uacute;do</b>: " . str_replace( "\n", "<br>", $conteudo ) . "<br />
				</div>";
			}
			$saida = str_replace( "<!-- #&NOTIFICACOES&# -->", $notificacao, $saida );
			$db->Consulta("UPDATE portal.notificacao SET visualizacoes = visualizacoes + 1 WHERE ref_cod_funcionario = '{$id_usuario}' AND data_hora_ativa < NOW()");
			$db->Consulta("DELETE FROM portal.notificacao WHERE visualizacoes > 10");
		}

		$saida = str_replace("<!-- #&ULTIMOACESSO&# -->", $ultimoAcesso, $saida);
		$saida = str_replace("<!-- #&USERLOGADO&# -->", $nome_user, $saida);
		$saida = str_replace("<!-- #&CORPO&# -->", $corpo, $saida);
		$saida = str_replace("<!-- #&ANUNCIO&# -->", $menu_dinamico, $saida);
		
		
		//verificação do ip da máquina
		if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '' )
		{
			$ip_maquina = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else 
			$ip_maquina = $_SERVER['REMOTE_ADDR'];
		
		$sql = "UPDATE funcionario SET ip_logado = '$ip_maquina' , data_login = NOW() WHERE ref_cod_pessoa_fj = {$id_usuario}";
		$db2 = new clsBanco();
		$db2->Consulta($sql);

		return $saida;

		$this->Depurar ( "Corpo HTML feito" );

	}

	/*private*/ function organiza($listaBanners)
	{
		$aux_inicio = 0;
		$aux_fim = 0;
		foreach($listaBanners as $ind => $banner)
		{
			$aux_fim = $aux_inicio + $banner["prioridade"];
			$banner["controle_inicio"] = $aux_inicio;
			$banner["controle_fim"] = $aux_fim;
			$aux_inicio = $aux_fim+1;
			$listaBanners[$ind] = $banner;
		}
		return array($listaBanners, $aux_fim);
	}

	/*private*/ function makeBanner()
	{
		$retorno = "";
		$listaBanners = array();
		$db = new clsBanco();
		$db->Consulta("SELECT caminho, title, prioridade, link FROM portal_banner WHERE lateral=1 ORDER BY prioridade, title");
		while($db->ProximoRegistro())
		{
			list($caminho, $title, $prioridade, $link) = $db->Tupla();

			$listaBanners[] = array("titulo"=>$title, "caminho"=>$caminho, "prioridade"=>$prioridade, "link"=>$link, "controle_inicio"=>0, "controle_fim"=>0);

		}

		list ($listaBanners, $aux_fim) = $this->organiza($listaBanners);

		$pregadas = 0;
		$total_pregar = count($listaBanners) > 7 ? 7 :count($listaBanners);
		while ($pregadas < $total_pregar)
		{
			$sorteio = rand(0, $aux_fim);
			foreach($listaBanners as $ind => $banner)
			{
				if ($banner["controle_inicio"]<=$sorteio && $banner["controle_fim"]>=$sorteio)
				{
					if ($pregadas == 0)
					{
						$img = "<IMG style='margin-top: 170px;' src='fotos/imgs/{$banner['caminho']}' border=0 title='{$banner['titulo']}' alt='{$banner['titulo']}' width='149' height='74'>";

						if (!empty($banner['link']))
						{
							$retorno .= "<a href='{$banner['link']}' target='_blank' alt='{$banner['titulo']}'>{$img}</a><BR><BR>";
						}
						else
						{
							$retorno .= "{$img}<BR><BR>";
						}
					}
					else
					{
						$img = "<IMG src='fotos/imgs/{$banner['caminho']}' border=0 title='{$banner['titulo']}' alt='{$banner['titulo']}' width='149' height='74'>";

						if (!empty($banner['link']))
						{
							$retorno .= "<a href='{$banner['link']}' target='_blank' alt='{$banner['titulo']}'>{$img}</a><BR><BR>";
						}
						else
						{
							$retorno .= "{$img}<BR><BR>";
						}
					}
					unset($listaBanners[$ind]);
					$pregadas++;
					list ($listaBanners, $aux_fim) = $this->organiza($listaBanners);
					continue;
				}
			}
		}
		return $retorno;
	}

	/*protected*/ function Formular()
	{
		return false;
	}

	/*private */ function CadastraAcesso()
	{
		@session_start();
		if (@$_SESSION['marcado'] != "private" )
		{
			if (!$this->convidado)
			{
				$ip = empty($_SERVER['REMOTE_ADDR']) ? "NULL" : $_SERVER['REMOTE_ADDR'];
				$ip_de_rede = empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? "NULL" : $_SERVER['HTTP_X_FORWARDED_FOR'];
				$id_pessoa = $_SESSION['id_pessoa'];

				$logAcesso = new clsLogAcesso( false, $ip, $ip_de_rede, $id_pessoa );
				$logAcesso->cadastra();

				$_SESSION['marcado'] = "private";
			}
		}
		session_write_close();
	}

	/*private*/ function MakeAll ()
	{

		$cronometro = new clsCronometro();
		$cronometro->marca( "inicio" );
		$liberado = true;

		$saida_geral = "";

		if ($this->convidado)
		{
			@session_start();
			$_SESSION['convidado']=true;
			$_SESSION['id_pessoa']="0";
			session_write_close();
		}

		$controlador = new clsControlador();
		if ($controlador->Logado() && $liberado || $this->convidado)
		{
			$this->Formular();
			$this->VerificaPermicao();
			$this->CadastraAcesso();
			$saida_geral = $this->MakeHeadHtml();
			if($this->renderMenu)
			{
				$saida_geral .= $this->MakeBody();
			}
			else
			foreach ($this->clsForm as $form)
			{
				$saida_geral .= $form->RenderHTML();
			}
			$saida_geral .= $this->MakeFootHtml();
			if($_GET['suspenso']==1 || $_SESSION['suspenso']==1 || $_SESSION["tipo_menu"] == 1 )
			{
				if($this->renderMenuSuspenso)
				{
					$saida_geral = str_replace("<!-- #&MENUSUSPENSO&# -->", $this->makeMenuSuspenso(), $saida_geral);
				}

				if($_GET['suspenso']==1)
				{
					@session_start();
						$_SESSION['suspenso']=1;
					@session_write_close();
				}
			}
		}
		else if ((empty($_POST['login']))||(empty($_POST['senha'])) && $liberado)
		{
			$saida_geral .= $this->MakeHeadHtml();
			$controlador->Logar	(false);
			$saida_geral .= $this->MakeFootHtml();
		}
		else
		{
			$controlador->Logar(true);
			if ($controlador->Logado() && $liberado)
			{

				$this->Formular();
				$this->VerificaPermicao();
				$this->CadastraAcesso();
				$saida_geral = $this->MakeHeadHtml();
				$saida_geral .= $this->MakeBody();
				$saida_geral .= $this->MakeFootHtml();
			}
			else
			{
				$saida_geral = $this->MakeHeadHtml();
				$controlador->Logar	(false);
				$saida_geral .= $this->MakeFootHtml();
			}
		}
		echo $saida_geral;
		$cronometro->marca( "fim" );
		$tempoTotal = $cronometro->getTempoTotal();
		$tempoTotal += 0;
		$objConfig = new clsConfig();
		if ( $tempoTotal > $objConfig->arrayConfig["intSegundosProcessaPagina"] )
		{
			$conteudo = "<table border=\"1\" width=\"100%\">";
			$conteudo .= "<tr><td><b>Data</b>:</td><td>" . date( "d/m/Y H:i:s", time() ) . "</td></tr>";
			$conteudo .= "<tr><td><b>Script</b>:</td><td>{$_SERVER["PHP_SELF"]}</td></tr>";
			$conteudo .= "<tr><td><b>Tempo de processamento</b>:</td><td>{$tempoTotal} segundos</td></tr>";
			$conteudo .= "<tr><td><b>Tempo max permitido</b>:</td><td>{$objConfig->arrayConfig["intSegundosProcessaPagina"]} segundos</td></tr>";
			$conteudo .= "<tr><td><b>URL get</b>:</td><td>{$_SERVER['QUERY_STRING']}</td></tr>";
			$conteudo .= "<tr><td><b>Metodo</b>:</td><td>{$_SERVER["REQUEST_METHOD"]}</td></tr>";
			if ( $_SERVER["REQUEST_METHOD"] == "POST" )
			{
				$conteudo .= "<tr><td><b>POST vars</b>:</td><td>";
				foreach ( $_POST AS $var => $val )
				{
					$conteudo .= "{$var} => {$val}<br>";
				}
				$conteudo .= "</td></tr>";
			} else if ( $_SERVER["REQUEST_METHOD"] == "GET" )
			{
				$conteudo .= "<tr><td><b>GET vars</b>:</td><td>";
				foreach ( $_GET AS $var => $val )
				{
					$conteudo .= "{$var} => {$val}<br>";
				}
				$conteudo .= "</td></tr>";
			}

			if( $_SERVER["HTTP_REFERER"] )
			{
				$conteudo .= "<tr><td><b>Referrer</b>:</td><td>{$_SERVER["HTTP_REFERER"]}</td></tr>";
			}
			$conteudo .= "</table>";

			$objMail = new clsEmail( $objConfig->arrayConfig['ArrStrEmailsAdministradores'], "[INTRANET - PMI] Desempenho de pagina", $conteudo );
			$objMail->envia();
		}

//		$dbt->Consulta("UPDATE pmidesenvolvimento.verificatempo SET data_fim = NOW() WHERE id =$id ");
	}

	function setAlertaProgramacao( $string )
	{
		if( is_string( $string ) && $string )
		{
			$this->prog_alert = $string;
		}
	}
}
?>
