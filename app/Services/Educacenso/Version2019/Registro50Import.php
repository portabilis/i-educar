<?php

namespace App\Services\Educacenso\Version2019;

use App\Models\Educacenso\Registro50;
use App\Models\Educacenso\RegistroEducacenso;
use App\Models\Employee;
use App\Models\EmployeeInep;
use App\Models\LegacySchoolClassTeacher;
use App\Models\LegacySchoolClassTeacherDiscipline;
use App\Models\LegacyInstitution;
use App\Models\LegacySchoolClass;
use App\Models\LegacyDiscipline;
use App\Models\SchoolClassInep;
use App\Services\Educacenso\RegistroImportInterface;
use App\User;

class Registro50Import implements RegistroImportInterface
{
    /**
     * @var Registro50
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
     * @var LegacyInstitution
     */
    private $institution;

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
        $this->year = $year;
        $this->user = $user;
        $this->model = $model;
        $this->model->inepTurma = $this->model->codigoTurma; // TODO: REMOVER
        $this->institution = app(LegacyInstitution::class);

        $schoolClass = $this->getSchoolClass();
        if (!$schoolClass) {
            return;
        }

        $employee = $this->getEmployee();
        if (empty($employee)) {
            return;
        }

        $this->createSchoolClassTeacher($schoolClass, $employee);
    }

    /**
     * @param $arrayColumns
     * @return Registro50|RegistroEducacenso
     */
    public static function getModel($arrayColumns)
    {
        $registro = new Registro50();
        $registro->hydrateModel($arrayColumns);
        return $registro;
    }

    /**
     * @return LegacySchoolClass
     */
    private function getSchoolClass() : ?LegacySchoolClass
    {
        return SchoolClassInep::where('cod_turma_inep', $this->model->inepTurma)->first()->schoolClass ?? null;
    }

    /**
     * @return Employee|null
     */
    private function getEmployee() : ?Employee
    {
        $inepNumber = $this->model->inepDocente;
        if (!$inepNumber) {
            return null;
        }

        $employeeInep = EmployeeInep::where('cod_docente_inep', $inepNumber)->first();
        if (empty($employeeInep)) {
            return null;
        }

        return $employeeInep->employee ?? null;
    }

    /**
     * @param $schoolClass LegacySchoolClass
     * @param $employee Employee
     * @return void
     */
    private function createSchoolClassTeacher(LegacySchoolClass $schoolClass, Employee $employee) : void
    {
        $schoolClassTeacher = LegacySchoolClassTeacher::firstOrNew([
            'ano' => $this->year,
            'instituicao_id' => $this->institution->id,
            'turma_id' => $schoolClass->id,
            'servidor_id' => $employee->id,
        ]);
        $schoolClassTeacher->funcao_exercida = $this->model->funcaoDocente;
        $schoolClassTeacher->tipo_vinculo = $this->model->tipoVinculo;
        $schoolClassTeacher->saveOrFail();

        $this->linkDisciplines($schoolClassTeacher);
    }

    /**
     * @param $schoolClassTeacher LegacySchoolClassTeacher
     * @param $employee Employee
     * @return void
     */
    private function linkDisciplines(LegacySchoolClassTeacher $schoolClassTeacher) : void
    {
        foreach ($this->model->componentes as $codigoEducacenso) {
            $discipline = LegacyDiscipline::where('codigo_educacenso', $codigoEducacenso)->first();

            if (!$discipline) {
                continue;
            }

            LegacySchoolClassTeacherDiscipline::firstOrCreate([
                'professor_turma_id' => $schoolClassTeacher->id,
                'componente_curricular_id' => $discipline->id,
            ]);
        }
    }
}
