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

require_once ("include/clsBanco.inc.php");

class clsControlador
{
	var $logado;
	var $erroMsg;

	function clsControlador()
	{
		@session_set_cookie_params(1200);
		@session_start();
		if( $_SESSION["servicos"]["intranet"] < time() )
		{
			//session_unset();
			//header( "location: http://servicos.itajai.sc.gov.br" );
			//die();
		}

		if ($_SESSION['itj_controle']=="logado")
		{
			$this->logado = true;
		}
		else
		{
			$this->logado = false;
		}

		/*
		CONTROLE DOS MENUS
		*/
		if( isset( $_GET['mudamenu'] ) && isset( $_GET['categoria'] ) && isset( $_GET['acao'] ) )
		{
			if( $_GET['acao'] )
			{
				$_SESSION['menu_opt'][$_GET['categoria']] = 1;
				$_SESSION['menu_atual'] = $_GET['categoria'];
			}
			else
			{
				unset( $_SESSION['menu_opt'][$_GET['categoria']] );
				if($_SESSION['menu_atual'] == $_GET['categoria'])
				{
					unset( $_SESSION['menu_atual']);
				}
			}
			$db = new clsBanco();
			if( isset( $_SESSION['id_pessoa'] ) )
			{
				$db->Consulta( "UPDATE funcionario SET opcao_menu = '" . serialize( $_SESSION['menu_opt'] ) . "' WHERE ref_cod_pessoa_fj = '" . $_SESSION['id_pessoa'] . "'" );
			}
		}
		session_write_close();
	}

	function Logado()
	{
		return $this->logado;
	}

	function Logar($acao)
	{
		if ($acao)
		{
			
//			$login =  ereg_replace( "^0+([0-9]+)\$", "\\1",@$_POST['login'] );
			$login = @$_POST['login'];
			$senha = md5( @$_POST['senha'] );
			$db = new clsBanco();

			$db->Consulta( "SELECT ref_cod_pessoa_fj FROM funcionario WHERE matricula = '{$login}'" );
			if ($db->ProximoRegistro())
			{
				list($idpes) = $db->Tupla();
				
				// padrao: meia hora atraz

				$intervalo = date("Y-m-d H:i", time() - ( 60 * 1 ) );

				// se houve o ultimo login bem sucedido foi em menos de meia hora, conta somente dali para a frente
				$db->consulta("SELECT data_hora FROM acesso WHERE cod_pessoa = '{$idpes}' AND data_hora > '{$intervalo}' AND sucesso = 't' ORDER BY data_hora DESC LIMIT 1" );
				if( $db->Num_Linhas() )
				{
					$db->ProximoRegistro();
					list( $intervalo ) = $db->Tupla();
				}

				$tentativas = $db->CampoUnico("SELECT COUNT(0) FROM acesso WHERE cod_pessoa = '{$idpes}' AND data_hora > '{$intervalo}' AND sucesso = 'f'" );
				if( $tentativas > 5 )
				{
					$hora_ultima_tentativa = $db->CampoUnico("SELECT data_hora FROM acesso WHERE cod_pessoa = '{$idpes}' ORDER BY data_hora DESC LIMIT 1 OFFSET 4" );
					$hora_ultima_tentativa = explode(".",$hora_ultima_tentativa);
					$hora_ultima_tentativa = $hora_ultima_tentativa[0];

					$data_libera = date("d/m/Y H:i", strtotime($hora_ultima_tentativa) + ( 60 * 30));

					die( "<html><body></body><script>alert( 'Houveram mais de 5 tentativas frustradas de acessar a sua conta na última meia hora.\\nPor segurança sua conta ficará interditada até: {$data_libera}' );document.location.href='http://ieducar.dccobra.com.br/intranet';</script></html>" );
				}

				$db->Consulta( "SELECT ref_cod_pessoa_fj, opcao_menu, ativo, tempo_expira_senha, tempo_expira_conta, data_troca_senha, data_reativa_conta, proibido, ref_cod_setor_new, tipo_menu FROM funcionario WHERE ref_cod_pessoa_fj = '{$idpes}' AND senha = '{$senha}'" );
				if ($db->ProximoRegistro())
				{
					list ($id_pessoa, $opcaomenu, $ativo, $tempo_senha, $tempo_conta, $data_senha, $data_conta, $proibido, $setor_new, $tipo_menu ) = $db->Tupla();
					if( ! $proibido )
					{
						if( $ativo )
						{
							// usuario ativo, vamos ver se nao expirou a conta dele
							$expirada = false;
							if( ! empty( $tempo_conta ) && !empty( $data_conta ) )
							{
								if( time() - strtotime( $data_conta ) > $tempo_conta * 60 * 60 * 24 )
								{
									// conta expirada, avisa a falar com admin
									$db->Consulta( "UPDATE funcionario SET ativo='0' WHERE ref_cod_pessoa_fj = '$id_pessoa'" );
									die( "<html><body></body><script>alert( 'Sua conta na intranet expirou.\nContacte um administrador para reativa-la.' );document.location.href='http://ieducar.dccobra.com.br/intranet';</script></html>" );
								}
							}
							// vendo se a senha nao expirou
							if( ! empty( $tempo_senha ) && ! empty( $data_senha ) )
							{
								if( time() - strtotime( $data_senha ) > $tempo_senha * 60 * 60 * 24 )
								{
									// senha expirada, manda pra mudanca de senha
									die( "<html><body><form id='reenvio' name='reenvio' action='usuario_trocasenha.php' method='POST'><input type='hidden' name='cod_pessoa' value='{$id_pessoa}'></form></body><script>document.getElementById('reenvio').submit();</script></html>" );
								}
							}

							//verificação do ip da máquina
							if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '' )
							{
								$ip_maquina = $_SERVER['HTTP_X_FORWARDED_FOR'];
							}
							else 
								$ip_maquina = $_SERVER['REMOTE_ADDR'];
														
							$sql = "SELECT ip_logado, data_login FROM funcionario WHERE ref_cod_pessoa_fj = {$id_pessoa}";
							$db2 = new clsBanco();
							$db2->Consulta($sql);
							while ($db2->ProximoRegistro())
							{
								list($ip_banco, $data_login) = $db2->Tupla();
								if ($ip_banco)
								{
									if (abs(time() - strftime("now") - strtotime($data_login)) <= 10 * 60 && $ip_banco != $ip_maquina)
									{
										die( "<html><body></body><script>alert( 'Conta já em uso.\\nTente novamente mais tarde' );document.location.href='http://ieducar.dccobra.com.br/intranet';</script></html>" );
									}
									else 
									{
										$sql = "UPDATE funcionario SET data_login = NOW() WHERE ref_cod_pessoa_fj = {$id_pessoa}";
										$db2->Consulta($sql);
									}
								}
								else
								{
									$sql = "UPDATE funcionario SET ip_logado = '{$ip_maquina}', data_login = NOW() WHERE ref_cod_pessoa_fj = {$id_pessoa}";
									$db2->Consulta($sql);
								}
							}
							
							// se chegou aki nao tah expirada, pode logar
							@session_start();
							$_SESSION = array();
							$_SESSION['itj_controle'] = "logado";
							$_SESSION['id_pessoa'] = $id_pessoa;
							$_SESSION['pessoa_setor'] = $setor_new;
							$_SESSION['menu_opt'] = unserialize( $opcaomenu );
							$_SESSION['tipo_menu'] = $tipo_menu;
							@session_write_close();
							$this->logado = true;
							
							
							
						}
						else
						{
							if( ! empty( $tempo_conta ) && ! empty( $data_conta ) )
							{
								if( time() - strtotime( $data_conta ) > $tempo_conta * 60 * 60 * 24 )
								{
									$this->erroMsg = "Sua conta na intranet expirou. Contacte um administrador para reativa-la.";
									$expirada = 1;
								}
								else
								{
									$this->erroMsg = "Sua conta n&atilde;o est&aacute; ativa. Use a op&ccedil;&atilde;o 'Nunca usei a intrenet'.";
									$expirada = 0;
								}
							}
						}
					}
					else
					{
						$this->erroMsg = "Imposs&iacute;vel realizar login.";
						$this->logado = false;
					}
				}
				else
				{
					$ip = empty($_SERVER['REMOTE_ADDR']) ? "NULL" : $_SERVER['REMOTE_ADDR'];
					$ip_de_rede = empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? "NULL" : $_SERVER['HTTP_X_FORWARDED_FOR'];
					$db->Consulta( "INSERT INTO acesso (data_hora, ip_externo, ip_interno, cod_pessoa, sucesso) VALUES (now(), '{$ip}', '{$ip_de_rede}',  {$idpes}, 'f')" );

					$this->erroMsg = "Login ou Senha incorretos.";
					$this->logado = false;
				}
			}
			else
			{
				$this->erroMsg = "Login ou Senha incorretos.";
				$this->logado = false;
			}
		}
		else
		{
			$arquivo = "templates/nvp_htmlloginintranet.tpl";
			$ptrTpl = fopen($arquivo, "r");
			$strArquivo = fread($ptrTpl, filesize($arquivo));
			if( $this->erroMsg )
			{
				$strArquivo = str_replace( "<!-- #&ERROLOGIN&# -->", $this->erroMsg, $strArquivo );
			}
			fclose ($ptrTpl);
			//echo $strArquivo;
			//print_r( $_SESSION );
			die( $strArquivo );
		}
	}

	function obriga_Login()
	{
		if ($_POST['login'] && $_POST['senha'])
		{
			$this->logar(true);
		}
		if (!$this->logado)
		{
			$this->logar(false);
		}
	}
}
?>
