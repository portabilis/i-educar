<?php

namespace App\Services\Discipline;

interface MoveDisciplineDataInterface
{
    public function moveData($disciplineFrom, $disciplineTo, $year, $gradeId);
}
