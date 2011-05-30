<?php

/*
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 */


require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/clsPDF.inc.php';


require_once 'relatorios/phpjasperxml07d/class/fpdf/fpdf.php';
require_once 'relatorios/phpjasperxml07d/class/PHPJasperXML.inc';


class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Relação Geral de Alunos por Escola" );
		$this->processoAp = "999105"; //alterar
		$this->renderMenu = false;
		$this->renderMenuSuspenso = false;
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
	
	var $aux_sexo;
	var $aux_idadeinicial;
	var $aux_idadefinal;

	var $nm_escola;
	var $nm_instituicao;
	
	var $pdf;
	var $pagina_atual = 1;
	var $total_paginas = 1;
	
	var $page_y = 135;

	var $get_link = false;

	var $total;

	var $aux_cod_escola;	

	/****************COLOCADO********************************/
	var $segue_padrao_escolar = true;
	var $mostra_cabecalho_modulo = array();
	/****************COLOCADO********************************/

	function renderHTML()
	{
	
	
	$xml =  simplexml_load_file("relatorios/jasperreports/portabilis_alunos_relacao_geral_alunos_escola.jrxml");
	
    if (($_POST['ref_cod_escola']) == 0){
	  $aux_cod_escola = 0;
	}
	else{
	  $aux_cod_escola = $_POST['ref_cod_escola'];
	}
	
	if ($_POST['sexo'] == 2) {
		$aux_sexo = "M";		
	}
	elseif ($_POST['sexo'] == 3) {
		$aux_sexo = "F";
	}
	else{
		$aux_sexo = "A";		
	}
	
	if (! isset($_POST['idadeinicial']) || ! $_POST['idadeinicial']) {
		$aux_idadeinicial = 0;	
	}
	else{
		$aux_idadeinicial = $_POST['idadeinicial'];	
	}
	
	if (! isset($_POST['idadefinal']) || ! $_POST['idadefinal']) {
		$aux_idadefinal = 0;	
	}
	else{
		$aux_idadefinal = $_POST['idadefinal'];			
	}
			
    $PHPJasperXML = new PHPJasperXML();
	$PHPJasperXML->debugsql=false;
    $PHPJasperXML->arrayParameter=array("ano"=>$_POST['ano'],"instituicao"=>$_POST['ref_cod_instituicao'],"escola" =>$aux_cod_escola, "sexo"=>"'".  $aux_sexo."'","idadeinicial"=>$aux_idadeinicial,"idadefinal"=>$aux_idadefinal);	
		
	$PHPJasperXML->xml_dismantle($xml);

	$PHPJasperXML->transferDBtoArray($server,$user,$pass,$db,$port);
	$PHPJasperXML->outpage("I");    //page output method I:standard output  D:Download file

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
