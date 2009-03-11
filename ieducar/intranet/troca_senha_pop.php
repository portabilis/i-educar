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
		$this->SetTitulo( "Prefeitura de Itaja&iacute;- Usu&aacute;rios!" );
		$this->processoAp = "0";
		$this->renderBanner = false;
		$this->renderMenu = false;
		$this->renderMenuSuspenso = false;
	}
}

class indice extends clsCadastro
{
//	var $p_cod_pessoa_fj, $p_nm_pessoa, $p_id_federal, $idtlog, $p_endereco, $p_cep, $p_ref_bairro, $p_ddd_telefone_1, $p_telefone_1, $p_ddd_telefone_2, $p_telefone_2, $p_ddd_telefone_mov, $p_telefone_mov, $p_ddd_telefone_fax, $p_telefone_fax, $p_email, $p_http, $p_tipo_pessoa, $p_sexo;
//	var $f_matricula, $f_senha, $f_ativo, $f_ref_sec, $f_ramal, $f_ref_dept, $f_ref_setor, $ref_cod_funcionario_vinculo, $bloco, $apartamento, $andar, $ref_cod_setor;

//	var $confere_senha;

	var $p_cod_pessoa_fj;
	var $f_senha;
	var $f_senha2;
//	var $confere_senha_ant;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->p_cod_pessoa_fj = @$_SESSION['id_pessoa'];
		@session_write_close();
		$objPessoa = new clsPessoaFj();
		$db = new clsBanco();
		$db->Consulta( "SELECT f.senha FROM funcionario f WHERE f.ref_cod_pessoa_fj={$this->p_cod_pessoa_fj}" );
		if ($db->ProximoRegistro())
		{
			list($this->f_senha) = $db->Tupla();
//			$this->f_senha = $this->confere_senha;
		}
		$this->acao_enviar = "acao2()";
		return $retorno;
	}

	function null2empityStr( $vars )
	{
		foreach ( $vars AS $key => $valor )
		{
			$valor .= "";
			if( $valor == "NULL" )
			{
				$vars[$key] = "";
			}
		}
		return $vars;
	}

	function Gerar()
	{
		@session_start();
		$this->campoOculto( "p_cod_pessoa_fj", $this->p_cod_pessoa_fj );
		$this->cod_pessoa_fj = $this->p_cod_pessoa_fj;

		if (empty($_SESSION['convidado']))
		{
			$this->campoRotulo("", "<strong>Informações</strong>", "<strong>Sua senha expirará em alguns dias, por favor cadastre uma nova senha com no mínimo 8 caracteres e diferente da senha anterior</strong>");
			$this->campoSenha( "f_senha", "Senha",  "", true, "A sua nova senha deverá conter pelo menos oito digitos");
			$this->campoSenha( "f_senha2", "Redigite a Senha", $this->f_senha2, true);
		}

	}
	
	function Novo() 
	{
		@session_start();
		$pessoaFj = $_SESSION['id_pessoa'];
		session_write_close();
		$sql = "SELECT ref_cod_pessoa_fj FROM funcionario WHERE md5('{$this->f_senha}') = senha AND ref_cod_pessoa_fj = {$this->p_cod_pessoa_fj}";
		$db = new clsBanco();
		$senha_igual = $db->CampoUnico($sql);
		if ($this->f_senha && !$senha_igual)
		{
			$sql_funcionario = "UPDATE funcionario SET senha=md5('{$this->f_senha}'), data_troca_senha = NOW(), tempo_expira_senha = 30 WHERE ref_cod_pessoa_fj={$this->p_cod_pessoa_fj}";
			$db->Consulta( $sql_funcionario );
			echo "<script>
						window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
						window.parent.location = 'index.php';
				  </script>";
			return true;
			die();
		}
		$this->mensagem .= "A sua nova senha deverá ser diferente da anterior";
		return false;
	}

	function Editar()
	{
		/*@session_start();
		$pessoaFj = $_SESSION['id_pessoa'];
		session_write_close();

		if ($this->f_senha != $this->confere_senha)
		{
			$sql_funcionario = "UPDATE funcionario SET senha=md5('{$this->f_senha}'), data_troca_senha = NOW(), ref_cod_funcionario_vinculo='{$this->ref_cod_funcionario_vinculo}', $sql ramal='{$this->f_ramal}', ref_ref_cod_pessoa_fj='{$pessoaFj}', tempo_expira_senha = 30 WHERE ref_cod_pessoa_fj={$this->p_cod_pessoa_fj}";
		}
		return true;*/
	}

}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();
?>

<script>

function acao2()
{
	if ($F('f_senha').length > 7)
	{
		if ($F('f_senha') == $F('f_senha2'))
		{
			acao();	
		}
		else
		{
			alert('As senhas devem ser iguais');		
		}
	}
	else
	{
		alert('A sua nova senha deverá conter pelo menos oito digitos');
	}
}

</script>