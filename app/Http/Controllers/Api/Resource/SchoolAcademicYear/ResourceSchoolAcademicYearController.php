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
        return JsonResource::collection(LegacySchoolAcademicYear::query()->getResource($request->all()));
    }
}
