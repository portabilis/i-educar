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
     * @var array
     */
    private $messages;

    public function __construct(Output $output)
    {
        $this->output = $output;
    }

    /**
     * {@inheritDoc}
     */
    public function collection(Collection $rows)
    {
        $this->output->progressStart($rows->count());

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

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->sendLog();
    }

    public function moveData($disciplineFrom, $disciplineTo, $year, $gradeId)
    {
        foreach ($this->moveDataServices as $moveDataService) {
            $updatedResources = $moveDataService->moveData($disciplineFrom, $disciplineTo, $year, $gradeId);

            $this->addInfoMessage($disciplineFrom, $disciplineTo, get_class($moveDataService), $updatedResources);
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

    public function setMoveDataService(MoveDisciplineDataInterface $moveDataServices): MoveDisciplineDataService
    {
        $this->moveDataServices[] = $moveDataServices;

        return $this;
    }

    /**
     * Adiciona uma mensagem de informação
     *
     * @param int $disciplineFrom
     * @param int $disciplineTo
     * @param string  $copier
     * @param int $updatedResources
     */
    private function addInfoMessage($disciplineFrom, $disciplineTo, $copier, $updatedResources)
    {
        $this->messages[$copier][] =
            sprintf(
                '%s recursos atualizados do componente %s para %s',
                $updatedResources,
                $disciplineFrom,
                $disciplineTo
            );
    }

    /**
     * Envia uma mensagem para o output com as informações do movimento de dados
     */
    private function sendLog()
    {
        foreach ($this->messages as $copier => $copierMessages) {
            $this->output->info('---- ' . $copier . ' ----');

            foreach ($copierMessages as $message) {
                $this->output->info($message);
            }
        }
    }
}
