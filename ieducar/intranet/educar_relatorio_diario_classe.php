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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Diário de Classe" );
		$this->processoAp = "664";
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

	var $page_y = 125;

	var $get_file;

	var $cursos = array();

	var $get_link;

	var $total;

	//var $array_disciplinas = array();

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

		//$obj_permissoes = new clsPermissoes();
		//if($obj_permissoes->nivel_acesso($this->pessoa_logada) > 7)
			//header("location: index.php");

		return $retorno;
	}

	function Gerar()
	{

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

		//if(!$nivel_usuario)
			//header("location: index.php");
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		if($_POST){
			foreach ($_POST as $key => $value) {
				$this->$key = $value;

			}
		}



		$this->ano = $ano_atual = date("Y");
		//$this->mes = $mes_atual = date("n");
		/*
		$lim = 5;
		for($a = date('Y') ; $a < $ano_atual + $lim ; $a++ )
				$anos["{$a}"] = "{$a}";

		$this->campoLista( "ano", "Ano",$anos, $this->ano,"",false );
		*/

		$this->campoNumero( "ano", "Ano", $this->ano, 4, 4, true );

		$this->campoCheck("em_branco","Relatório em branco","");
		$this->campoNumero("numero_registros","N&uacute;mero de linhas","",3,3);

	//	$this->campoLista( "mes", "M&ecirc;s",$this->meses_do_ano, $this->mes,"",false );

		$get_escola = true;
		$obrigatorio = true;
		$exibe_nm_escola = true;
//		$get_escola_curso = true;
		$get_curso = true;
		$get_escola_curso_serie = true;
		//$get_turma = true;
		//$curso_padrao_ano_escolar = 1;
		//$exibe_campo_lista_curso_escola = true;
		include("include/pmieducar/educar_campo_lista.php");

		/*$db = new clsBanco();
		$consulta ="SELECT distinct
					       m.cod_turma
					       ,m.nm_turma
					       ,s.cod_serie
					       ,s.nm_serie
					       ,m.ref_ref_cod_escola
					  FROM pmieducar.turma          m
					       ,pmieducar.serie         s
					 WHERE m.ref_ref_cod_serie = s.cod_serie";

		$db->Consulta($consulta);

		$script = "<script>turma = new Array();\n";
		while ($db->ProximoRegistro()) {
			$tupla = $db->Tupla();
			$script .= "turma[turma.length] = new Array('{$tupla['cod_turma']}','{$tupla['nm_turma']}','{$tupla['cod_serie']}','{$tupla['nm_serie']}','{$tupla['ref_ref_cod_escola']}');\n";
		}
		echo $script .= "</script>";
*/
		$opcoes_turma = array('' => 'Selecione');
		if ( ($this->ref_ref_cod_serie && $this->ref_cod_escola) || $this->ref_cod_curso )
		{
			$obj_turma = new clsPmieducarTurma();
			$obj_turma->setOrderby("nm_turma ASC");
			$lst_turma = $obj_turma->lista( null, null, null, $this->ref_ref_cod_serie, $this->ref_cod_escola, null, null, null, null, null, null, null, null, null, 1, null, null, null, null, null, null, null, null, null, $this->ref_cod_curso );
			if ( is_array( $lst_turma ) && count( $lst_turma ) )
			{
				foreach ( $lst_turma as $turma )
				{
					$opcoes_turma["{$turma['cod_turma']}"] = "{$turma['nm_turma']}";
				}
			}
		}
		$this->campoLista("ref_cod_turma","Turma",$opcoes_turma,$this->ref_cod_turma);

		$this->campoLista("ref_cod_modulo","M&oacute;dulo",array('' => 'Selecione'),"");

		if($this->ref_cod_escola)
			$this->ref_ref_cod_escola = $this->ref_cod_escola;

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

function acao2()
{

	if(!acao())
		return false;

	showExpansivelImprimir(400, 200,'',[], "Diário de Classe");

	document.formcadastro.target = 'miolo_'+(DOM_divs.length-1);

	document.formcadastro.submit();
}

document.formcadastro.action = 'educar_relatorio_diario_classe_proc.php';

function getModulos()
{
	var campoEscola = document.getElementById( 'ref_cod_escola' ).value;
	var campoCurso = document.getElementById( 'ref_cod_curso' ).value;
	var campoAno = document.getElementById( 'ano' ).value;
	var campoTurma = document.getElementById( 'ref_cod_turma' ).value;
	var xml1 = new ajax(getModulos_XML);
	strURL = "educar_modulo_xml.php?esc="+campoEscola+"&ano="+campoAno+"&curso="+campoCurso+"&turma="+campoTurma;
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
//before_getEscola = function(){getModulos(); };

//addEvent_('load',getModulos);
document.getElementById('ref_cod_escola').onchange = function()
{
	getEscolaCurso();
	document.getElementById('ref_cod_curso').onchange();

}
document.getElementById('ano').onchange = function()
{
	getModulos();

}

document.getElementById('ref_cod_curso').onchange = function()
{
	getEscolaCursoSerie();
	document.getElementById( 'ref_cod_modulo' ).length = 1;
	getModulos();
}
document.getElementById('ref_cod_turma').onchange = function()
{
	document.getElementById( 'ref_cod_modulo' ).length = 1;
	getModulos();
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

</script>
