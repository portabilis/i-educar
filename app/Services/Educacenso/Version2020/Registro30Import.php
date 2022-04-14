<?php

namespace App\Services\Educacenso\Version2020;

use App\Models\Educacenso\Registro30;
use App\Models\Educacenso\RegistroEducacenso;
use App\Models\LegacyInstitution;
use App\Models\LegacyStudent;
use App\Services\Educacenso\Version2019\Registro30Import as Registro30Import2019;
use App\Services\Educacenso\Version2020\Models\Registro30Model;

class Registro30Import extends Registro30Import2019
{
    /**
     * Faz a importação dos dados a partir da linha do arquivo
     *
     * @param RegistroEducacenso $model
     * @param int                $year
     * @param                    $user
     *
     * @return void
     */
    public function import(RegistroEducacenso $model, $year, $user)
    {
        $this->user = $user;
        $this->model = $model;
        $this->institution = app(LegacyInstitution::class);

        $person = $this->getOrCreatePerson();

        $this->createRace($person);
        $this->createDeficiencies($person);

        if ($this->model->isStudent()) {
            $student = $this->getOrCreateStudent($person);
            $this->storeStudentData($student);
        }

        if ($this->model->isTeacher() || $this->model->isManager()) {
            $employee = $this->getOrCreateEmployee($person);
            $this->storeEmployeeData($employee);
        }
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
     *
     * @return Registro30|RegistroEducacenso
     */
    public static function getModel($arrayColumns)
    {
        $registro = new Registro30Model();
        $registro->hydrateModel($arrayColumns);

        return $registro;
    }
}
