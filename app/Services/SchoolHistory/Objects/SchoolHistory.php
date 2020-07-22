<?php

namespace App\Services\SchoolHistory\Objects;

use App\Services\SchoolHistory\SchoolHistoryService;
use DateTime;

class SchoolHistory
{
    private $service;
    private $seriesYearsModel;
    private $certificationText;
    public $disciplines;

    const GRADE_SERIE = 1;
    const GRADE_ANO = 2;
    const GRADE_EJA = 3;

    public function __construct(SchoolHistoryService $service, $seriesYearsModel)
    {
        $this->service = $service;
        $this->seriesYearsModel = $seriesYearsModel;
    }

    public function addDataGroupByDiscipline($data)
    {
        $discipline = $this->getDiscipline($data['cod_aluno'], $data['nm_disciplina']);
        $column = $this->getColumn($data['nm_serie'], $data['historico_grade_curso_id']);

        if (!$column) {
            return;
        }

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
        $discipline->data_atual = $data['data_atual_extenso'];
        $discipline->nome_serie_aux = $this->certificationText;
        $discipline->municipio = $data['municipio'];

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
            $this->disciplines[$studentId][$disciplineName] = new DisciplineGroup;
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
            $lines[] = (array)$discipline;
        }

        return $lines;
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
            return str_replace(".", ",", $score);
        }

        return $score;
    }

    public function makeTextoCertificacao($data)
    {
        $this->certificationText = $this->service->getCertificationText($data);
    }

    public function getStatus($status)
    {
        $allStatus = [
            1 => 'Apro',
            12 => 'AprDep',
            13 => 'AprCo',
            2 => 'Repr',
            3 => 'Curs',
            4 => 'Tran',
            5 => 'Recl',
            6 => 'Aban',
            14 => 'RpFt',
            15 => 'Fal',
        ];

        return $allStatus[$status];
    }
}
