<?php

namespace App\Services\SchoolHistory\Objects;

class DisciplineGroup
{
    public function addLastCourseName($nome_curso)
    {
        $this->nome_curso = $nome_curso;
    }

    public function addColumnYear($column, $year)
    {
        $this->{"ano_{$column}serie"} = $year;
    }

    public function addColumnSchool($column, $school)
    {
        $this->{"escola_{$column}serie"} = $school;
    }

    public function addColumnSchoolTown($column, $schoolTown)
    {
        $this->{"escola_cidade_{$column}serie"} = $schoolTown;
    }

    public function addColumnSchoolFS($column, $schoolFS)
    {
        $this->{"escola_uf_{$column}serie"} = $schoolFS;
    }

    public function addLastRecord($record)
    {
        $this->nm_termo = $record;
    }

    public function addLastBook($book)
    {
        $this->nm_livro = $book;
    }

    public function addLastSchoolSheet($sheet)
    {
        $this->nm_folha = $sheet;
    }

    public function addColumnRecord($column, $record)
    {
        $this->{"registro_{$column}serie"} = $record;
    }

    public function addColumnBook($column, $book)
    {
        $this->{"livro_{$column}serie"} = $book;
    }

    public function addColumnSchoolSheet($column, $sheet)
    {
        $this->{"folha_{$column}serie"} = $sheet;
    }

    public function addColumnWorkload($column, $workload)
    {
        $this->{"carga_horaria{$column}"} = $workload;
    }

    public function addColumnAttendance($column, $attendance)
    {
        $this->{"freq{$column}"} = $attendance;
    }

    public function addColumnSchoolDays($column, $schoolDays)
    {
        $this->{"dias_letivos{$column}"} = $schoolDays;
    }

    public function addColumnGeneralAbsence($column, $generalAbsence)
    {
        $this->{"faltas_globalizadas{$column}"} = $generalAbsence;
    }

    public function addColumnStatus($column, $status)
    {
        $this->{"status_serie{$column}"} = $status;
    }

    public function addColumnTransferred($column, $transferred)
    {
        $this->{"transferido{$column}"} = $transferred;
    }

    public function addColumnScore($column, $score)
    {
        $this->{"nota_{$column}serie"} = $score;
    }

    public function addColumnAbsence($column, $absence)
    {
        $this->{"falta_{$column}serie"} = $absence;
    }

    public function addColumnDisciplineWorkload($column, $disciplineWorkload)
    {
        $this->{"chd{$column}"} = $disciplineWorkload;
    }

    public function addColumnDependencyDiscipline($column, $dependencyDiscipline)
    {
        $this->{"disciplina_dependencia{$column}"} = $dependencyDiscipline;
    }
}
