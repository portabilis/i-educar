<?php

namespace App\Http\Controllers\Api\Resource\Grade;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Resource\Grade\ResourceGradeRequest;
use App\Models\LegacyGrade;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceGradeController extends Controller
{
    public function index(ResourceGradeRequest $request): JsonResource
    {
        $course = $request->get('course');
        $school = $request->get('school');
        $grade_exclude = $request->get('grade_exclude');
        $school_exclude = $request->get('school_exclude');

        $grades = LegacyGrade::select('cod_serie as id')->selectName()
            ->whereCourse($course)->whereNotGrade($grade_exclude)->whereSchool($school)->whereNotSchool($school_exclude)
            ->active()->orderByNameAndCourse()
            ->get();

        JsonResource::withoutWrapping();
        return JsonResource::collection($grades);
    }
}
