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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Relat&oacute;rio Professor Disciplina" );
		$this->processoAp = "827";
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
	var $ref_cod_disciplina;

	var $nm_escola;
	var $nm_instituicao;
	var $ref_cod_curso;


	var $pdf;

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

		$get_escola = true;
		$exibe_nm_escola = true;
		$get_curso = true;
		$escola_obrigatorio = false;
		$curso_obrigatorio = true;
		$instituicao_obrigatorio = true;

		include("include/pmieducar/educar_campo_lista.php");

		$this->campoLista("ref_cod_disciplina","Disciplina",array('' => 'Selecione'), $this->ref_cod_disciplina,"",false,"","",false,false);


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


function getDisciplina()
{

	var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
	var campoCurso = document.getElementById('ref_cod_curso').value;

	var xml1 = new ajax(getDisciplina_XML);
	strURL = "educar_disciplina_xml.php?cur="+campoCurso;

	xml1.envia(strURL);
}

function getDisciplina_XML(xml)
{
	var disciplinas = xml.getElementsByTagName( "disciplina" );
	var campoDisciplina = document.getElementById( 'ref_cod_disciplina' );

	campoDisciplina.length = 1;

	for ( var j = 0; j < disciplinas.length; j++ )
	{

		campoDisciplina.options[campoDisciplina.options.length] = new Option( disciplinas[j].firstChild.nodeValue, disciplinas[j].getAttribute('cod_disciplina'), false, false );

	}

}


document.getElementById('ref_cod_curso').onchange = function()
{
	getDisciplina();
}

document.getElementById('ref_cod_escola').onchange = function()
{

	if(document.getElementById('ref_cod_escola').value)
		getEscolaCurso();
	else
		getCurso();

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

	showExpansivelImprimir(400, 200,'',[], "Relatório Professor por Disciplina");

	document.formcadastro.target = 'miolo_'+(DOM_divs.length-1);

	document.getElementById( 'btn_enviar' ).disabled =false;

	document.formcadastro.submit();

}

document.formcadastro.action = 'educar_relatorio_professor_disciplina_proc.php';
</script>
