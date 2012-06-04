<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisHistoricoEscolarSerieAno extends Report
{
  function setForm()
  {
    $this->dynamicSelectMenus->helperFor('instituicao');
    $this->dynamicSelectMenus->helperFor('escola');
    $this->dynamicSelectMenus->helperFor('pesquisaAluno');
  }

  function onValidationSuccess()
  {
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola', (int)$_POST['ref_cod_escola']);
    $this->addArg('aluno', (int)$_POST['ref_cod_aluno']);
  }

# function validate()
#  {

#    $anos_historicos_sem_situacao = $this->db->select("select ano from pmieducar.historico_escolar where ref_cod_aluno = {$_POST3['ref_cod_aluno']} and aprovado is null");
#
#    if (count($anos_historicos_sem_situacao))
#    {
#      $msg = 'O(s) histórico(s) do(s) ano(s): ';
#      foreach($anos_historicos_sem_situacao as $ano)
#      {
#        $msg .= "{$ano['ano']}, ";
#      }
#      $msg .= 'está(ão) sem situação definida.';
#      $this->addValidationError($msg);
#    }
#
#  }

}

$report = new PortabilisHistoricoEscolarSerieAno($name = 'Histórico Escolar (Séries/Anos)', $templateName = 'portabilis_historico_escolar_series_anos');

$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');
$report->addRequiredField('ref_cod_aluno', 'aluno');

$report->render();
?>
