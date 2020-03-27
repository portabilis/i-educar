<?php

namespace iEducar\Modules\Educacenso\Data;

use App\Models\Educacenso\Registro40 as Registro40Model;
use iEducar\Modules\Educacenso\Formatters;

class Registro40 extends AbstractRegistro
{
    use Formatters;

    /**
     * @var Registro40Model
     */
    protected $model;


    /**
     * @var Registro40Model[]
     */
    protected $modelArray;


    /**
     * @param integer $escolaId
     * @return Registro40Model[]
     */
    public function getData($escolaId)
    {
        $return = $this->repository->getDataForRecord40($escolaId);

        foreach ($return as $data) {
            $this->hydrateModel($data);
            $this->modelArray[] = $this->model;
            $this->model = new Registro40Model();
        }

        return $this->modelArray;
    }

    /**
     * @param $escolaId
     * @return Registro40Model[]
     */
    public function getExportFormatData($escolaId)
    {
        return $this->getData($escolaId);
    }
}
