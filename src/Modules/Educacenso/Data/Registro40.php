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
     * @param integer $escolaId
     * @return Registro40Model
     */
    public function getData($escolaId)
    {
        $data = $this->processData($this->repository->getDataForRecord40($escolaId));
        $this->hydrateModel($data[0]);

        return $this->model;
    }

    private function processData($data)
    {
        return $data;
    }
}
