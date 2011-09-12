<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisAtestadoFrequencia extends Report
{
  function setForm()
  {

    $get_escola = true;
    $get_aluno = true;
    $instituicao_obrigatorio = true;
    $escola_obrigatorio = true;
    $aluno_obrigatorio = true;
    
    $this->ano = $ano_atual = date("Y");
    $this->campoNumero( "ano", "Ano", $this->ano, 4, 4, true);

    include("include/pmieducar/educar_campo_lista.php");  
 
    
  }

  function onValidationSuccess()
  {
    $this->addArg('ano', (int)$_POST['ano']);
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola', (int)$_POST['ref_cod_escola']);
    $this->addArg('aluno', (int)$_POST['ref_cod_aluno']);
  }

  function validate()
  {
    if (! $this->db->selectField("select 1 from pmieducar.matricula where ref_ref_cod_escola = {$_POST['ref_cod_escola']} and ref_cod_aluno = {$_POST['ref_cod_aluno']} and aprovado = 3 and ano = {$_POST['ano']}"))
    {
      $this->addValidationError('Este aluno não possui matriculas em andamento neste ano e escola.');
    }
  }

}

$report = new PortabilisAtestadoFrequencia($name = 'Atestado de Frequência', $templateName = 'portabilis_atestado_frequencia');

$report->addRequiredField('ano','ano');
$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');
$report->addRequiredField('ref_cod_aluno', 'aluno');

$report->render();
?>
