<?php

namespace App\Services\Discipline;

use App\Contracts\Output;
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
     * @var Output
     */
    private $output;

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

            if (!is_numeric($disciplineTo) || !is_numeric($disciplineFrom)) {
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
            $updatedResources = $moveDataService->moveData($disciplineFrom, $disciplineTo, $year, $gradeId);

            $this->sendInfoMessage($disciplineFrom, $disciplineTo, get_class($moveDataService), $updatedResources);
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

    /**
     * Seta classe de output
     * @param Output $output
     */
    public function setOutput(Output $output)
    {
        $this->output = $output;
    }

    /**
     * Envia uma mensagem para o output com as informações do movimento de dados
     *
     * @param integer $disciplineFrom
     * @param integer $disciplineTo
     * @param string $copier
     * @param integer $updatedResources
     */
    private function sendInfoMessage($disciplineFrom, $disciplineTo, $copier, $updatedResources)
    {
        $this->output->info(
            sprintf('%s recursos atualizados do compoente %s para %s - %s', [
                $updatedResources,
                $disciplineFrom,
                $disciplineTo,
                $copier
            ])
        );
    }
}
