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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Controle Bimestral de Desempenho de Alunos" );
		$this->processoAp = "654";
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
	var $ref_cod_curso;
	var $sequencial;

	var $pdf;
	var $pagina_atual = 1;
	var $total_paginas = 1;

	var $page_y = 125;

	var $get_link;

	var $cursos = array();

	var $array_disciplinas = array();

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
		/*echo '<script type="text/javascript">
		 document.write(\'<div id="loading"><br /> <img src="green_rot.gif" border="0"><b>Loading, please wait...</b></div>\');
		 window.onload=function(){
		   document.getElementById("loading").style.display="none";
		 }
		</script>';*/
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
		
		$this->campoNumero( "ano", "Ano", $this->ano, 4, 4, true );

		$get_escola = true;
		$obrigatorio = true;
		$exibe_nm_escola = true;
		$get_curso = true;
		$curso_padrao_ano_escolar = 1;
//		$get_semestre = true;
		
		include("include/pmieducar/educar_campo_lista.php");

		$this->campoLista("ref_cod_modulo","M&oacute;dulo",array('' => 'Selecione'),"");

		if($this->get_link)
			$this->campoRotulo("rotulo11", "-", "<a href='$this->get_link' target='_blank'>Baixar Relatório</a>");

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

//Event.observe(window, 'load', function() { if ($F('apagar_radios') == 1) setVisibility('tr_semestres', false);});

function getModulos()
{
	var campoEscola = document.getElementById( 'ref_cod_escola' ).value;
	var campoAno = document.getElementById( 'ano' ).value;
	var xml1 = new ajax(getModulos_XML);
	strURL = "educar_ano_letivo_modulo_xml.php?esc="+campoEscola+"&ano="+campoAno;
	xml1.envia(strURL);
}


function getModulos_XML(xml)
{

	var modulos = xml.getElementsByTagName( "ano_letivo_modulo" );

	var campoEscola = document.getElementById( 'ref_cod_escola' ).value;
	var campoCurso= document.getElementById( 'ref_cod_curso' ).value;
	var campoModulo = document.getElementById( 'ref_cod_modulo' );
	var campoAno = document.getElementById( 'ano' ).value;

	campoModulo.length = 1;
	campoModulo.options[0] = new Option( 'Selecione um módulo', '', false, false );
	for ( var j = 0; j < modulos.length; j++ )
	{
		//if ( modulos[j][2] == campoEscola && modulos[j][3] == campoAno)
		//{
		campoModulo.options[campoModulo.options.length] = new Option( modulos[j].firstChild.nodeValue, modulos[j].getAttribute('cod_modulo') + "-" +modulos[j].getAttribute('sequencial') , false, false );
		//}
	}
	if ( campoModulo.length == 1 ) {
		campoModulo.options[0] = new Option( 'O curso não possui nenhum módulo', '', false, false );
	}


}

document.getElementById('ano').onkeyup = function()
{
	getModulos();
}

//after_getEscolaCurso = function () {getModulos()};
//document.getElementById('ref_cod_curso').onchange = function(){getModulos()};

//getEscolaCurso();
before_getEscola = function(){
	getModulos();
};

document.getElementById('ref_cod_escola').onchange = function(){
	document.getElementById( 'ref_cod_modulo' ).length = 1; 
	getEscolaCurso();
	getModulos();
};

addEvent_('load',getModulos);

function acao2()
{
/*	if ($F('is_padrao') == 0)
	{
		if (!$F('sem1') && !$F('sem2'))
		{
			alert("O campo 'Semestre' deve ser preenchido corretamente!");
			document.getElementById('sem1').focus();
			return;
		}
	}*/
	if(!acao())
		return false;

	showExpansivelImprimir(400, 200,'',[], "Controle Bimestral de Desempenho de Alunos");

	document.formcadastro.target = 'miolo_'+(DOM_divs.length-1);

	document.formcadastro.submit();
}

document.formcadastro.action = 'educar_relatorio_controle_bim_desempenho_proc.php';

</script>