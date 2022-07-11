<?php

namespace App\Http\Controllers\Api\Resource\SchoolClass;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Resource\SchoolClass\ResourceSchoolClassRequest;
use App\Models\LegacySchoolClass;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceSchoolClassController extends Controller
{
    public function index(ResourceSchoolClassRequest $request): JsonResource
    {
        $institution = $request->get('institution');
        $school = $request->get('school');
        $grade = $request->get('grade');
        $course = $request->get('course');
        $year = $request->get('year');

        $school_classes = LegacySchoolClass::select(['cod_turma as id'])->selectName()
            ->whereInstitution($institution)->whereSchool($school)->whereCourse($course)->whereGrade($grade)->whereInProgress($year)
            ->active()->orderByName()
            ->get();

        JsonResource::withoutWrapping();
        return JsonResource::collection($school_classes);
    }
}
