<?php

namespace iEducar\Modules\Educacenso\Data;

use App\Models\Educacenso\Registro10 as Registro10Model;
use iEducar\Modules\Educacenso\Formatters;
use Portabilis_Date_Utils;
use Portabilis_Utils_Database;

class Registro60 extends AbstractRegistro
{
    use Formatters;

    /**
     * @var Registro60Model
     */
    protected $model;

    /**
     * @param $school
     * @param $year
     * @return Registro60Model[]
     */
    public function getData($school, $year)
    {
        $data = $this->repository->getDataForRecord60($school, $year);

        $models = [];
        foreach ($data as $record) {
            $record = $this->processData($record);
            $models[] = $this->hydrateModel($record);
        }

        return $models;
    }

    /**
     * @param $data
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

        return $data;
    }
}
