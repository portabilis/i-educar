<?php

namespace iEducar\Modules\Educacenso\Migrations;

use App\Models\EmployeeGraduation;
use App\Services\EmployeeGraduationService;
use iEducar\Modules\ValueObjects\EmployeeGraduationValueObject;
use Illuminate\Support\Facades\DB;

class InsertEmployeeGraduations implements EducacensoMigrationInterface
{
    public static function execute()
    {
        $graduations = self::getGraduations();
        foreach ($graduations as $graduation) {
            self::storeGraduation($graduation);
        }
    }

    private static function getGraduations()
    {
        return DB::table('pmieducar.servidor')
            ->orWhereNotNull('codigo_curso_superior_1')
            ->orWhereNotNull('codigo_curso_superior_2')
            ->orWhereNotNull('codigo_curso_superior_3')
            ->get(
                [
                    'cod_servidor',
                    'codigo_curso_superior_1',
                    'ano_conclusao_curso_superior_1',
                    'instituicao_curso_superior_1',
                    'codigo_curso_superior_2',
                    'ano_conclusao_curso_superior_2',
                    'instituicao_curso_superior_2',
                    'codigo_curso_superior_3',
                    'ano_conclusao_curso_superior_3',
                    'instituicao_curso_superior_3',
                ]
            );

    }

    private static function storeGraduation($graduation)
    {
        /** @var EmployeeGraduationService $employeeGraduationService */
        $employeeGraduationService = app(EmployeeGraduationService::class);

        if (self::isValid($graduation, 1)) {
            $valueObject = new EmployeeGraduationValueObject();
            $valueObject->employeeId = $graduation->cod_servidor;
            $valueObject->courseId = $graduation->codigo_curso_superior_1;
            $valueObject->completionYear = $graduation->ano_conclusao_curso_superior_1;
            $valueObject->collegeId = $graduation->instituicao_curso_superior_1;
            $employeeGraduationService->storeGraduation($valueObject);
        }

        if (self::isValid($graduation, 2)) {
            $valueObject = new EmployeeGraduationValueObject();
            $valueObject->employeeId = $graduation->cod_servidor;
            $valueObject->courseId = $graduation->codigo_curso_superior_2;
            $valueObject->completionYear = $graduation->ano_conclusao_curso_superior_2;
            $valueObject->collegeId = $graduation->instituicao_curso_superior_2;
            $employeeGraduationService->storeGraduation($valueObject);
        }

        if (self::isValid($graduation, 3)) {
            $valueObject = new EmployeeGraduationValueObject();
            $valueObject->employeeId = $graduation->cod_servidor;
            $valueObject->courseId = $graduation->codigo_curso_superior_3;
            $valueObject->completionYear = $graduation->ano_conclusao_curso_superior_3;
            $valueObject->collegeId = $graduation->instituicao_curso_superior_3;
            $employeeGraduationService->storeGraduation($valueObject);
        }
    }

    private static function isValid($graduation, $field)
    {
        $courseProperty = "codigo_curso_superior_{$field}";
        $yearProperty = "ano_conclusao_curso_superior_{$field}";
        $collegeProperty = "instituicao_curso_superior_{$field}";

        $duplicated = EmployeeGraduation::where('course_id', $graduation->$courseProperty)->where('employee_id', $graduation->cod_servidor)->exists();
        if ($duplicated) {
            return false;
        }

        if (
            $graduation->$courseProperty &&
            $graduation->$yearProperty &&
            $graduation->$collegeProperty &&
            $graduation->$yearProperty > 1940
        ) {
            return true;
        }

        return false;
    }
}