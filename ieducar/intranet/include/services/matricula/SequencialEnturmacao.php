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

  public function ordenaSequencialNovaMatricula() {
    if ($this->enturmarPorUltimo()) {

      $novoSequencial = $this->sequencialAlunoAposData();

      $this->somaSequencialPosterior($novoSequencial);

      return $novoSequencial;
    }

    $sequencialNovoAluno = $this->sequencialAlunoOrdemAlfabetica();

    $this->somaSequencialPosterior($sequencialNovoAluno);

    return $sequencialNovoAluno;
  }

  private function sequencialAlunoAposData() {
      $db = new clsBanco();
      $sql = "SELECT MAX(sequencial_fechamento)+1
                                FROM pmieducar.matricula_turma
                               INNER JOIN pmieducar.matricula ON (matricula.cod_matricula = matricula_turma.ref_cod_matricula)
                               WHERE matricula_turma.ativo = 1
                                 AND matricula.ativo = 1
                                 AND ref_cod_turma = {$this->refCodTurma}";

      if (!$this->matriculaDependencia()) {
        $sql .= " AND matricula.dependencia = FALSE";
      }

      $novoSequencial = $db->CampoUnico($sql);

      $novoSequencial = $novoSequencial ? $novoSequencial : 1;

      return $novoSequencial;
  }

  private function sequencialAlunoOrdemAlfabetica() {
    $db = new clsBanco();
    $sql =
    "SELECT sequencial_fechamento, pessoa.nome
       FROM pmieducar.matricula_turma
      INNER JOIN pmieducar.matricula ON (matricula.cod_matricula = matricula_turma.ref_cod_matricula)
      INNER JOIN pmieducar.aluno ON (aluno.cod_aluno = matricula.ref_cod_aluno)
      INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
      WHERE matricula_turma.ativo = 1
        AND matricula_turma.ref_cod_turma = $this->refCodTurma
        AND matricula_turma.data_enturmacao <= '$this->dataEnturmacao'::date
      ORDER BY sequencial_fechamento";

    $db->Consulta($sql);

    $alunos = array();
    while ($db->ProximoRegistro()) {
      $aluno = $db->Tupla();
      $sequencial = $aluno['sequencial_fechamento'];
      $alunos[$sequencial] = strtoupper($aluno['nome']);
    }

    $matricula = new clsPmieducarMatricula($this->refCodMatricula);
    $matricula = $matricula->detalhe();

    $alunos['novo-aluno'] = $matricula['nome'];

    asort($alunos);

    $novoSequencial = 0;
    foreach ($alunos as $sequencial => $nome) {
      if ($sequencial == 'novo-aluno') {
        $novoSequencial++;
        break;
      }
      $novoSequencial++;
    }
    return $novoSequencial;
  }

  private function somaSequencialPosterior($sequencial) {
    $sql =
    "UPDATE pmieducar.matricula_turma
        SET sequencial_fechamento = sequencial_fechamento + 1
      WHERE ativo = 1
        AND ref_cod_turma = $this->refCodTurma
        AND sequencial_fechamento >= $sequencial";

    $db = new clsBanco();
    $db->Consulta($sql);
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

  function matriculaDependencia() {
    $db = new clsBanco();
    $dependencia = $db->CampoUnico("SELECT dependencia
                                      FROM pmieducar.matricula
                                     WHERE matricula.cod_matricula = {$this->refCodMatricula}");
    return dbBool($dependencia);
  }
}