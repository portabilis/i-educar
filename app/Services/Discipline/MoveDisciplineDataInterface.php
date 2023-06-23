<?php

namespace App\Services\Discipline;

interface MoveDisciplineDataInterface
{
    /**
     * Move os dados de uma disciplina pra outra e retorna o total de recursos atualizados
     *
     * @param int $disciplineFrom
     * @param int $disciplineTo
     * @param int $year
     * @param int $gradeId
     * @return int
     */
    public function moveData($disciplineFrom, $disciplineTo, $year, $gradeId);
}
