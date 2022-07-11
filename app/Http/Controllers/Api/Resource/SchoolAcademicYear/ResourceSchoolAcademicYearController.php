<?php

namespace App\Http\Controllers\Api\Resource\SchoolAcademicYear;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Resource\SchoolAcademicYear\ResourceSchoolAcademicYearRequest;
use App\Models\LegacySchoolAcademicYear;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceSchoolAcademicYearController extends Controller
{
    public function index(ResourceSchoolAcademicYearRequest $request): JsonResource
    {
        $school = $request->get('school');
        $year = $request->get('year');
        $limit = $request->get('limit');

        $school_academic_years = LegacySchoolAcademicYear::select('ano as year')
            ->whereSchool($school)->whereGteYear($year)
            ->active()->orderByYear()
            ->when($limit, fn($q) => $q->limit($limit))
            ->get();

        JsonResource::withoutWrapping();
        return JsonResource::collection($school_academic_years);
    }
}
