<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisHistoricoEscolarSeries extends Report
{
  function setForm()
  {

    $get_escola = true;
    $instituicao_obrigatorio = true;
    $get_aluno = true;
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

    $anos_historicos_sem_situacao = $this->db->select("select ano from pmieducar.historico_escolar where ref_cod_aluno = {$_POST['ref_cod_aluno']} and aprovado is null");

    if (count($anos_historicos_sem_situacao))
    {
      $msg = 'O(s) histórico(s) do(s) ano(s): ';
      foreach($anos_historicos_sem_situacao as $ano)
      {
        $msg .= "{$ano['ano']}, ";
      }
      $msg .= 'está(ão) sem situação definida.';
      $this->addValidationError($msg);
    }

  }

}

$report = new PortabilisHistoricoEscolarSeries($name = 'Histórico Escolar (8 Anos)', $templateName = 'portabilis_historico_escolar');

$report->addRequiredField('ano','ano');
$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');
$report->addRequiredField('ref_cod_aluno', 'aluno');

$report->render();
?>
