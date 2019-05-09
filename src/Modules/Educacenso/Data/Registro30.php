<?php

namespace iEducar\Modules\Educacenso\Data;

use App\Models\Educacenso\ItemOfRegistro30;
use App\Models\Educacenso\Registro30 as Registro30Model;
use App\Repositories\EducacensoRepository;
use iEducar\Modules\Educacenso\Formatters;

class Registro30
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

    public function __construct(EducacensoRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param integer $escolaId
     * @return Registro30Model[]
     */
    public function getData()
    {
        $arrayPersonId = $this->getArrayPersonId();

        $return = $this->repository->getDataForRecord30($arrayPersonId);

        foreach ($return as $data) {
            $this->hydrateModel($data);
            $this->modelArray[] = $this->model;
            $this->model = new Registro30Model();
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
            $registro30Model->codigoPessoa = $model->getCodigoPessoa();
            $registro30Model->tipo = $type;
            $this->modelArray[] = $registro30Model;
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
