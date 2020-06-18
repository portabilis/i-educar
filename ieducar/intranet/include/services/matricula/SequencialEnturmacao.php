<?php

use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SequencialEnturmacao
{
    public $refCodMatricula;
    public $refCodTurma;
    public $dataEnturmacao;

    /**
     * @var LegacyRegistration
     */
    private $registration;

    /**
     * @var LegacySchoolClass
     */
    private $schoolClass;

    public function __construct($refCodMatricula, $refCodTurma, $dataEnturmacao)
    {
        $this->refCodMatricula = $refCodMatricula;
        $this->refCodTurma = $refCodTurma;
        $this->dataEnturmacao = $dataEnturmacao;

        $this->registration = LegacyRegistration::findOrFail($refCodMatricula);
        $this->schoolClass = LegacySchoolClass::findOrFail($refCodTurma);
    }

    public function ordenaSequencialNovaMatricula()
    {
        $relocationDate = $this->getRelocationDate();
        $sequencialFechamento = $this->existeMatriculaTurma();

        if ($sequencialFechamento) {
            $this->subtraiSequencialPosterior($sequencialFechamento);
        }

        if ($this->matriculaDependencia()) {
            $novoSequencial = $this->sequencialAlunoDependencia();

            $this->somaSequencialPosterior($novoSequencial);

            return $novoSequencial;
        }

        if ($this->enturmarPorUltimo()) {
            $novoSequencial = $this->sequencialAlunoAposData();

            $this->somaSequencialPosterior($novoSequencial);

            return $novoSequencial;
        }

        if (isset($relocationDate) && $relocationDate > $this->dataEnturmacao) {
            $novoSequencial = $this->sequencialAlunoAntesData();

            $this->somaSequencialPosterior($novoSequencial);

            return $novoSequencial;
        } else {
            $sequencialNovoAluno = $this->sequencialAlunoOrdemAlfabetica();

            $this->somaSequencialPosterior($sequencialNovoAluno);

            return $sequencialNovoAluno;
        }

        return $sequencialNovoAluno;
    }

    public function ordenaSequencialExcluiMatricula()
    {
        $sequencialFechamento = $this->existeMatriculaTurma();

        $this->subtraiSequencialPosterior($sequencialFechamento);

        // FIXME
        // A implentação anterior retornava uma variável indefinida, ver
        // impactos ao corrigir esta implementação para retornar o sequencial
        // correto.

        return null;
    }

    private function sequencialAlunoAposData()
    {
        $sql = "  SELECT MAX(sequencial_fechamento)+1 as sequencial
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
            $sql .= ' AND matricula.dependencia = FALSE';
        }

        $novoSequencial = DB::selectOne($sql)->sequencial;

        $novoSequencial = $novoSequencial ? $novoSequencial : 1;

        return $novoSequencial;
    }

    private function sequencialAlunoAntesData()
    {
        $relocationDate = $this->getRelocationDate();

        $sql = "SELECT MAX(sequencial_fechamento) + 1 as sequencial
                FROM pmieducar.matricula_turma
               INNER JOIN pmieducar.matricula ON (matricula.cod_matricula = matricula_turma.ref_cod_matricula)
               INNER JOIN pmieducar.escola ON (matricula.ref_ref_cod_escola = escola.cod_escola)
               INNER JOIN pmieducar.instituicao ON (instituicao.cod_instituicao = escola.ref_cod_instituicao)
               INNER JOIN pmieducar.aluno ON (aluno.cod_aluno = matricula.ref_cod_aluno)
               INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
               WHERE matricula.ativo = 1
                 AND ref_cod_turma = {$this->refCodTurma}
                 AND matricula_turma.data_enturmacao < '{$relocationDate}'
                 AND pessoa.nome < (SELECT pessoa.nome
                                      FROM pmieducar.matricula
                                     INNER JOIN pmieducar.aluno ON (aluno.cod_aluno = matricula.ref_cod_aluno)
                                     INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
                                     WHERE matricula.cod_matricula = {$this->refCodMatricula})";

        if (!$this->matriculaDependencia()) {
            $sql .= ' AND matricula.dependencia = FALSE';
        }

        $novoSequencial = DB::selectOne($sql)->sequencial;

        $novoSequencial = $novoSequencial ? $novoSequencial : 1;

        return $novoSequencial;
    }

    private function sequencialAlunoDependencia()
    {
        $sequencialPorNome = $this->sequencialAlunoDependenciaOrdemAlfabetica();

        if ($sequencialPorNome) {
            return $sequencialPorNome;
        }

        $sequencialPorData = $this->sequencialAlunoDependenciaPorData();

        if ($sequencialPorData) {
            return $sequencialPorData;
        }

        return $this->sequencialAlunoDependenciaAposRegular();
    }

    private function sequencialAlunoDependenciaAposRegular()
    {
        $sql = "SELECT MAX(sequencial_fechamento)+1 as sequencial
            FROM pmieducar.matricula_turma
            INNER JOIN pmieducar.matricula ON (matricula.cod_matricula = matricula_turma.ref_cod_matricula)
            INNER JOIN pmieducar.aluno ON (aluno.cod_aluno = matricula.ref_cod_aluno)
            INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
            WHERE matricula.ativo = 1
            AND ref_cod_turma = {$this->refCodTurma}
            AND (CASE
                WHEN matricula_turma.ativo = 1 THEN TRUE
                WHEN matricula_turma.transferido THEN TRUE
                WHEN matricula_turma.remanejado THEN TRUE
                WHEN matricula.dependencia THEN TRUE
                WHEN matricula_turma.abandono THEN TRUE
                WHEN matricula_turma.reclassificado THEN TRUE
                ELSE FALSE
            END)
            AND matricula.dependencia = false";

        return DB::selectOne($sql)->sequencial ?: 1;
    }

    private function sequencialAlunoDependenciaOrdemAlfabetica()
    {
        $sql = "SELECT MAX(sequencial_fechamento)+1 as sequencial
            FROM pmieducar.matricula_turma
            INNER JOIN pmieducar.matricula ON (matricula.cod_matricula = matricula_turma.ref_cod_matricula)
            INNER JOIN pmieducar.aluno ON (aluno.cod_aluno = matricula.ref_cod_aluno)
            INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
            WHERE matricula.ativo = 1
            AND ref_cod_turma = {$this->refCodTurma}
            AND (CASE
                WHEN matricula_turma.ativo = 1 THEN TRUE
                WHEN matricula_turma.transferido THEN TRUE
                WHEN matricula_turma.remanejado THEN TRUE
                WHEN matricula.dependencia THEN TRUE
                WHEN matricula_turma.abandono THEN TRUE
                WHEN matricula_turma.reclassificado THEN TRUE
                ELSE FALSE
            END)
            AND matricula.dependencia = true
            AND matricula_turma.data_enturmacao <= '{$this->dataEnturmacao}'
            AND (CASE
                    WHEN matricula_turma.data_enturmacao = '{$this->dataEnturmacao}'
                        THEN pessoa.nome <= (
                            SELECT pessoa.nome
                            FROM pmieducar.matricula
                            INNER JOIN pmieducar.aluno ON (aluno.cod_aluno = matricula.ref_cod_aluno)
                            INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
                            WHERE matricula.cod_matricula = {$this->refCodMatricula}
                        )
                     ELSE TRUE
                 END)";

        return DB::selectOne($sql)->sequencial;;
    }


    private function sequencialAlunoDependenciaPorData()
    {
        $sql = "SELECT MAX(sequencial_fechamento)+1 as sequencial
            FROM pmieducar.matricula_turma
            INNER JOIN pmieducar.matricula ON (matricula.cod_matricula = matricula_turma.ref_cod_matricula)
            INNER JOIN pmieducar.aluno ON (aluno.cod_aluno = matricula.ref_cod_aluno)
            INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
            WHERE matricula.ativo = 1
            AND ref_cod_turma = {$this->refCodTurma}
            AND (CASE
                WHEN matricula_turma.ativo = 1 THEN TRUE
                WHEN matricula_turma.transferido THEN TRUE
                WHEN matricula_turma.remanejado THEN TRUE
                WHEN matricula.dependencia THEN TRUE
                WHEN matricula_turma.abandono THEN TRUE
                WHEN matricula_turma.reclassificado THEN TRUE
                ELSE FALSE
            END)
            AND matricula.dependencia = true
            AND matricula_turma.data_enturmacao <= '{$this->dataEnturmacao}'
            AND (CASE
                    WHEN matricula_turma.data_enturmacao = '{$this->dataEnturmacao}'
                        THEN pessoa.nome <= (
                            SELECT pessoa.nome
                            FROM pmieducar.matricula
                            INNER JOIN pmieducar.aluno ON (aluno.cod_aluno = matricula.ref_cod_aluno)
                            INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
                            WHERE matricula.cod_matricula = {$this->refCodMatricula}
                        )
                     ELSE TRUE
                 END)";

        return DB::selectOne($sql)->sequencial;;
    }

    private function sequencialAlunoOrdemAlfabetica()
    {
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

        $students = DB::select($sql);

        $alunos = [];

        foreach ($students as $student) {
            $sequencial = $student->sequencial_fechamento;
            $alunos[$sequencial] = strtoupper($student->nome);
        }

        $nome = $this->registration->student->person->name;

        $alunos['novo-aluno'] = limpa_acentos(strtoupper($nome));

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

    private function somaSequencialPosterior($sequencial)
    {
        DB::update(
            '
                UPDATE pmieducar.matricula_turma
                SET sequencial_fechamento = sequencial_fechamento + 1
                WHERE ref_cod_turma = ?
                AND sequencial_fechamento >= ?
            ', [
                $this->refCodTurma, $sequencial
            ]
        );
    }

    private function subtraiSequencialPosterior($sequencial)
    {
        DB::update(
            '
                UPDATE pmieducar.matricula_turma
                SET sequencial_fechamento = sequencial_fechamento - 1
                WHERE ref_cod_turma = ?
                AND sequencial_fechamento > ?
            ', [
                $this->refCodTurma, $sequencial
            ]
        );
    }

    private function enturmarPorUltimo()
    {
        $enturmarPorUltimo = false;

        $institution = $this->schoolClass->school->institution;

        $dataFechamento = $institution->data_fechamento;

        $possuiDataFechamento = is_string($dataFechamento);

        if ($possuiDataFechamento) {
            $ano = $this->registration->year;

            $dataFechamento = explode('-', $dataFechamento);

            $dataFechamento = $ano . '-' . $dataFechamento[1] . '-' . $dataFechamento[2];

            if (strtotime($dataFechamento) < strtotime($this->dataEnturmacao)) {
                $enturmarPorUltimo = true;
            }
        }

        $dataBaseTransferencia = $institution->data_base_transferencia;

        if ($dataBaseTransferencia && $this->existeMatriculaTransferidaAno()) {
            if (strtotime($dataBaseTransferencia) < strtotime($this->dataEnturmacao)) {
                $enturmarPorUltimo = true;
            }
        }

        if ($dataBaseRemanejamento = $this->getRelocationDate()) {
            if (strtotime($dataBaseRemanejamento) < strtotime($this->dataEnturmacao)) {
                $enturmarPorUltimo = true;
            }
        }

        return $enturmarPorUltimo;
    }

    private function existeMatriculaTransferidaAno()
    {
        $codAluno = $this->registration->ref_cod_aluno;

        $ano = $this->getAnoMatricula();

        return (bool) DB::selectOne("SELECT 1 FROM pmieducar.matricula WHERE ref_cod_aluno = {$codAluno} AND ano = {$ano} AND aprovado = 4");
    }

    public function getAnoMatricula()
    {
        return $this->registration->year;
    }

    public function matriculaDependencia()
    {
        return $this->registration->is_dependency;
    }

    public function getRelocationDate()
    {
        $date = $this->schoolClass->school->institution->relocation_date;
        if ($date) {
            $date = substr($this->dataEnturmacao, 0, 4) . $date->format('-m-d');
        }

        $newDate = new DateTime($date);
        $day = $newDate->format('d');

        if (date('L', $newDate->getTimestamp()) == 0 && $day == '29') {
            return $newDate->modify('-1 day')->format('Y-m-d');
        }

        return $newDate->format('Y-m-d');
    }

    public function existeMatriculaTurma()
    {
        $result = DB::selectOne(
            '
                SELECT sequencial_fechamento
                FROM pmieducar.matricula_turma
                INNER JOIN pmieducar.matricula
                ON matricula.cod_matricula = matricula_turma.ref_cod_matricula
                WHERE matricula.ativo = 1
                AND ref_cod_matricula = ?
                AND ref_cod_turma = ?
                AND (
                    CASE
                        WHEN matricula_turma.ativo = 1 THEN TRUE
                        WHEN matricula_turma.transferido THEN TRUE
                        WHEN matricula_turma.remanejado THEN TRUE
                        WHEN matricula.dependencia THEN TRUE
                        WHEN matricula_turma.abandono THEN TRUE
                        WHEN matricula_turma.reclassificado THEN TRUE
                        ELSE FALSE
                    END
                )
            ', [
                $this->refCodMatricula, $this->refCodTurma
            ]
        );

        if (empty($result)) {
            return null;
        }

        return $result->sequencial_fechamento;
    }
}
