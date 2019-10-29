<?php

namespace App\Services\Educacenso\Version2019;

use App\Models\Educacenso\Registro10;
use App\Models\Educacenso\RegistroEducacenso;
use App\Services\Educacenso\RegistroImportInterface;
use App\User;

class Registro10Import implements RegistroImportInterface
{
    /**
     * @var Registro10
     */
    private $model;
    /**
     * @var User
     */
    private $user;
    /**
     * @var int
     */
    private $year;

    /**
     * Faz a importação dos dados a partir da linha do arquivo
     *
     * @param RegistroEducacenso $model
     * @param int $year
     * @param $user
     * @return void
     */
    public function import(RegistroEducacenso $model, $year, $user)
    {
        $this->user = $user;
        $this->model = $model;
        $this->year = $year;

        dd($this->model);
    }

    /**
     * @param $arrayColumns
     * @return Registro10|RegistroEducacenso
     */
    public static function getModel($arrayColumns)
    {
        $registro = new Registro10();
        $registro->hydrateModel($arrayColumns);
        return $registro;
    }
}
