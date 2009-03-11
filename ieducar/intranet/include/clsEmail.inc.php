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
class clsEmail
{
	var $remetente;
	var $remetenteNome;
	var $replyto;
	var $destinatarios;
	var $assunto;
	var $conteudo;
	var $template;
	var $footer;
	var $tipo;
	var $compilado;
	var $conteudoCompilado;
	var $charset = "iso-8859-1";
	
	function  clsEmail( $destinatarios, $assunto, $conteudo, $template=false, $remetente=false, $remetenteNome=false, $replyto=false, $tipo="html", $strFooter=false )
	{
		$this->compilado = false;
		$this->destinatarios = $destinatarios;
		$this->assunto = $assunto;
		$this->conteudo = $conteudo;
		$this->footer = $strFooter;
		$this->template = ( $template )? $template : "email_padrao";
		$this->remetente = ( $remetente )? $remetente: "sistema@itajai.sc.gov.br";
		$this->remetenteNome = ( $remetenteNome ) ? $remetenteNome: "Sistema - Itajai.sc.gov.br";
		$this->replyto = ( $replyto )? $replyto: $remetente;
		$this->tipo = ( $tipo == "html" )? "text/html": "text/plain";
	}
	
	function compilar()
	{
		if( $this->tipo != "text/html" )
		{
			$this->conteudoCompilado = $this->conteudo;
		}
		else 
		{
			$arquivo = "templates/{$this->template}.tpl";
			$ptrTpl = fopen( $arquivo, "r");
			$strArquivo = fread($ptrTpl, filesize($arquivo));
			fclose ($ptrTpl);
			$strArquivo = str_replace( "<!-- #&CONTEUDO&# -->", $this->conteudo, $strArquivo );
			$strArquivo = str_replace( "<!-- #&ASSUNTO&# -->", $this->assunto, $strArquivo );
			if( $this->footer )
			{
				$strArquivo = str_replace( "<!-- #&FOOTER&# -->", $this->footer, $strArquivo );
			}
			$this->conteudoCompilado = $strArquivo;
		}
		$this->compilado = true;
	}
	
	function envia()
	{
		if( ! $this->compilado ) $this->compilar();
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: {$this->tipo}; charset={$this->charset}\n";
		$headers .= "X-Priority: 3\n";
		$headers .= "X-MSMail-Priority: Normal\n";
		$headers .= "X-Mailer: php/" . phpversion() . "\n";
		$headers .= "From: \"{$this->remetenteNome}\" <{$this->remetente}>\n";
		$headers .= "Reply-To: {$this->replyto}\n";
		
		if( is_array( $this->destinatarios ) )
		{
			$this->destinatarios = implode( ",", $this->destinatarios );
		}
		$this->destinatarios = str_replace( " ", ",", $this->destinatarios );
		
		$ok = mail( $this->destinatarios, $this->assunto, $this->conteudoCompilado, $headers );
		return $ok;
	}
	
	function addDestinatario( $email )
	{
		if( is_array( $this->destinatarios ) )
		{
			$this->destinatarios[] = $email;
		}
		else 
		{
			if( $this->destinatarios != "" )
			{
				$this->destinatarios .= ",{$email}";
			}
			else 
			{
				$this->destinatarios = $email;
			}
		}
	}
}
?>