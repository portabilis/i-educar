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
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once ("include/clsPDF.inc.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Histórico Escolar" );
		$this->processoAp = "999200";
	}
}

class indice extends clsCadastro
{

	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	var $ref_cod_escola;
	var $ref_cod_aluno;
	var $nm_aluno;
	var $nm_aluno_;

	var $ano;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		return $retorno;
	}

	function Gerar()
	{
		@session_start();
			$pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$nivel_acesso = $obj_permissoes->nivel_acesso( $pessoa_logada );
		if( $nivel_acesso == 1 || $nivel_acesso == 2 )
		{
			$obrigatorio = true;
			$get_escola = true;
			include("include/pmieducar/educar_campo_lista.php");
		}
		else
		{
			$this->ref_cod_escola = $obj_permissoes->getEscola( $pessoa_logada );
			$this->campoOculto("ref_cod_escola", $this->ref_cod_escola);
		}

		$this->nm_aluno = $this->nm_aluno_;

		$this->campoTexto("nm_aluno", "Aluno", $this->nm_aluno, 30, 255, true, false, false, "", "<img border=\"0\" onclick=\"pesquisa_aluno();\" id=\"ref_cod_aluno_lupa\" name=\"ref_cod_aluno_lupa\" src=\"imagens/lupa.png\"\/>","","",true);
		$this->campoOculto("nm_aluno_", $this->nm_aluno_);
		$this->campoOculto("ref_cod_aluno", $this->ref_cod_aluno);
		$this->acao_enviar = false;

		//$this->array_botao = array( "Gerar Relat&oacute;rio" );
		//this->array_botao_url_script = array( "document.formcadastro.action = 'portabilis_historico_escolar_proc.php'" );
		$this->acao_enviar = 'acao2()';
		$this->acao_executa_submit = false;
		
		$this->url_cancelar = "educar_index.php";
		$this->nome_url_cancelar = "Cancelar";
		
	}

	function Novo()
	{
		return true;
	}
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>
<script>

function pesquisa_aluno()
{
	pesquisa_valores_popless('educar_pesquisa_aluno.php?ref_cod_escola='+document.getElementById('ref_cod_escola').value)
}

var func = function(){document.getElementById('btn_enviar').disabled= false;};
if( window.addEventListener ) {
		//mozilla
	  document.getElementById('btn_enviar').addEventListener('click',func,false);
	} else if ( window.attachEvent ) {
		//ie
	  document.getElementById('btn_enviar').attachEvent('onclick',func);
	}
function acao2()
{
    document.formcadastro.target = '_blank';
	document.getElementById( 'btn_enviar' ).disabled =false;
	document.formcadastro.submit();
}

// Chamado do arquivo que ira processar o relatorio
document.formcadastro.action = 'portabilis_historico_escolar_proc.php';
  
</script>