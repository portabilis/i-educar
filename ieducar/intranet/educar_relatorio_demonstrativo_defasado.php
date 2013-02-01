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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Demonstrativo de alunos defasados idade/s&eacute;rie" );
		$this->processoAp = "653";
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
	var $ano;
	var $mes;

	var $nm_escola;
	var $nm_instituicao;
	//var $totalDiasUteis;
	var $qt_anos = 11;
	var $idade_inicial = 6 ;
	//var $necessidades;

	var $pdf;
	var $pagina_atual = 1;
	var $total_paginas = 1;

	var $get_link = false;
	var $cursos = array();

	var $array_ano_idade = array();

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
		if($obj_permissoes->nivel_acesso($this->pessoa_logada) > 7)
			header("location: index.php");

		return $retorno;
	}

	function Gerar()
	{

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		if($_POST){
			foreach ($_POST as $key => $value) {
				$this->$key = $value;

			}
		}



		$this->ano = $ano_atual = date("Y");
		$this->mes = $mes_atual = date("n");
		/*
		$lim = 5;
		for($a = date('Y') ; $a < $ano_atual + $lim ; $a++ )
				$anos["{$a}"] = "{$a}";
		$this->campoLista( "ano", "Ano",$anos, $this->ano,"",false );
		*/

		$this->campoNumero( "ano", "Ano", $this->ano, 4, 4, true );
		$this->campoLista( "mes", "M&ecirc;s",$this->meses_do_ano, $this->mes,"",false );


		$get_escola = true;
		$instituicao_obrigatorio = true;
		$escola_obrigatorio = false;
		$exibe_nm_escola = true;
		$get_escola_curso = true;
		$exibe_campo_lista_curso_escola = false;
		include("include/pmieducar/educar_campo_lista.php");
		$this->campoRotulo("cursos_","Cursos","<div id='cursos'>Selecione uma escola</div>");

		if($nivel_usuario <=3)
		{

			echo "<script>
					window.onload = function(){document.getElementById('ref_cod_escola').onchange = changeCurso};
				  </script>";

		}
		else
		{

			echo "<script>
					window.onload = function(){ changeCurso() };
				  </script>";
		}

		if($this->get_link)
			$this->campoRotulo("rotulo11", "-", "<a href='$this->get_link' target='_blank'>Baixar Relatório</a>");

		$this->acao_enviar = 'acao2()';
		$this->acao_executa_submit = false;

		$this->url_cancelar = "educar_index.php";
		$this->nome_url_cancelar = "Cancelar";


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


changeCurso =
function(){
	var campoEscola = document.getElementById('ref_cod_escola').value;
	if(campoEscola)
	{
		var xml1 = new ajax(getCurso_XML);
		strURL = "educar_curso_serie_xml.php?esc="+campoEscola+"&cur=1";
		xml1.envia(strURL);
	}
	else
	{
		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
		var xml1 = new ajax(getCursos_XML);
		strURL = "educar_curso_xml.php?ins="+campoInstituicao;
		xml1.envia(strURL);
	}


}

function getCursos_XML(xml)
{

	var escola = document.getElementById('ref_cod_escola');
	var instituicao = document.getElementById('ref_cod_instituicao');
	var cursos = document.getElementById('cursos');
	var conteudo = '';
	var achou = false;
	var inst_curso = xml.getElementsByTagName( "curso" );

	cursos.innerHTML = 'Selecione uma instituição';

	if(instituicao.value == '')
		return;

	for(var ct = 0; ct < inst_curso.length;ct++)
	{

		achou = true;
		conteudo += '<input type="checkbox" checked="checked" name="cursos[]" id="cursos[]" value="'+ inst_curso[ct].getAttribute('cod_curso') +'"><label for="cursos[]">' + inst_curso[ct].firstChild.nodeValue +'</label> <br />';

	}
	if( !achou ){
		cursos.innerHTML = 'Instituição sem cursos';
		return;
	}
	cursos.innerHTML = '<table cellspacing="0" cellpadding="0" border="0">';
	cursos.innerHTML += '<tr align="left"><td>'+ conteudo +'</td></tr>';
	cursos.innerHTML += '</table>';

}
changeCurso();
after_getEscola = changeCurso;

function getCurso_XML(xml)
{

	var escola = document.getElementById('ref_cod_escola');
	var cursos = document.getElementById('cursos');
	var conteudo = '';
	var achou = false;
	var escola_curso = xml.getElementsByTagName( "item" );

	cursos.innerHTML = 'Selecione uma escola';
	if(escola.value == '')
		return;

	for(var ct = 0; ct < escola_curso.length;ct+=2)
	{

		achou = true;
		conteudo += '<input type="checkbox" checked="checked" name="cursos[]" id="cursos[]" value="'+ escola_curso[ct].firstChild.nodeValue +'"><label for="cursos[]">' + escola_curso[ct+1].firstChild.nodeValue +'</label> <br />';

	}
	if( !achou ){
		cursos.innerHTML = 'Escola sem cursos';
		return;
	}
	cursos.innerHTML = '<table cellspacing="0" cellpadding="0" border="0">';
	cursos.innerHTML += '<tr align="left"><td>'+ conteudo +'</td></tr>';
	cursos.innerHTML += '</table>';

}

function acao2()
{

	if(!acao())
		return;

	showExpansivelImprimir(400, 200,'',[], "Demonstrativo de alunos defasados idade/s&eacute;rie");

	document.formcadastro.target = 'miolo_'+(DOM_divs.length-1);

	document.getElementById( 'btn_enviar' ).disabled =false;

	document.formcadastro.submit();

}

document.formcadastro.action = 'educar_relatorio_demonstrativo_defasado_proc.php';
</script>
