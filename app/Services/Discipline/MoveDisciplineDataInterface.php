<?php

namespace App\Services\Discipline;

interface MoveDisciplineDataInterface
{
    /**
     * Move os dados de uma disciplina pra outra e retorna o total de recursos atualizados
     *
     * @param integer $disciplineFrom
     * @param integer $disciplineTo
     * @param integer $year
     * @param integer $gradeId
     *
     * @return integer
     */
    public function moveData($disciplineFrom, $disciplineTo, $year, $gradeId);
}
