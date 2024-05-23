<?php

namespace iEducar\Modules\Educacenso\Data;

use App\Models\Educacenso\Registro50 as Registro50Model;
use App\Services\SchoolClass\SchoolClassService;
use iEducar\Modules\Educacenso\Formatters;
use iEducar\Modules\SchoolClass\Period;
use Portabilis_Utils_Database;

class Registro50 extends AbstractRegistro
{
    use Formatters;

    /**
     * @var Registro50Model
     */
    protected $model;

    /**
     * @var Registro50Model[]
     */
    protected $modelArray;

    /**
     * @return Registro50Model[]
     */
    public function getData($escola, $ano)
    {
        $return = $this->repository->getDataForRecord50($ano, $escola);

        foreach ($return as $data) {
            $recordCopies = $this->copyByPeriod($data);
            foreach ($recordCopies as $recordCopy) {
                $this->hydrateModel($recordCopy);

                $this->model->componentes = array_unique(Portabilis_Utils_Database::pgArrayToArray($this->model->componentes));
                $this->model->unidadesCurriculares = array_unique(Portabilis_Utils_Database::pgArrayToArray($this->model->unidadesCurriculares));
                $this->model->estruturaCurricular = array_unique(Portabilis_Utils_Database::pgArrayToArray($this->model->estruturaCurricular));

                $this->modelArray[] = $this->model;
                $this->model = new Registro50Model();
            }
        }

        return $this->modelArray;
    }

    public function getExportFormatData($ano, $escola)
    {
        $modelArray = $this->getData($ano, $escola);

        return $modelArray;
    }

    private function copyByPeriod($record)
    {
        if ($record->turmaTurnoId !== Period::FULLTIME) {
            return [$record];
        }

        $service = new SchoolClassService();

        $studentPeriods = $service->getStudentsPeriods($record->codigoTurma);
        $hasPeriods = $service->hasStudentsPartials($record->codigoTurma);

        if ($hasPeriods) {
            return $studentPeriods->map(function ($periodId) use ($record) {
                $newRecord = clone $record;
                $newRecord->codigoTurma .= '-' . $periodId;

                return $newRecord;
            })->toArray();
        }

        return [$record];
    }
}
