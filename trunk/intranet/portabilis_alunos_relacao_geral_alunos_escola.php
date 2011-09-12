<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisRelacaoGeralAlunosEscola extends Report
{
  function setForm()
  {

    $get_escola = true;
    $get_curso = true;
    $get_escola_curso_serie = true;
    $get_turma = true;
    $instituicao_obrigatorio = true;
    $escola_obrigatorio = true;
    
    $this->ano = $ano_atual = date("Y");
    $this->campoNumero( "ano", "Ano", $this->ano, 4, 4, true);

    include("include/pmieducar/educar_campo_lista.php");

		$opcoes[1] = "Ambos";
		$opcoes[2] = "Masculino";
		$opcoes[3] = "Feminino";
		
		$this->campoLista('sexo', 'Sexo: ', $opcoes, $this->sexo);
		
		$this->campoNumero('idadeinicial', 'Faixa etсria: ', $this->idadeinicial,
		2, 2, FALSE, '', '', FALSE, FALSE, TRUE);

		$this->campoNumero('idadefinal', ' atщ ', $this->idadefinal, 2, 2, FALSE);  

    
  }

  function onValidationSuccess()
  {
    
    if (! isset($_POST['ref_cod_curso']))
      $this->addArg('curso', 0);
    else
      $this->addArg('curso', (int)$_POST['ref_cod_curso']);  
      
    if (! isset($_POST['ref_ref_cod_serie']))
      $this->addArg('serie', 0);
    else
      $this->addArg('serie', (int)$_POST['ref_ref_cod_serie']);
      
    if (! isset($_POST['ref_cod_turma']))
      $this->addArg('turma', 0);
    else
      $this->addArg('turma', (int)$_POST['ref_cod_turma']); 

    if ($_POST['sexo'] == 2) {
      $_POST['sexo'] = "M";		
    }
    elseif ($_POST['sexo'] == 3) {
      $_POST['sexo'] = "F";
    }
    else{
      $_POST['sexo'] = "A";		
    }
      
    $this->addArg('ano', (int)$_POST['ano']);
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola', (int)$_POST['ref_cod_escola']);
    $this->addArg('sexo', $_POST['sexo']);
    $this->addArg('idadeinicial', (int)$_POST['idadeinicial']);
    $this->addArg('idadefinal', (int)$_POST['idadefinal']);
  }
}

$report = new PortabilisRelacaoGeralAlunosEscola($name = 'Relaчуo Geral de Alunos Por Escola', $templateName = 'portabilis_alunos_relacao_geral_alunos_escola');

$report->addRequiredField('ano','ano');
$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');
$report->addRequiredField('sexo', 'sexo');

$report->render();
?>