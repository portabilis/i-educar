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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Relação Geral de Alunos por Escola" );
		$this->processoAp = "999105"; //alterar
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


	var $page_y = 139;

	var $sexo;
	var $idadeinicial;
	var $idadefinal;

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
		$escola_curso_serie_obrigatorio = false;
		$curso_obrigatorio = false;
		
		$this->ano = $ano_atual = date("Y");
		
		//campo adicionado para pegar por parametro o Ano Letivo da Escola
		$this->campoNumero( "ano", "Ano", $this->ano, 4, 4, true);
		
		include("include/pmieducar/educar_campo_lista.php");
		
		$this->campoLista("ref_cod_turma","Turma",array('' => 'Selecione'),'',"",false,"","",false,false);	
		
		$opcoes[1] = "Ambos";
		$opcoes[2] = "Masculino";
		$opcoes[3] = "Feminino";
		
		$this->campoLista('sexo', 'Sexo', $opcoes, $this->sexo);
		
		$this->campoNumero('idadeinicial', 'Faixa etária', $this->idadeinicial,
		2, 2, FALSE, '', '', FALSE, FALSE, TRUE);

		$this->campoNumero('idadefinal', ' até ', $this->idadefinal, 2, 2, FALSE);
		
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

	document.formcadastro.target = '_blank';
	document.getElementById( 'btn_enviar' ).disabled =false;
	document.formcadastro.submit();
}

// Chamado do arquivo que ira processar o relatorio
document.formcadastro.action = 'portabilis_alunos_relacao_geral_alunos_escola_proc.php';

document.getElementById('ref_cod_escola').onchange = function()
{
	getEscolaCurso();
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

</script>








