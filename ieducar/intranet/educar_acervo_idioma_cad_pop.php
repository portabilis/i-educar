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

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Idioma" );
		$this->processoAp = "590";
		$this->renderMenu = false;
		$this->renderMenuSuspenso = false;
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

	var $cod_acervo_idioma;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_idioma;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_biblioteca;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();


		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 590, $this->pessoa_logada, 11,  "educar_acervo_idioma_lst.php" );

		return $retorno;
	}

	function Gerar()
	{
		echo "<script>window.onload=function(){parent.EscondeDiv('LoadImprimir')}</script>";
		// primary keys
		$this->campoOculto( "cod_acervo_idioma", $this->cod_acervo_idioma );
		$this->campoOculto("ref_cod_biblioteca", $this->ref_cod_biblioteca);
		// text
		$this->campoTexto( "nm_idioma", "Idioma", $this->nm_idioma, 30, 255, true );

		// data

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 590, $this->pessoa_logada, 11,  "educar_acervo_idioma_lst.php" );


		$obj = new clsPmieducarAcervoIdioma( $this->cod_acervo_idioma, $this->pessoa_logada, $this->pessoa_logada, $this->nm_idioma, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->ref_cod_biblioteca );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			echo "<script>
					parent.document.getElementById('idioma').value = '$cadastrou';
					parent.document.getElementById('tipoacao').value = '';
					parent.document.getElementById('formcadastro').submit();
			     </script>";
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarAcervoIdioma\nvalores obrigatorios\nis_numeric( $this->ref_usuario_cad ) && is_string( $this->nm_idioma )\n-->";
		return false;
	}

	function Editar()
	{
	}

	function Excluir()
	{
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
	document.getElementById('ref_cod_biblioteca').value = parent.document.getElementById('ref_cod_biblioteca').value;
</script>