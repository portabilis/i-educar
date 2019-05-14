<?php

use App\Services\EmployeeGraduationService;
use iEducar\Modules\ValueObjects\EmployeeGraduationValueObject;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class InsertEmployeeGraduations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $graduations = $this->getGraduations();
        foreach ($graduations as $graduation) {
            $this->storeGraduation($graduation);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }

    private function getGraduations()
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

    private function storeGraduation($graduation)
    {
        /** @var EmployeeGraduationService $employeeGraduationService */
        $employeeGraduationService = app(EmployeeGraduationService::class);

        if ($this->isValid($graduation, 1)) {
            $valueObject = new EmployeeGraduationValueObject();
            $valueObject->employeeId = $graduation->cod_servidor;
            $valueObject->courseId = $graduation->codigo_curso_superior_1;
            $valueObject->completionYear = $graduation->ano_conclusao_curso_superior_1;
            $valueObject->collegeId = $graduation->instituicao_curso_superior_1;
            $employeeGraduationService->storeGraduation($valueObject);
        }

        if ($this->isValid($graduation, 2)) {
            $valueObject = new EmployeeGraduationValueObject();
            $valueObject->employeeId = $graduation->cod_servidor;
            $valueObject->courseId = $graduation->codigo_curso_superior_2;
            $valueObject->completionYear = $graduation->ano_conclusao_curso_superior_2;
            $valueObject->collegeId = $graduation->instituicao_curso_superior_2;
            $employeeGraduationService->storeGraduation($valueObject);
        }

        if ($this->isValid($graduation, 3)) {
            $valueObject = new EmployeeGraduationValueObject();
            $valueObject->employeeId = $graduation->cod_servidor;
            $valueObject->courseId = $graduation->codigo_curso_superior_3;
            $valueObject->completionYear = $graduation->ano_conclusao_curso_superior_3;
            $valueObject->collegeId = $graduation->instituicao_curso_superior_3;
            $employeeGraduationService->storeGraduation($valueObject);
        }
    }

    private function isValid($graduation, $field)
    {
        $courseProperty = "codigo_curso_superior_{$field}";
        $yearProperty = "ano_conclusao_curso_superior_{$field}";
        $collegeProperty = "instituicao_curso_superior_{$field}";

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
