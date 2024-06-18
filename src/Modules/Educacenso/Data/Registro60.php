<?php

namespace iEducar\Modules\Educacenso\Data;

use App\Models\Educacenso\Registro60 as Registro60Model;
use App\Services\SchoolClass\SchoolClassService;
use iEducar\Modules\Educacenso\Formatters;
use iEducar\Modules\SchoolClass\Period;
use Portabilis_Utils_Database;

class Registro60 extends AbstractRegistro
{
    use Formatters;

    /**
     * @var Registro60Model
     */
    protected $model;

    /**
     * @return Registro60Model[]
     */
    public function getData($school, $year)
    {
        $data = $this->repository->getDataForRecord60($school, $year);

        $models = [];
        foreach ($data as $record) {
            $record = $this->processData($record);
            $record = $this->processPeriodSchoolClass($record);
            $models[] = $this->hydrateModel($record);
        }

        return $models;
    }

    /**
     * @return Registro60Model
     */
    protected function hydrateModel($data)
    {
        $model = clone $this->model;
        foreach ($data as $field => $value) {
            if (property_exists($model, $field)) {
                $model->$field = $value;
            }
        }

        return $model;
    }

    private function processData($data)
    {
        $data->veiculoTransporteEscolar = Portabilis_Utils_Database::pgArrayToArray($data->veiculoTransporteEscolar);
        $data->estruturaCurricularTurma = Portabilis_Utils_Database::pgArrayToArray($data->estruturaCurricularTurma);
        $data->tipoAtendimentoMatricula = Portabilis_Utils_Database::pgArrayToArray($data->tipoAtendimentoMatricula);

        return $data;
    }

    public function getExportFormatData($ano, $escola)
    {
        $modelArray = $this->getData($ano, $escola);

        return $modelArray;
    }

    private function processPeriodSchoolClass($record)
    {
        if ($record->turmaTurnoId === Period::FULLTIME) {
            $service = new SchoolClassService();

            $hasPeriods = $service->hasStudentsPartials($record->codigoTurma);
            if ($hasPeriods) {
                $record->codigoTurma .= '-' . $record->turnoId;
            }
        }

        return $record;
    }
}
