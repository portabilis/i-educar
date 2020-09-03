<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LegacySchoolClass;
use App\Services\CourseService;
use App\Services\SchoolClassService;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function getCalendars(Request $request, SchoolClassService $schoolClassService, CourseService $courseService)
    {
        $courses = $this->getCourses($request, $courseService);

        $schoolClass = LegacySchoolClass::query()
            ->where('ano', $request->get('ano'))
            ->whereIn('ref_cod_curso', $courses)
            ->get(['cod_turma'])->pluck('cod_turma')->all();

        return $schoolClassService->getCalendars($schoolClass);
    }

    private function getCourses(Request $request, CourseService $courseService)
    {
        $curso = $request->get('ref_cod_curso');
        if ($curso) {
            return [$curso];
        }

        $ano = $request->get('ano') ?: date('Y');
        $escola = $request->get('ref_cod_escola');
        $user = $request->get('user');

        return $courseService->getCoursesByUserAndSchool($user, $escola, $ano);
    }
}
