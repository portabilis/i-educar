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
        //altera o model se school e grade
        if ($request->get('school') && $request->get('grade')) {
            return LegacySchoolGradeDiscipline::getResource();
        }

        return LegacyDisciplineAcademicYear::getResource();
    }
}
