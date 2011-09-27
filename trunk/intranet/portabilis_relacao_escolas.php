<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisRelacaoEscolas extends Report
{
  function setForm()
  {

    $get_escola = true;
    $instituicao_obrigatorio = true;
    $escola_obrigatorio = false;
    $this->ano = $ano_atual = date("Y");
    $this->campoNumero( "ano", "Ano", $this->ano, 4, 4, true);

    include("include/pmieducar/educar_campo_lista.php");
    //$this->campoLista("ref_cod_escola","Escola",array('' => 'Selecione'),'',"",false,"","",false,false);
  }

  function onValidationSuccess()
  {
    if (! isset($_POST['ref_cod_escola']))
      $this->addArg('escola', 0);
    else
      $this->addArg('escola', (int)$_POST['ref_cod_escola']);
    
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
  }

}

$report = new PortabilisRelacaoEscolas($name = 'Relaчуo de Escolas', $templateName = 'portabilis_relacao_escolas');

$report->addRequiredField('ano');
$report->addRequiredField('ref_cod_instituicao', 'instituiчуo');
  
$report->render();
?>