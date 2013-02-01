<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itajaï¿½								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software Pï¿½blico Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itajaï¿½			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  ï¿½  software livre, vocï¿½ pode redistribuï¿½-lo e/ou	 *
	*	modificï¿½-lo sob os termos da Licenï¿½a Pï¿½blica Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a versï¿½o 2 da	 *
	*	Licenï¿½a   como  (a  seu  critï¿½rio)  qualquer  versï¿½o  mais  nova.	 *
	*																		 *
	*	Este programa  ï¿½ distribuï¿½do na expectativa de ser ï¿½til, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia implï¿½cita de COMERCIALI-	 *
	*	ZAï¿½ï¿½O  ou  de ADEQUAï¿½ï¿½O A QUALQUER PROPï¿½SITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licenï¿½a  Pï¿½blica  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Vocï¿½  deve  ter  recebido uma cï¿½pia da Licenï¿½a Pï¿½blica Geral GNU	 *
	*	junto  com  este  programa. Se nï¿½o, escreva para a Free Software	 *
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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Boletim" );
		$this->processoAp = "830";
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

	var $ano;
	var $mes;

	var $nm_escola;
	var $nm_instituicao;
	var $ref_cod_curso;
	var $sequencial;
	var $pdf;
	var $pagina_atual = 1;
	var $total_paginas = 1;
	var $nm_professor;
	var $nm_turma;
	var $nm_serie;
	var $nm_disciplina;
	var $curso_com_exame = 0;
	var $ref_cod_matricula;

	var $page_y = 135;

	var $nm_aluno;
	var $array_modulos = array();
	var $nm_curso;
	var $get_link = false;

	var $total;

	var $ref_cod_modulo;

	var $meses_do_ano = array(
							 "1" => "JANEIRO"
							,"2" => "FEVEREIRO"
							,"3" => "MAR&Ccedil;O"
							,"4" => "ABRIL"
							,"5" => "MAIO"
							,"6" => "JUNHO"
							,"7" => "JULHO"
							,"8" => "AGOSTO"
							,"9" => "SETEMBRO"
							,"10" => "OUTUBRO"
							,"11" => "NOVEMBRO"
							,"12" => "DEZEMBRO"
						);


	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();

		return $retorno;
	}

	function Gerar()
	{

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

		if($_POST){
			foreach ($_POST as $key => $value) {
				$this->$key = $value;

			}
		}

		$this->ano = $ano_atual = date("Y");
		$this->mes = $mes_atual = date("n");
	
		$get_escola = true;
		$exibe_nm_escola = true;
		$get_curso = true;
		$get_escola_curso_serie = true;
		$escola_obrigatorio = false;
		$instituicao_obrigatorio = true;
//		$get_semestre = true;
		$this->campoNumero("ano", "Ano", $this->ano, 4, 4, true);
		include("include/pmieducar/educar_campo_lista.php");
		$this->campoLista("ref_cod_turma","Turma",array('' => 'Selecione'),'','','','','', '', false);

		if($this->ref_cod_escola)
			$this->ref_ref_cod_escola = $this->ref_cod_escola;
		$this->campoLista( "ref_cod_matricula", "Aluno",array(''=>'Selecione'), "","",false,"","",false,false );
		if($this->get_link)
			$this->campoRotulo("rotulo11", "-", "<a href='$this->get_link' target='_blank'>Baixar Relatï¿½rio</a>");

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

document.getElementById('ref_cod_instituicao').onchange = function ()
{
	getAluno();
	getDuploEscolaCurso();
}

document.getElementById('ref_cod_escola').onchange = function()
{
	//setMatVisibility();
	getEscolaCurso();
	var campoTurma = document.getElementById( 'ref_cod_turma' );
	getAluno();
	getTurmaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{
	getEscolaCursoSerie();
	getAluno();
	getTurmaCurso();
}

document.getElementById('ref_ref_cod_serie').onchange = function()
{

	getAluno();
	
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
		campoTurma.options[0] = new Option( 'A sïérie não possui nenhuma turma', '', false, false );
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

document.getElementById('ref_cod_turma').onchange = function()
{
	getAluno();
	var This = this;

}

function setMatVisibility()
{
	var campoTurma = document.getElementById('ref_cod_turma');
	var campoAluno = document.getElementById('ref_cod_matricula');

	campoAluno.length = 1;

	if (campoTurma.value == '')
	{
		setVisibility('tr_ref_cod_matricula',false);
		setVisibility('ref_cod_matricula',false);
	}
	else
	{
		setVisibility('tr_ref_cod_matricula',true);
		setVisibility('ref_cod_matricula',true);
	}
}
function getAluno()
{
	
	var campoInst = document.getElementById('ref_cod_instituicao').value;
	var campoEscola = document.getElementById('ref_cod_escola').value;
	var campoCurso = document.getElementById('ref_cod_curso').value;
	var campoSerie = document.getElementById('ref_ref_cod_serie').value;
	var campoTurma = document.getElementById('ref_cod_turma').value;

	var xml1 = new ajax(getAluno_XML);
	strURL = "educar_alunos_beneficios_xml.php?inst="+campoInst+"&esc="+campoEscola+"&curso="+campoCurso+"&serie="+campoSerie+"&turma="+campoTurma;

	xml1.envia(strURL);
}

function getAluno_XML(xml)
{
	var aluno = xml.getElementsByTagName( "matricula" );
	var campoTurma = document.getElementById( 'ref_cod_turma' );
	var campoAluno = document.getElementById('ref_cod_matricula');

	campoAluno.length = 1;

	for ( var j = 0; j < aluno.length; j++ )
	{

		campoAluno.options[campoAluno.options.length] = new Option( aluno[j].firstChild.nodeValue, aluno[j].getAttribute('cod_matricula'), false, false );

	}
}


setVisibility('tr_ref_cod_matricula',true);
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

	showExpansivelImprimir(400, 200,'',[], "Boletim");

	document.formcadastro.target = 'miolo_'+(DOM_divs.length-1);

	document.getElementById( 'btn_enviar' ).disabled =false;

	document.formcadastro.submit();

}

document.getElementById('formcadastro').action ='educar_relatorio_alunos_beneficios_proc.php';

</script>