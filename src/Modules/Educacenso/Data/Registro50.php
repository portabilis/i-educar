<?php

namespace iEducar\Modules\Educacenso\Data;

use App\Models\Educacenso\Registro50 as Registro50Model;
use iEducar\Modules\Educacenso\Formatters;

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
     * @param $escola
     * @param $ano
     * @return Registro50Model[]
     */
    public function getData($escola, $ano)
    {
        $return = $this->repository->getDataForRecord50($ano, $escola);

        foreach ($return as $data) {
            $this->hydrateModel($data);
            $this->modelArray[] = $this->model;
            $this->model = new Registro50Model();
        }

        return $this->modelArray;
    }

    public function getExportFormatData($escola, $ano)
    {
        $modelArray = $this->getData($escola, $ano);

        foreach ($modelArray as $registro50) {

        }
        
        return $modelArray;
    }
}
