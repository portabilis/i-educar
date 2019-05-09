<?php

namespace iEducar\Modules\Educacenso\Data;

use App\Models\Educacenso\ItemOfRegistro30;
use App\Models\Educacenso\Registro30 as Registro30Model;
use App\Repositories\EducacensoRepository;
use iEducar\Modules\Educacenso\Formatters;

class Registro30 extends AbstractRegistro
{
    use Formatters;

    /**
     * @var EducacensoRepository
     */
    protected $repository;

    /**
     * @var Registro30Model
     */
    protected $model;

    /**
     * @var Registro30Model[]
     */
    protected $modelArray;

    /**
     * @param $schoolId
     * @return Registro30Model[]
     */
    public function getData($schoolId)
    {
        $arrayPersonId = $this->getArrayPersonId();

        $commonData = $this->repository->getCommonDataForRecord30($arrayPersonId, $schoolId);
        foreach ($commonData as $data) {
            $this->model = $this->modelArray[$data->codigoPessoa];
            $this->hydrateModel($data);
            $this->modelArray[$data->codigoPessoa] = $this->model;
        }

        $employeeData = $this->repository->getEmployeeDataForRecord30($arrayPersonId, $schoolId);
        foreach ($employeeData as $data) {
            $this->model = $this->modelArray[$data->codigoPessoa];
            $this->hydrateModel($data);
            $this->modelArray[$data->codigoPessoa] = $this->model;
        }

        $employeeData = $this->repository->getEmployeeDataForRecord30($arrayPersonId, $schoolId);
        foreach ($employeeData as $data) {
            $this->model = $this->modelArray[$data->codigoPessoa];
            $this->hydrateModel($data);
            $this->modelArray[$data->codigoPessoa] = $this->model;
        }

        return $this->modelArray;
    }

    /**
     * @param ItemOfRegistro30[] $array
     * @param string $type
     */
    public function setArrayDataByType($array, $type)
    {
        foreach ($array as $model) {
            if (!($model instanceof ItemOfRegistro30)) {
                continue;
            }

            $registro30Model = new Registro30Model();

            if (isset($this->modelArray[$model->getCodigoPessoa()])) {
                $registro30Model = $this->modelArray[$model->getCodigoPessoa()];
            }

            $registro30Model->codigoPessoa = $model->getCodigoPessoa();

            $registro30Model->tipos[$type] = true;
            $this->modelArray[$model->getCodigoPessoa()] = $registro30Model;
        }
    }

    /**
     * @param $escolaId
     * @return Registro30Model[]
     */
    public function getExportFormatData($escolaId)
    {
        $modelArray = $this->getData($escolaId);

        foreach ($modelArray as $registro30) {

        }

        return $modelArray;
    }

    private function getArrayPersonId()
    {
        if (empty($this->modelArray)) {
            return [];
        }

        $arrayId = [];
        foreach ($this->modelArray as $model) {
            $arrayId[] = $model->codigoPessoa;
        }

        return $arrayId;
    }
}
