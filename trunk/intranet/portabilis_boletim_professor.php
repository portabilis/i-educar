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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Boletim do Professor" );
		$this->processoAp = "999205";
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

	var $pdf;
	var $ref_cod_turma;
	var $ref_cod_matricula;

	var $page_y = 139;



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
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		if($_POST){
			foreach ($_POST as $key => $value) {
				$this->$key = $value;

			}
		}

		if($this->ref_cod_escola)
			$this->ref_ref_cod_escola = $this->ref_cod_escola;

		$get_escola = true;
		$get_curso = true;
		$get_escola_curso_serie = true;
		$get_turma = true;
		$sem_padrao = true;
		$instituicao_obrigatorio = true;
		$escola_obrigatorio = true;
		$escola_curso_serie_obrigatorio = true;
		$curso_obrigatorio = true;
		
		$this->ano = $ano_atual = date("Y");
		
		//campo adicionado para pegar por parametro o Ano Letivo da Escola
		$this->campoNumero( "ano", "Ano", $this->ano, 4, 4, true);

		include("include/pmieducar/educar_campo_lista.php");
		
		$this->url_cancelar = "educar_index.php";
		$this->nome_url_cancelar = "Cancelar";
		
		
		$this->campoLista("ref_cod_turma","Turma",array('' => 'Selecione'),"","",false,"","",false,true);

		$this->campoLista( "ref_cod_matricula", "Aluno",array(''=>'Selecione'), "","",false,"Campo não obrigatório","",false,false);

		
	    //campo adicionado para pegar por parametro o nome da disciplina
		$this->campoTexto("disciplina","Disciplina:",$this->disciplina,40,255,false);
		
		//campo adicionado para pegar por parametro o nome do professor
		$this->campoTexto("professor","Professor(a):",$this->professor,40,255,false);

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
	setMatVisibility();
	getEscolaCurso();
	var campoTurma = document.getElementById( 'ref_cod_turma' );
	getTurmaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{

	getEscolaCursoSerie();
	getTurmaCurso();
}

document.getElementById('ano').onkeyup = function()
{

	setMatVisibility();
	getAluno();
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

	setMatVisibility();

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
	/*if ( campoTurma.length == 1 && campoCurso != '' ) {
		campoTurma.options[0] = new Option( 'O curso não possui nenhuma turma', '', false, false );
	}*/
	setMatVisibility();
}


document.getElementById('ref_cod_turma').onchange = function()
{
	getAluno();
	var This = this;
	setMatVisibility();

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
/* Comentei aqui para não apresentar o campo do aluno
/*function getAluno()
{
	var campoTurma = document.getElementById('ref_cod_turma').value;
	var campoAno = document.getElementById('ano').value;

	var xml1 = new ajax(getAluno_XML);
	strURL = "educar_matricula_turma_xml.php?tur="+campoTurma+"&ano="+campoAno;

	xml1.envia(strURL);
}*/

function getAluno_XML(xml)
{
	var aluno = xml.getElementsByTagName( "matricula" );
	var campoTurma = document.getElementById( 'ref_cod_turma' );
	var campoAluno = document.getElementById('ref_cod_matricula');

	campoAluno.length = 1;
	//campoAluno.options[0] = new Option( 'Selecione uma Turma', '', false, false );

	for ( var j = 0; j < aluno.length; j++ )
	{

		campoAluno.options[campoAluno.options.length] = new Option( aluno[j].firstChild.nodeValue, aluno[j].getAttribute('cod_matricula'), false, false );

	}
	/*if ( campoTurma.length == 1 && campoCurso != '' ) {
		campoTurma.options[0] = new Option( 'O curso não possui nenhuma turma', '', false, false );
	}*/
}


setVisibility('tr_ref_cod_matricula',false);
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
			return false;

			 if (!(/[^ ]/.test( document.getElementById("ref_cod_instituicao").value )))
			{
				mudaClassName( 'formdestaque', 'obrigatorio' );
				document.getElementById("ref_cod_instituicao").className = "formdestaque";
				alert( 'Preencha o campo \'Instituição\' corretamente!' );
				document.getElementById("ref_cod_instituicao").focus();
				return false;
			}
			 if (!(/[^ ]/.test( document.getElementById("ref_cod_curso").value )))
			{
				mudaClassName( 'formdestaque', 'obrigatorio' );
				document.getElementById("ref_cod_curso").className = "formdestaque";
				alert( 'Preencha o campo \'Curso\' corretamente!' );
				document.getElementById("ref_cod_curso").focus();
				return false;
			}


			 if (!(/[^ ]/.test( document.getElementById("ref_cod_turma").value )))
			{
				mudaClassName( 'formdestaque', 'obrigatorio' );
				document.getElementById("ref_cod_turma").className = "formdestaque";
				alert( 'Preencha o campo \'Turma\' corretamente!' );
				document.getElementById("ref_cod_turma").focus();
				return false;
			}		

    document.formcadastro.target = '_blank';
	document.getElementById( 'btn_enviar' ).disabled =false;
	document.formcadastro.submit();

}


// Chamado do arquivo que ira processar o relatorio
document.formcadastro.action = 'portabilis_boletim_professor_proc.php';

document.getElementById('em_branco').onclick = function()
{
	if(this.checked)
	{
		$('ref_cod_instituicao').disabled = true;
		$('ref_cod_escola').disabled = true;
		$('ref_cod_curso').disabled = true;
		$('ref_ref_cod_serie').disabled = true;
		$('ref_cod_turma').disabled = true;
		$('ref_cod_matricula').disabled = true;
	}
	else
	{
		$('ref_cod_instituicao').disabled = false;
		$('ref_cod_escola').disabled = false;
		$('ref_cod_curso').disabled = false;
		$('ref_ref_cod_serie').disabled = false;
		$('ref_cod_turma').disabled = false;
		$('ref_cod_matricula').disabled = false;
	}
}
</script>









