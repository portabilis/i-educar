<?php

namespace App\Services\Discipline;

use App\Models\MigratedDiscipline;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class MoveDisciplineDataService implements ToCollection
{
    /**
     * @var MoveDisciplineDataInterface[]
     */
    private $moveDataServices = [];

    /**
     * @inheritDoc
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $disciplineFrom = $row[0];
            $disciplineTo = $row[1];
            $gradeId = $row[2];
            $year = $row[3];

            if (!is_int($disciplineTo) || !is_int($disciplineFrom)) {
                continue;
            }

            MigratedDiscipline::create([
                'old_discipline_id' => $disciplineFrom,
                'new_discipline_id' => $disciplineTo,
                'grade_id' => $gradeId,
                'year' => $year,
            ]);

            $this->moveData($disciplineFrom, $disciplineTo, $year, $gradeId);
        }
    }

    public function moveData($disciplineFrom, $disciplineTo, $year, $gradeId)
    {
        foreach ($this->moveDataServices as $moveDataService) {
            $moveDataService->moveData($disciplineFrom, $disciplineTo, $year, $gradeId);
        }
    }

    public function setDefaultCopiers()
    {
        $this->moveDataServices = [
            new MoveDataTeacherDiscipline(),
            new MoveDataDisciplineScore(),
            new MoveDataDisciplineScoreAverage(),
            new MoveDataScoreExam(),
            new MoveDataDisciplineDescritiveOpinion(),
            new MoveDataDisciplineAbsence(),
            new MoveDataDisciplineExemption(),
        ];
    }

    /**
     * @param MoveDisciplineDataInterface $moveDataServices
     * @return MoveDisciplineDataService
     */
    public function setMoveDataService(MoveDisciplineDataInterface $moveDataServices): MoveDisciplineDataService
    {
        $this->moveDataServices[] = $moveDataServices;
        return $this;
    }
}
