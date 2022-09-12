<?php

namespace App\Services\Educacenso\Version2022;

use App\Models\Educacenso\Registro50;
use App\Models\Educacenso\RegistroEducacenso;
use App\Models\LegacySchoolClassTeacher;
use App\Services\Educacenso\Version2019\Registro50Import as Registro50Import2019;
use App\Services\Educacenso\Version2022\Models\Registro50Model;

class Registro50Import extends Registro50Import2019
{
    public function import(RegistroEducacenso $model, $year, $user)
    {
        $this->user = $user;
        $this->model = $model;
        $this->year = $year;

        parent::import($model, $year, $user);

        $employee = parent::getEmployee();
        $schoolClass = $this->getSchoolClass();
        $schoolClassTeacher = LegacySchoolClassTeacher::where('turma_id', $schoolClass->getKey())
            ->where('servidor_id', $employee->getKey())
            ->first();
        $schoolClassTeacher->unidades_curriculares = transformDBArrayInString($model->unidadesCurriculares) ?: null;

        $schoolClassTeacher->save();
    }

    /**
     * @param $arrayColumns
     *
     * @return Registro50|RegistroEducacenso
     */
    public static function getModel($arrayColumns)
    {
        $registro = new Registro50Model();
        $registro->hydrateModel($arrayColumns);

        return $registro;
    }
}
