<?php

namespace App\Services\SchoolHistory\Objects;

use DateTime;

class SchoolHistory
{
    private $studentId;
    private $seriesYearsModel;
    public $disciplines;

    const GRADE_SERIE = 1;
    const GRADE_ANO = 2;
    const GRADE_EJA = 3;

    public function __construct($studentId, $seriesYearsModel)
    {
        $this->studentId = $studentId;
        $this->seriesYearsModel = $seriesYearsModel;
    }

    public function addDataGroupByDiscipline($data)
    {
        $discipline = $this->getDiscipline($data['nm_disciplina']);
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
        $discipline->data_nasc = $this->getFormattedDate($data['data_nasc']);
        $discipline->nome_do_pai = $data['nome_do_pai'];
        $discipline->nome_da_mae = $data['nome_da_mae'];
        $discipline->obsevacao_all .= $this->getFormattedNotes($data['observacao']);
        $discipline->data_atual_extenso = $data['data_atual_extenso'];

        $discipline->addColumnYear($column, $data['ano']);
        $discipline->addColumnSchool($column, $data['escola']);
        $discipline->addColumnSchoolTown($column, $data['escola_cidade']);
        $discipline->addColumnSchoolFS($column, $data['escola_uf']);
        $discipline->addColumnRecord($column, $data['registro']);
        $discipline->addColumnBook($column, $data['livro']);
        $discipline->addColumnSchoolSheet($column, $data['folha']);
        $discipline->addColumnWorkload($column, $data['carga_horaria']);
        $discipline->addColumnAttendance($column, $data['frequencia']);
        $discipline->addColumnSchoolDays($column, $data['dias_letivos']);
        $discipline->addColumnGeneralAbsence($column, $data['faltas_globalizadas']);
        $discipline->addColumnStatus($column, $data['aprovado']);
        $discipline->addColumnTransferred($column, $data['aprovado'] == 4);
        $discipline->addColumnScore($column, $this->getFormattedScore($data['nota']));
        $discipline->addColumnAbsence($column, $data['faltas']);
        $discipline->addColumnDisciplineWorkload($column, $data['carga_horaria_disciplina']);
        $discipline->addColumnDependencyDiscipline($column, $data['disciplina_dependencia']);

        $this->disciplines[$data['nm_disciplina']] = $discipline;
    }

    public function getDiscipline($disciplineName)
    {
        if (!$this->disciplines[$disciplineName]) {
            $this->disciplines[$disciplineName] = new DisciplineGroup;
        }

        return $this->disciplines[$disciplineName];
    }

    public function getLines()
    {
        $lines = [];
        foreach ($this->disciplines as $discipline) {
            $lines[] = (array)$discipline;
        }

        return $lines;
    }

    public function getColumn($gradeName, $gradeType)
    {
        $column = substr($gradeName, 0, 1);

        if (!is_numeric($column)) {
            return;
        }

        if ($gradeType == SchoolHistory::GRADE_SERIE && $this->seriesYearsModel) {
            return $column + 1;
        }

        return $column;
    }

    public function getFormattedDate($dataField)
    {
        return (new DateTime($dataField))->format('d/m/Y');
    }

    public function getFormattedNotes($note)
    {
        if ($note) {
            return $note . ' <br> ';
        }
    }

    public function getFormattedScore($score)
    {
        if (is_numeric($score)) {
            return str_replace(".", ",", $score);
        }

        return $score;
    }
}
