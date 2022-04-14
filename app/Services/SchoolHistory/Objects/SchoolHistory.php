<?php

namespace App\Services\SchoolHistory\Objects;

use App\Models\SchoolHistoryStatus;
use App\Services\SchoolHistory\SchoolHistoryFooter;
use App\Services\SchoolHistory\SchoolHistoryService;
use DateTime;

class SchoolHistory
{
    private $service;
    private $seriesYearsModel;
    private $certificationText;
    private $formatScoresGreaterThanTen;
    public $disciplines;

    public const GRADE_SERIE = 1;
    public const GRADE_ANO = 2;
    public const GRADE_EJA = 3;

    public function __construct(SchoolHistoryService $service, $seriesYearsModel)
    {
        $this->service = $service;
        $this->seriesYearsModel = $seriesYearsModel;
    }

    public function setFormatScoresGreaterThanTen($formatScoresGreaterThanTen)
    {
        $this->formatScoresGreaterThanTen = $formatScoresGreaterThanTen;
    }

    public function addDataGroupByDiscipline($data)
    {
        $column = $this->getColumn($data['nm_serie'], $data['historico_grade_curso_id']);

        if (!$column) {
            return;
        }

        $discipline = $this->getDiscipline($data['cod_aluno'], $data['nm_disciplina']);

        $discipline->nm_disciplina = $data['nm_disciplina'];
        $discipline->cod_aluno = $data['cod_aluno'];
        $discipline->nome_aluno = $data['nome_aluno'];
        $discipline->nm_escola = $data['nm_escola'];
        $discipline->cod_inep = $data['cod_inep'];
        $discipline->cidade_nascimento_uf = $data['cidade_nascimento_uf'];
        $discipline->uf_nascimento = $data['uf_nascimento'];
        $discipline->cidade_nascimento = $data['cidade_nascimento'];
        $discipline->data_nasc = (new DateTime($data['data_nasc']))->format('d/m/Y');
        $discipline->nome_do_pai = $data['nome_do_pai'];
        $discipline->nome_da_mae = $data['nome_da_mae'];
        $discipline->data_atual = $data['data_atual'];
        $discipline->data_atual_extenso = $data['data_atual_extenso'];
        $discipline->nome_serie_aux = $this->certificationText;
        $discipline->municipio = $data['municipio'];
        $discipline->ato_poder_publico = $data['ato_poder_publico'];
        $discipline->ato_autorizativo = $data['ato_autorizativo'];

        $discipline->addLastCourseName($data['nome_curso']);
        $discipline->addColumnYear($column, $data['ano']);
        $discipline->addColumnSchool($column, $data['escola']);
        $discipline->addColumnSchoolTown($column, $data['escola_cidade']);
        $discipline->addColumnSchoolFS($column, $data['escola_uf']);
        $discipline->addLastRecord($data['registro']);
        $discipline->addLastBook($data['livro']);
        $discipline->addLastSchoolSheet($data['folha']);
        $discipline->addColumnRecord($column, $data['registro']);
        $discipline->addColumnBook($column, $data['livro']);
        $discipline->addColumnSchoolSheet($column, $data['folha']);
        $discipline->addColumnWorkload($column, $data['carga_horaria']);
        $discipline->addColumnAttendance($column, $data['frequencia']);
        $discipline->addColumnSchoolDays($column, $data['dias_letivos']);
        $discipline->addColumnGeneralAbsence($column, $data['faltas_globalizadas']);
        $discipline->addColumnStatus($column, $this->getStatus($data['aprovado']));
        $discipline->addColumnTransferred($column, $data['aprovado'] == 4);
        $discipline->addColumnScore($column, $this->getFormattedScore($data['nota']));
        $discipline->addColumnAbsence($column, $data['faltas']);
        $discipline->addColumnDisciplineWorkload($column, $data['carga_horaria_disciplina']);
        $discipline->addColumnDependencyDiscipline($column, $data['disciplina_dependencia']);

        $this->disciplines[$data['cod_aluno']][$data['nm_disciplina']] = $discipline;
    }

    public function getDiscipline($studentId, $disciplineName)
    {
        if (!$this->disciplines[$studentId][$disciplineName]) {
            $this->disciplines[$studentId][$disciplineName] = new DisciplineGroup();
        }

        return $this->disciplines[$studentId][$disciplineName];
    }

    public function getLines()
    {
        $lines = [];
        foreach ($this->disciplines as $student) {
            $lines = array_merge($lines, $this->getDisciplinesByStudent($student));
        }

        return $lines;
    }

    private function getDisciplinesByStudent($student)
    {
        $lines = [];
        foreach ($student as $discipline) {
            $lines[] = (array) $discipline;
        }

        return $lines;
    }

    public function makeBlankSpaceObservations($templateName)
    {
        foreach ($this->disciplines as $key => $student) {
            $studentKey = array_key_last($student);
            $numberOfDisciplines = count($student);
            $this->disciplines[$key][$studentKey]->espaco_branco = $this->service->getBlankSpace($templateName, $numberOfDisciplines, 12);
        }
    }

    public function defineIfHasScoreGreaterThanTen($hasGreaterScoreThanTen)
    {
        foreach ($this->disciplines as $key => $student) {
            $studentKey = array_key_last($student);
            $this->disciplines[$key][$studentKey]->qtde_notas_maiores_dez = $hasGreaterScoreThanTen;
        }
    }

    public function makeAllObservations()
    {
        foreach ($this->disciplines as $key => $student) {
            $studentKey = array_key_last($student);
            $this->disciplines[$key][$studentKey]->observacao_all = $this->service->getAllObservationsByStudent($key);
        }
    }

    public function makeBolsaFamilia()
    {
        foreach ($this->disciplines as $key => $student) {
            $studentKey = array_key_last($student);
            $this->disciplines[$key][$studentKey]->obs_bolsa_familia = $this->service->getBolsaFamiliaText($key);
        }
    }

    public function makeFooterData()
    {
        $schoolHistoryFooter = new SchoolHistoryFooter($this->disciplines);
        $this->disciplines = $schoolHistoryFooter->insertFooterDataInLastStudentDiscipline();
    }

    public function getColumn($levelName, $gradeType)
    {
        if (!$this->service->isValidLevelName($levelName, $gradeType)) {
            return;
        }

        $column = $this->service->getLevelByName($levelName);

        if ($this->service->isEightYears($gradeType) && $this->seriesYearsModel) {
            return $column + 1;
        }

        return $column;
    }

    public function getFormattedScore($score)
    {
        if (is_numeric($score)) {
            $score = str_replace('.', ',', $score);
        }

        if ($this->formatScoresGreaterThanTen && $score > 10) {
            $score = '*' . $score;
        }

        return $score;
    }

    public function makeTextoCertificacao($data)
    {
        $this->certificationText = $this->service->getCertificationText($data);
    }

    public function getStatus($status)
    {
        $allStatus = (new SchoolHistoryStatus())->getDescriptiveValues();

        return $allStatus[$status];
    }
}
