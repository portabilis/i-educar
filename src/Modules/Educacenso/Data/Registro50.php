<?php

namespace iEducar\Modules\Educacenso\Data;

use App\Models\Educacenso\Registro50 as Registro50Model;
use iEducar\Modules\Educacenso\Formatters;
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
     * @param $escola
     * @param $ano
     *
     * @return Registro50Model[]
     */
    public function getData($escola, $ano)
    {
        $return = $this->repository->getDataForRecord50($ano, $escola);

        foreach ($return as $data) {
            $this->hydrateModel($data);

            $this->model->componentes = array_unique(Portabilis_Utils_Database::pgArrayToArray($this->model->componentes));
            $this->model->unidadesCurriculares = array_unique(Portabilis_Utils_Database::pgArrayToArray($this->model->unidadesCurriculares));
            $this->model->estruturaCurricular = array_unique(Portabilis_Utils_Database::pgArrayToArray($this->model->estruturaCurricular));

            $this->modelArray[] = $this->model;
            $this->model = new Registro50Model();
        }

        return $this->modelArray;
    }

    public function getExportFormatData($ano, $escola)
    {
        $modelArray = $this->getData($ano, $escola);

        return $modelArray;
    }
}
