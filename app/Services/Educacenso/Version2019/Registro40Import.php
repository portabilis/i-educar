<?php

namespace App\Services\Educacenso\Version2019;

use App\Models\Educacenso\Registro40;
use App\Models\Educacenso\RegistroEducacenso;
use App\Models\Employee;
use App\Models\EmployeeInep;
use App\Models\LegacyInstitution;
use App\Models\LegacySchool;
use App\Models\SchoolInep;
use App\Models\SchoolManager;
use App\Services\Educacenso\RegistroImportInterface;
use App\Services\Educacenso\Version2019\Models\Registro40Model;
use App\User;

class Registro40Import implements RegistroImportInterface
{
    /**
     * @var Registro40
     */
    protected $model;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var LegacyInstitution
     */
    protected $institution;

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
        $manager->link_type_id = (int) $this->model->tipoVinculo ?: null;
        if (!$this->existsChiefSchoolManager($school)) {
            $manager->chief = true;
        }

        $manager->saveOrFail();
    }

    protected function existsChiefSchoolManager(LegacySchool $school): bool
    {
        return $school->schoolManagers()->where('chief', true)->exists();
    }

    /**
     * @return LegacySchool
     */
    protected function getSchool(): ?LegacySchool
    {
        $schoolInep = SchoolInep::where('cod_escola_inep', $this->model->inepEscola)->first();
        if ($schoolInep) {
            return $schoolInep->school;
        }

        return null;
    }
}
