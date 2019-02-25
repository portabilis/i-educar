<?php

namespace iEducar\Modules\Educacenso\Data;

use App\Models\Educacenso\Registro00 as Registro00Model;

class Registro00 extends AbstractRegistro
{

    /**
     * @var Registro00Model
     */
    protected $model;

    /**
     * @param $escola
     * @param $ano
     * @return Registro00Model
     */
    public function getData($escola, $ano)
    {
        $data = $this->repository->getDataForRecord00($escola, $ano);

        $this->hydrateModel($data[0]);

        return $this->model;
    }
}
