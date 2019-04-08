<?php

namespace iEducar\Modules\Educacenso\Data;

use App\Models\Educacenso\RegistroEducacenso;
use App\Repositories\EducacensoRepository;

abstract class AbstractRegistro
{
    /**
     * @var EducacensoRepository
     */
    protected $repository;

    /**
     * @var RegistroEducacenso
     */
    protected $model;

    public function __construct(EducacensoRepository $repository, RegistroEducacenso $model)
    {
        $this->repository = $repository;
        $this->model = $model;
    }

    /**
     * @param $data
     */
    protected function hydrateModel($data)
    {
        foreach ($data as $field => $value) {
            if (property_exists($this->model, $field)) {
                $this->model->$field = $value;
            }
        }
    }
}
