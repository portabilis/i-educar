<?php

namespace App\Http\Controllers\Api\Resource\Course;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Resource\Course\ResourceCourseRequest;
use App\Models\LegacyCourse;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceCourseController extends Controller
{
    public function index(ResourceCourseRequest $request): JsonResource
    {
        $institution = $request->get('institution');
        $school = $request->get('school');
        $notSchoolPattern = $request->get('not_pattern') === '1';
        $course = $request->get('course');

        $courses = LegacyCourse::select(['cod_curso as id', 'padrao_ano_escolar as is_standard_calendar', 'qtd_etapas as steps'])->selectName()
            ->whereCourse($course)->whereInstitution($institution)->whereSchool($school)->whereNotIsStandardCalendar($not_school_pattern)
            ->active()->orderByName()->get();

        JsonResource::withoutWrapping();
        return JsonResource::collection($courses);
    }
}
