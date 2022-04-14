<?php

namespace App\Services\Educacenso\Version2020;

use App\Models\Educacenso\Registro40;
use App\Models\Educacenso\RegistroEducacenso;
use App\Models\Employee;
use App\Models\EmployeeInep;
use App\Models\LegacyInstitution;
use App\Models\SchoolManager;
use App\Services\Educacenso\Version2019\Registro40Import as Registro40Import2019;
use App\Services\Educacenso\Version2020\Models\Registro40Model;

class Registro40Import extends Registro40Import2019
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

        $employee = $this->getEmployee();
        if (empty($employee)) {
            return;
        }

        $this->createOrUpdateManager($employee);
    }

    /**
     * @param $arrayColumns
     *
     * @return Registro40|RegistroEducacenso
     */
    public static function getModel($arrayColumns)
    {
        $registro = new Registro40Model();
        $registro->hydrateModel($arrayColumns);

        return $registro;
    }

    /**
     * @return Employee|null
     */
    private function getEmployee(): ?Employee
    {
        $inepNumber = $this->model->inepGestor;
        if (empty($inepNumber)) {
            return null;
        }

        $employeeInep = EmployeeInep::where('cod_docente_inep', $inepNumber)->first();

        if (empty($employeeInep)) {
            return null;
        }

        return $employeeInep->employee ?? null;
    }

    /**
     * @param Employee $employee
     *
     * @return void
     */
    private function createOrUpdateManager(Employee $employee): void
    {
        $school = $this->getSchool();

        if (empty($school)) {
            return;
        }

        $manager = SchoolManager::firstOrNew([
            'employee_id' => $employee->id,
            'school_id' => $school->id,
        ]);

        $manager->role_id = $this->model->cargo;
        $manager->access_criteria_id = $this->model->criterioAcesso ?: null;
        $manager->access_criteria_description = $this->model->especificacaoCriterioAcesso;

        if (!$this->existsChiefSchoolManager($school)) {
            $manager->chief = true;
        }

        $manager->saveOrFail();
    }
}
