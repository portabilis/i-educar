<?php

require_once 'include/pmieducar/geral.inc.php';

class SequencialEnturmacao {

  var $refCodMatricula;
  var $refCodTurma;
  var $dataEnturmacao;

  public function __construct($refCodMatricula, $refCodTurma, $dataEnturmacao) {
    $this->refCodMatricula = $refCodMatricula;
    $this->refCodTurma = $refCodTurma;
    $this->dataEnturmacao = $dataEnturmacao;
  }

  public function reordenaSequencial() {
    if ($this->enturmarPorUltimo()) {
      $db = new clsBanco();
      return $db->CampoUnico("SELECT MAX(sequencial_fechamento)+1
                                FROM pmieducar.matricula_turma
                               WHERE ref_cod_turma = {$this->refCodTurma}");
    }

    $sequencial =  $this->sequencialOrdemAlfabetica();

    // $this->somaSequencialAlunos($sequencial);

    return $sequencial;
  }

  private function sequencialOrdemAlfabetica() {
    
  }

  private function enturmarPorUltimo() {
    $enturmarPorUltimo = FALSE;

    $turma = new clsPmieducarTurma($this->refCodTurma);
    $turma = $turma->detalhe();

    $instituicao = new clsPmieducarInstituicao($turma["ref_cod_instituicao"]);
    $instituicao = $instituicao->detalhe();
    $dataFechamento = $instituicao["data_fechamento"];

    $possuiDataFechamento = is_string($dataFechamento);

    if($possuiDataFechamento){
      $objMatricula = new clsPmieducarMatricula($this->refCodMatricula);
      $detMatricula = $objMatricula->detalhe();
      $ano = $detMatricula["ano"];

      $dataFechamento = explode("-", $dataFechamento);

      $dataFechamento = $ano . "-" . $dataFechamento[1] . "-" . $dataFechamento[2];

      if (strtotime($dataFechamento) < strtotime($this->dataEnturmacao))
        $enturmarPorUltimo = true;
    }

    $dataBaseTransferencia = $instituicao["data_base_transferencia"];

    if ($dataBaseTransferencia && $this->existeMatriculaTransferidaAno()){
      if (strtotime($dataBaseTransferencia) < strtotime($this->dataEnturmacao))
        $enturmarPorUltimo = true;
    }

    $dataBaseRemanejamento = $instituicao["data_base_remanejamento"];

    if($dataBaseRemanejamento){
      if (strtotime($dataBaseRemanejamento) < strtotime($this->dataEnturmacao))
        $enturmarPorUltimo = true;
    }

    return $enturmarPorUltimo;
  }

  private function existeMatriculaTransferidaAno(){
    if(is_numeric($this->refCodMatricula)){
      $db = new clsBanco();

      $codAluno = $db->CampoUnico("SELECT ref_cod_aluno FROM pmieducar.matricula WHERE cod_matricula = {$this->refCodMatricula}");
      $ano = $this->getAnoMatricula();
      if($codAluno)
        return $db->CampoUnico("SELECT 1 FROM pmieducar.matricula WHERE ref_cod_aluno = {$codAluno} AND ano = {$ano} AND
          aprovado = 4") == 1 ? TRUE : FALSE;
    }
  }

  function getAnoMatricula(){
    if (is_numeric($this->refCodMatricula)){
      $db = new clsBanco();
      return $db->CampoUnico("SELECT ano FROM pmieducar.matricula WHERE cod_matricula = {$this->refCodMatricula}");
    }
  }
}