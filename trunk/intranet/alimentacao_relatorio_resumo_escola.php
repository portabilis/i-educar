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
require_once( "include/alimentacao/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Relatório - Resumo por Escola" );
		$this->processoAp = "10009";
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


	var $mes;
	var $ano;

	var $sequencial;
	var $pdf;
	var $pagina_atual = 1;
	var $total_paginas = 1;

	var $page_y = 135;

	var $get_link = false;

	var $total;

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

		//Campos de pesquisa
		
		
		$opcoes = array();
		for ($i = 2008; $i <= date("Y");$i++)
		{
			$opcoes[$i] = $i;
		}
		$this->campoLista( "ano", "Ano", $opcoes, $this->ano,"",false,"","","",false );
		
		$obj_envio = new clsAlimentacaoEnvioMensalEscola();
		
		$opcoes = array();
		$opcoes = $obj_envio->getArrayMes();
		$opcoes[""]  = "Todos"; 
		
		$this->campoLista( "mes", "Mês", $opcoes, $this->mes,"",false,"","","",false );
	
		$this->url_cancelar = "alimentacao_relatorio_resumo_escola.php";
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
		return;

	showExpansivelImprimir(400, 200,'',[], "Boletim");

	document.formcadastro.target = 'miolo_'+(DOM_divs.length-1);

	document.getElementById( 'btn_enviar' ).disabled =false;

	document.formcadastro.submit();

}

document.getElementById('formcadastro').action ='alimentacao_relatorio_resumo_escola_proc.php';

</script>