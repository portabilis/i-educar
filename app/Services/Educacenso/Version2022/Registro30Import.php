<?php

namespace App\Services\Educacenso\Version2022;

use App\Models\Educacenso\Registro30;
use App\Models\Educacenso\RegistroEducacenso;
use App\Models\Employee;
use App\Services\Educacenso\Version2020\Registro30Import as Registro30Import2020;
use App\Services\Educacenso\Version2022\Models\Registro30Model;
use App\Services\EmployeePosgraduateService;
use iEducar\Modules\ValueObjects\EmployeePosgraduateValueObject;

class Registro30Import extends Registro30Import2020
{
    public function import(RegistroEducacenso $model, $year, $user)
    {
        $this->user = $user;
        $this->model = $model;
        $this->year = $year;
        parent::import($model, $year, $user);

        $model = $this->model;

        if ($this->model->isTeacher() || $this->model->isManager()) {
            $person = parent::getPerson();
            $employee = Employee::find($person->idpes);
            $employee->complementacao_pedagogica = transformDBArrayInString($this->model->complementacaoPedagogica);
            $employee->save();
            $this->storePosgraduate($employee);
        }
    }

    /**
     * @return Registro30|RegistroEducacenso
     */
    public static function getModel($arrayColumns)
    {
        $registro = new Registro30Model();
        $registro->hydrateModel($arrayColumns);

        return $registro;
    }

    protected function storePosgraduate($employee)
    {
        if (empty($this->model->posGraduacoes)) {
            return;
        }

        /** @var EmployeePosgraduateService $employeePosgraduateService */
        $employeePosgraduateService = app(EmployeePosgraduateService::class);

        foreach ($this->model->posGraduacoes as $posgraducao) {
            $valueObject = new EmployeePosgraduateValueObject();
            $valueObject->employeeId = $employee->id;
            $valueObject->entityId = $this->institution->getKey();
            $valueObject->typeId = $posgraducao['tipo'] ?: null;
            $valueObject->areaId = $posgraducao['area'] ?: null;
            $valueObject->completionYear = $posgraducao['ano_conclusao'] ?: null;
            $employeePosgraduateService->storePosgraduate($valueObject);
        }
    }
}
