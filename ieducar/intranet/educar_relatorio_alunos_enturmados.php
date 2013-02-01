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
require_once ("include/relatorio.inc.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Rela&ccedil;&atilde;o dos Alunos Enturmados" );
		$this->processoAp = "684";
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

	var $ref_cod_instituicao;
	var $ref_cod_escola;
	var $ref_ref_cod_escola;
	var $ref_cod_curso;
	var $ref_ref_cod_serie;
	var $ref_cod_turma;

	var $ano;
	var $link;

	function Inicializar()
	{
		$retorno = "Novo";

		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		return $retorno;
	}

	function Gerar()
	{
		$get_escola = true;
		$get_curso = true;
		$get_escola_curso_serie = true;
		$get_turma = true;

		//$get_serie = true;
		//$get_escola_serie = true;
		$exibe_get_serie = true;
		$this->campoNumero("ano", "Ano", date("Y"), 4, 4, true);
		include("include/pmieducar/educar_campo_lista.php");

		if ( $this->ref_cod_escola )
		{
			$this->ref_ref_cod_escola = $this->ref_cod_escola;
		}
		$this->acao_enviar = false;

		$this->array_botao = array( "Gerar Relat&oacute;rio" );
		$this->array_botao_url_script = array( "valida();" );
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

document.getElementById('ref_cod_escola').onchange = function()
{
	getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{
	getEscolaCursoSerie();
	getSerie();
}

document.getElementById('ref_ref_cod_serie').onchange = function()
{
	getTurma();
}

function valida() {
	showExpansivelImprimir(400, 200,  'educar_relatorio_alunos_enturmados_.php',['ref_cod_escola', 'ref_cod_curso', 'ref_ref_cod_serie', 'ref_cod_turma', 'ano'], 'Relatório i-Educar' )
}

</script>