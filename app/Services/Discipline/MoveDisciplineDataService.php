<?php

namespace App\Services\Discipline;

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

            $this->moveData($disciplineFrom, $disciplineTo, $year, $gradeId);
        }
    }

    private function moveData($disciplineFrom, $disciplineTo, $gradeId, $year)
    {
        foreach ($this->moveDataServices as $moveDataService) {
            $moveDataService->moveData($disciplineFrom, $disciplineTo, $gradeId, $year);
        }
    }

    public function setDefaultCopiers()
    {
        $this->moveDataServices = [

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
