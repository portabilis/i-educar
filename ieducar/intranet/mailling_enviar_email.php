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

class clsIndex extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Contratos!" );
		$this->processoAp = "87";
	}
}

class indice extends clsCadastro
{
	var $remetente_nome;
	var $remetente_email;
	var $destino_nome;
	var $destino_email;
	var $assunto;
	var $pagina_anterior;
	var $id_pessoa;
	
	function Inicializar()
	{
		@session_start();
		$this->id_pessoa = $_SESSION['id_pessoa'];
		session_write_close();
		$retorno = "Novo";
		$this->pagina_anterior = $_SERVER["HTTP_REFERER"];
		return $retorno;
	}
	
	function Gerar()
	{
		$this->campoOculto( "id_pessoa", $this->id_pessoa);
		$this->campoTexto( "remetente_nome", "Nome Remetente", $this->remetente_nome, "40", "40", true );
		$this->campoTexto( "remetente_email", "Email Remetente", $this->remetente_email, "40", "40", true );
		$this->campoTexto( "assunto", "Assunto", $this->assunto, "40", "40", true );		

		$db = new clsBanco;
		$db->Consulta( "SELECT nm_grupo, cod_mailling_grupo FROM mailling_grupo" );
		while( $db->ProximoRegistro() )
		{
			list($nome, $cod_grupo) = $db->Tupla();
			$rt = false;
			$this->campoCheck("gr_{$cod_grupo}", "Grupos", $rt, $nome);
		}
		$this->campoCheck("servidores", "Grupos", false, "Servidores da Prefeitura");	
		$hoje = date('Y-m-d', time());
		$amanha = date('Y-m-d', time()+ 86400);
		$db->Consulta( "SELECT titulo, cod_not_portal FROM not_portal WHERE data_noticia  >= '$hoje' AND data_noticia <= '$amanha' ORDER BY cod_not_portal DESC  " );
		while ($db->ProximoRegistro())
		{
			list($titulo, $cod_not) = $db->Tupla();
			$rt = false;
			$this->campoCheck("nt_{$cod_not}", "Notícias", $rt, $titulo);
		}		
		return true;
	}
	
	function Novo() 
	{
		$pagina_anterior = urldecode($this->pagina_anterior);
		$db = new clsBanco();
		$grupos = array();
		foreach ($_POST as $cod=>$val)
		{
			if(substr($cod,0,3) == "nt_")
			{
				$id_not = substr($cod,3);
				$db->Consulta("SELECT titulo, descricao FROM not_portal WHERE cod_not_portal={$id_not}");
				while($db->ProximoRegistro())
				{
					list($titulo,$descricao) = $db->Tupla();
					if(strlen($descricao) > 300)
						$descricao = substr($descricao,0,300)."...<a href='http://www.itajai.sc.gov.br/noticias_det.php?id_noticia={$id_not}'>Leia Mais</a>";
						$descricao = str_replace("\n\r", "<br>", $descricao);
						$descricao = str_replace("\n", "<br>", $descricao);
						$msg_nots .= "<span class=\"titulo\">{$titulo}</span><br><br>\n$descricao<br><br>\n"; 
				}
				$id_not_temp[] = $id_not; 	
			}
			if(substr($cod,0,3) == "gr_")
			{
				// monta o array com os grupos selecionados
				$id_grupo = substr($cod,3);
				$grupos[$id_grupo] = $id_grupo;
				
				$id_grupo_temp[] = $id_grupo;	
			}
			if($cod == "servidores")
			{
				// pega o email dos funcionarios
				$db->Consulta("SELECT DISTINCT( email ), ref_ref_cod_pessoa_fj FROM funcionario_email ORDER BY email");
				while ($db->ProximoRegistro())
				{
					list ( $email, $ref_pessoa ) = $db->Tupla();
					$email = str_replace( " ", "", $email );
					$email = str_replace( "\n", "", $email );
					$email = str_replace( "\r", "", $email );
					$email = strtolower( $email );
					
					$email .= "@itajai.sc.gov.br";
					$destino[$email] = array();
					$destino[$email]["tipo"] = 1;
					$destino[$email]["cod"] = $ref_pessoa;
				}
			}
		}
		
		if( is_array( $grupos ) && count( $grupos ) )
		{
			// pega os emails dos grupos selecionados
			$db->Consulta( "SELECT DISTINCT( email ), cod_mailling_email FROM mailling_email, mailling_grupo_email WHERE ref_cod_mailling_grupo IN ( " . implode( ", ", $grupos ) . " ) AND ref_cod_mailling_email=cod_mailling_email" );
			while($db->ProximoRegistro())
			{
				list( $email, $cod_email ) = $db->Tupla();
				$email = str_replace( " ", "", $email );
				$email = str_replace( "\n", "", $email );
				$email = str_replace( "\r", "", $email );
				$email = strtolower( $email );
				
				$destino[$email] = array();
				$destino[$email]["tipo"] = 0;
				$destino[$email]["cod"] = $cod_email;
			}
		}
		
		foreach ($id_grupo_temp as $id_grupo)
		{
			foreach ($id_not_temp as $id_not)
			{
				$db->Consulta("INSERT INTO mailling_historico (ref_cod_not_portal,ref_cod_mailling_grupo, ref_ref_cod_pessoa_fj, data_hora) VALUES ({$id_not},{$id_grupo},{$this->id_pessoa},NOW())");
			}
			
		}

		$msg_nots = str_replace( "'", "\\'", $msg_nots );
		$this->assunto = str_replace( "'", "\\'", $this->assunto );
		$this->remetente_nome = str_replace( "'", "\\'", $this->remetente_nome );
		$db->Consulta( "INSERT INTO mailling_email_conteudo( ref_ref_cod_pessoa_fj, conteudo, assunto, nm_remetente, email_remetente ) VALUES( '{$this->id_pessoa}', '{$msg_nots}', '{$this->assunto}', '{$this->remetente_nome}', '{$this->remetente_email}' ) " );
		$cod_conteudo = $db->InsertId("mailling_email_conteudo_cod_mailling_email_conteudo_seq");
		
		foreach ($destino as $email=> $dados )
		{
			if( $dados["tipo"] )
			{
				$db->Consulta( "INSERT INTO mailling_fila_envio( ref_cod_mailling_email_conteudo, ref_ref_cod_pessoa_fj, data_cadastro ) VALUES( $cod_conteudo, {$dados["cod"]}, NOW() )" );
			}
			else 
			{
				$db->Consulta( "INSERT INTO mailling_fila_envio( ref_cod_mailling_email_conteudo, ref_cod_mailling_email, data_cadastro ) VALUES( $cod_conteudo, {$dados["cod"]}, NOW() )" );
			}
		}
		
		header( "location: mailling_enviar_email.php" ); 
		die();
		return true;
	}
}
	
$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();
?>
<script>
document.getElementById("remetente_nome").focus();
</script>