<?php

namespace App\Services\Educacenso\Version2020;

use App\Models\Educacenso\Registro30;
use App\Models\Educacenso\RegistroEducacenso;
use App\Models\LegacyStudent;
use App\Services\Educacenso\Version2019\Registro30Import as Registro30Import2019;
use App\Services\Educacenso\Version2020\Models\Registro30Model;

class Registro30Import extends Registro30Import2019
{
    /**
     * @var Registro30
     */
    private $model;

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
        parent::import($model, $year, $user);
    }

    /**
     * @param LegacyStudent $student
     */
    private function storeStudentData(LegacyStudent $student)
    {
        $this->createStudentInep($student);
        $this->createRecursosProvaInep($student);
        $this->createCertidaoNascimento($student);

        $student->save();
    }

    /**
     * @param $arrayColumns
     * @return Registro30|RegistroEducacenso
     */
    public static function getModel($arrayColumns)
    {
        $registro = new Registro30Model();
        $registro->hydrateModel($arrayColumns);
        return $registro;
    }
}
