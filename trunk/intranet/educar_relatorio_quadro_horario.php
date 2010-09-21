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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Quadro Hor&aacute;rios" );
		$this->processoAp = "835";
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
	var $ref_cod_serie;
	var $ref_cod_turma;

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


		$this->ano = $ano_atual = date("Y");
		$this->mes = $mes_atual = date("n");

		$this->campoNumero( "ano", "Ano", $this->ano, 4, 4, true );

		$get_escola = true;
		$exibe_nm_escola = true;
		$get_escola_curso = true;
		$get_curso = true;
		$get_escola_curso_serie = true;
		$escola_obrigatorio = true;
		$curso_obrigatorio = true;
		$get_turma = false;
		$instituicao_obrigatorio = true;

		include("include/pmieducar/educar_campo_lista.php");


		$this->campoLista("ref_cod_turma","Turma",array('' => 'Selecione'),'',"",false,"","",false,false);

		if($this->ref_cod_escola)
			$this->ref_ref_cod_escola = $this->ref_cod_escola;

		$this->url_cancelar = "educar_index.php";
		$this->nome_url_cancelar = "Cancelar";

		$this->acao_enviar = 'acao2()';
		$this->acao_executa_submit = false;

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
	var campoTurma = document.getElementById( 'ref_cod_turma' );
	getTurmaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{

	getEscolaCursoSerie();
	getTurmaCurso();
}


document.getElementById('ref_ref_cod_serie').onchange = function()
{

	var campoEscola = document.getElementById( 'ref_cod_escola' ).value;
	var campoSerie = document.getElementById( 'ref_ref_cod_serie' ).value;

	var xml1 = new ajax(getTurma_XML);
	strURL = "educar_turma_xml.php?esc="+campoEscola+"&ser="+campoSerie;
	xml1.envia(strURL);
}

function getTurma_XML(xml)
{

	var campoSerie = document.getElementById( 'ref_ref_cod_serie' ).value;

	var campoTurma = document.getElementById( 'ref_cod_turma' );

	var turma = xml.getElementsByTagName( "turma" );

	campoTurma.length = 1;
	campoTurma.options[0] = new Option( 'Selecione uma Turma', '', false, false );
	for ( var j = 0; j < turma.length; j++ )
	{

		campoTurma.options[campoTurma.options.length] = new Option( turma[j].firstChild.nodeValue, turma[j].getAttribute('cod_turma'), false, false );

	}
	if ( campoTurma.length == 1 && campoSerie != '' ) {
		campoTurma.options[0] = new Option( 'A série não possui nenhuma turma', '', false, false );
	}

}

function getTurmaCurso()
{
	var campoCurso = document.getElementById('ref_cod_curso').value;
	var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

	var xml1 = new ajax(getTurmaCurso_XML);
	strURL = "educar_turma_xml.php?ins="+campoInstituicao+"&cur="+campoCurso;

	xml1.envia(strURL);
}

function getTurmaCurso_XML(xml)
{
	var turma = xml.getElementsByTagName( "turma" );
	var campoTurma = document.getElementById( 'ref_cod_turma' );
	var campoCurso = document.getElementById('ref_cod_curso');

	campoTurma.length = 1;
	campoTurma.options[0] = new Option( 'Selecione uma Turma', '', false, false );

	for ( var j = 0; j < turma.length; j++ )
	{

		campoTurma.options[campoTurma.options.length] = new Option( turma[j].firstChild.nodeValue, turma[j].getAttribute('cod_turma'), false, false );

	}

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

	if(!acao())
		return;

	showExpansivelImprimir(400, 200,'',[], "Quadro Horarios");

	document.formcadastro.target = 'miolo_'+(DOM_divs.length-1);

	document.getElementById( 'btn_enviar' ).disabled =false;


	document.formcadastro.submit();

}

document.formcadastro.action = 'educar_relatorio_quadro_horario_proc.php';
</script>
