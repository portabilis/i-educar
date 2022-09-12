<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LegacySchoolClass;
use App\Services\SchoolClass\SchoolClassService;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function getCalendars(Request $request, SchoolClassService $schoolClassService)
    {
        if (request()->get('ref_cod_turma')) {
            return $schoolClassService->getCalendars([request()->get('ref_cod_turma')]);
        }

        $schoolClass = LegacySchoolClass::query()
            ->where('ano', $request->get('ano'))
            ->whereHas('course', function ($courseQuery) {
                $courseQuery->isEja();
            })
            ->when($request->get('ref_cod_escola'), function ($query) {
                $query->where('ref_ref_cod_escola', request()->get('ref_cod_escola'));
            })
            ->when($request->get('ref_cod_serie'), function ($query) {
                $query->where('ref_ref_cod_serie', request()->get('ref_cod_serie'));
            })
            ->when($request->get('ref_cod_curso'), function ($query) {
                $query->where('ref_cod_curso', request()->get('ref_cod_curso'));
            })
            ->get(['cod_turma'])->pluck('cod_turma')->all();

        return $schoolClassService->getCalendars($schoolClass);
    }

    public function getStages(LegacySchoolClass $schoolClass)
    {
        return $schoolClass->stages;
    }
}
