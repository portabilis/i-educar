<?php

require_once 'include/pmieducar/geral.inc.php';

class SequencialEnturmacao {

  var $refCodMatricula;
  var $refCodTurma;
  var $dataEnturmacao;
  var $sequencial;

  public function __construct($refCodMatricula, $refCodTurma, $dataEnturmacao, $sequencial) {
    $this->refCodMatricula = $refCodMatricula;
    $this->refCodTurma = $refCodTurma;
    $this->dataEnturmacao = $dataEnturmacao;
    $this->sequencial = $sequencial;
  }

  public function ordenaSequencialNovaMatricula() {

    $instituicao = $this->existeDataBaseRemanejamento();
    $sequencialFechamento = $this->existeMatriculaTurma();

    if ($sequencialFechamento) {
      $this->subtraiSequencialPosterior($sequencialFechamento);
    }

    if ($this->enturmarPorUltimo()) {

      $novoSequencial = $this->sequencialAlunoAposData();

      $this->somaSequencialPosterior($novoSequencial);

      return $novoSequencial;
    }

    if (($instituicao) && ($instituicao > $this->dataEnturmacao)){

      $novoSequencial = $this->sequencialAlunoAntesData();

      $this->somaSequencialPosterior($novoSequencial);

      return $novoSequencial;

    }else{

      $sequencialNovoAluno = $this->sequencialAlunoOrdemAlfabetica();

      $this->somaSequencialPosterior($sequencialNovoAluno);

      return $sequencialNovoAluno;
    }

    return $sequencialNovoAluno;
  }

  public function ordenaSequencialExcluiMatricula() {

    $sequencialFechamento = $this->existeMatriculaTurma();

    $this->subtraiSequencialPosterior($sequencialFechamento);

     return $sequencialExcluiAluno;
  }

  private function sequencialAlunoAposData() {
      $db = new clsBanco();
      $sql = "  SELECT MAX(sequencial_fechamento)+1
                  FROM pmieducar.matricula_turma
                 INNER JOIN pmieducar.matricula ON (matricula.cod_matricula = matricula_turma.ref_cod_matricula)
                 INNER JOIN pmieducar.aluno ON (aluno.cod_aluno = matricula.ref_cod_aluno)
                 INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
                 WHERE matricula.ativo = 1
                   AND ref_cod_turma = {$this->refCodTurma}
                   AND matricula_turma.data_enturmacao <= '{$this->dataEnturmacao}'
                   AND (CASE WHEN matricula_turma.data_enturmacao = '{$this->dataEnturmacao}'
                             THEN pessoa.nome <= (SELECT pessoa.nome
                                                    FROM pmieducar.matricula
                                                   INNER JOIN pmieducar.aluno ON (aluno.cod_aluno = matricula.ref_cod_aluno)
                                                   INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
                                                   WHERE matricula.cod_matricula = {$this->refCodMatricula})
                            ELSE TRUE
                        END)";
      if (!$this->matriculaDependencia()) {
        $sql .= " AND matricula.dependencia = FALSE";
      }

      $novoSequencial = $db->CampoUnico($sql);

      $novoSequencial = $novoSequencial ? $novoSequencial : 1;

      return $novoSequencial;
  }

  private function sequencialAlunoAntesData() {
      $db = new clsBanco();
      $sql = "SELECT MAX(sequencial_fechamento) + 1
                FROM pmieducar.matricula_turma
               INNER JOIN pmieducar.matricula ON (matricula.cod_matricula = matricula_turma.ref_cod_matricula)
               INNER JOIN pmieducar.escola ON (matricula.ref_ref_cod_escola = escola.cod_escola)
               INNER JOIN pmieducar.instituicao ON (instituicao.cod_instituicao = escola.ref_cod_instituicao)
               INNER JOIN pmieducar.aluno ON (aluno.cod_aluno = matricula.ref_cod_aluno)
               INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
               WHERE matricula.ativo = 1
                 AND ref_cod_turma = {$this->refCodTurma}
                 AND matricula_turma.data_enturmacao < instituicao.data_base_remanejamento
                 AND pessoa.nome < (SELECT pessoa.nome
                                      FROM pmieducar.matricula
                                     INNER JOIN pmieducar.aluno ON (aluno.cod_aluno = matricula.ref_cod_aluno)
                                     INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
                                     WHERE matricula.cod_matricula = {$this->refCodMatricula})";

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
      WHERE matricula.ativo = 1
        AND (CASE WHEN matricula_turma.ativo = 1 THEN TRUE
                  WHEN matricula_turma.transferido THEN TRUE
                  WHEN matricula_turma.remanejado THEN TRUE
                  WHEN matricula.dependencia THEN TRUE
                  WHEN matricula_turma.abandono THEN TRUE
                  WHEN matricula_turma.reclassificado THEN TRUE
                  ELSE FALSE END)
        AND matricula_turma.ref_cod_turma = $this->refCodTurma
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

    $alunos['novo-aluno'] = limpa_acentos(strtoupper($matricula['nome']));

    asort($alunos);


    $novoSequencial = 0;
    foreach ($alunos as $sequencial => $nome) {
      if ($sequencial == 'novo-aluno') {
        $novoSequencial++;
        break;
      }
      $novoSequencial = $sequencial++;
    }
    return $novoSequencial;
  }

  private function somaSequencialPosterior($sequencial) {
    $sql =
    "UPDATE pmieducar.matricula_turma
        SET sequencial_fechamento = sequencial_fechamento + 1
      WHERE ref_cod_turma = $this->refCodTurma
        AND sequencial_fechamento >= $sequencial";

    $db = new clsBanco();
    $db->Consulta($sql);
  }

  private function subtraiSequencialPosterior($sequencial) {
    $sql =
    "UPDATE pmieducar.matricula_turma
        SET sequencial_fechamento = sequencial_fechamento - 1
      WHERE ref_cod_turma = $this->refCodTurma
        AND sequencial_fechamento > $sequencial";

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

  function existeDataBaseRemanejamento() {
    $getInstituicao = new clsPmieducarMatriculaTurma($this->refCodMatricula);
    $getInstituicao = $getInstituicao->getInstituicao();
    $instituicao = new clsPmieducarInstituicao($getInstituicao);
    $instituicao = $instituicao->detalhe();
    $data_base_remanejamento = $instituicao['data_base_remanejamento'];

    return $data_base_remanejamento;
  }

  function existeMatriculaTurma(){
    if(is_numeric($this->refCodMatricula)){
      $db = new clsBanco();

      return $db->CampoUnico("SELECT sequencial_fechamento
                                FROM pmieducar.matricula_turma
                               INNER JOIN pmieducar.matricula ON matricula.cod_matricula = matricula_turma.ref_cod_matricula
                               WHERE matricula.ativo = 1
                                 AND ref_cod_matricula = {$this->refCodMatricula}
                                 AND ref_cod_turma = {$this->refCodTurma}
                                 AND (CASE WHEN matricula_turma.ativo = 1 THEN TRUE
                                           WHEN matricula_turma.transferido THEN TRUE
                                           WHEN matricula_turma.remanejado THEN TRUE
                                           WHEN matricula.dependencia THEN TRUE
                                           WHEN matricula_turma.abandono THEN TRUE
                                           WHEN matricula_turma.reclassificado THEN TRUE
                                           ELSE FALSE
                                      END)");
    }
  }

  function excluirSequencial(){
    if ($this->excluirSequencial){
      $sequencial = $this->sequencial;
      $matricula = $this->refCodMatricula;

      $sqlDelete = "DELETE FROM pmieducar.matricula_turma
                      WHERE ref_cod_matricula = {$this->refCodMatricula}
                        AND sequencial = {$this->sequencial}";

      $sqlUpdate = "UPDATE pmieducar.matricula_turma
                       SET sequencial = sequencial - 1
                     WHERE ref_cod_matricula = {$this->refCodMatricula}
                       AND sequencial > {$this->sequencial}";

      $db = new clsBanco();
      $db->Consulta($sqlDelete);
      $db->Consulta($sqlUpdate);
    }
  }
}
