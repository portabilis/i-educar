<?php

namespace App\Services\Educacenso\Version2019;

use App\Models\Educacenso\Registro30;
use App\Models\Educacenso\RegistroEducacenso;
use App\Services\Educacenso\RegistroImportInterface;
use App\User;

class Registro30Import implements RegistroImportInterface
{
    /**
     * @var Registro30
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
     * @return Registro30|RegistroEducacenso
     */
    public static function getModel($arrayColumns)
    {
        $registro = new Registro30();
        $registro->hydrateModel($arrayColumns);
        return $registro;
    }
}
