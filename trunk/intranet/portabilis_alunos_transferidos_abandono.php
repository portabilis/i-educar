<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisRelacaoAlunosTransfAbandono extends Report
{
  function setForm()
  {

    $this->ano = $ano_atual = date("Y");
    $this->campoNumero( "ano", "Ano", $this->ano, 4, 4, true);

    $this->addFilterFor(array('instituicao', 'escola'));
     
    $opcoes[1] = "Abandono";
		$opcoes[2] = "Transferido";
    $opcoes[9] = "Ambos";
    
    $this->campoLista('situacao', 'Situação', $opcoes, $this->situacao);
   
  }

  function onValidationSuccess()
  {
    if (! isset($_POST['ref_cod_escola']))
      $this->addArg('escola', 0);
    else
      $this->addArg('escola', (int)$_POST['ref_cod_escola']);
      
    $this->addArg('ano', (int)$_POST['ano']);
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);    
    $this->addArg('situacao', (int)$_POST['situacao']);
  }

}

$report = new PortabilisRelacaoAlunosTransfAbandono($name = 'Relação de Alunos Transferidos/Abandono', $templateName = 'portabilis_alunos_transferidos_abandono');

$report->addRequiredField('ano', 'ano');
$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('situacao', 'situacao');
  
$report->render();
?>
