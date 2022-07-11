<?php

namespace App\Http\Controllers\Api\Resource\Discipline;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Resource\Discipline\ResourceDisciplineRequest;
use App\Models\LegacyDisciplineAcademicYear;
use App\Models\LegacySchoolGradeDiscipline;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceDisciplineController extends Controller
{
    public function index(ResourceDisciplineRequest $request): JsonResource
    {
        $grade = $request->get('grade');
        $course = $request->get('course');
        $school = $request->get('school');

        if ($school && $grade) {
            $disciplines = LegacySchoolGradeDiscipline::distinctDiscipline()
                ->select(['ref_cod_disciplina as id', 'carga_horaria as workload'])->addSelectName()
                ->whereSchool($school)->whereGrade($grade)->get();
        } else {
            $disciplines = LegacyDisciplineAcademicYear::distinctDiscipline()
                ->select(['componente_curricular_id as id', 'carga_horaria as workload'])->addSelectName()
                ->whereCourse($course)->whereGrade($grade)->get();
        }

        JsonResource::withoutWrapping();
        return JsonResource::collection($disciplines);
    }
}
