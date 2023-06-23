<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyCourse;
use App\Models\LegacySchoolClassStage;
use Illuminate\Http\Request;

class StageController extends ResourceController
{
    public function index(Request $request): array
    {
        $course = LegacyCourse::query()->active()->findOrFail($request->get('course'), ['padrao_ano_escolar']);

        if ($course->isStandardCalendar) {
            return [
                'data' => LegacyAcademicYearStage::query()
                    ->filter([
                        'school' => $request->get('school'),
                        'year_eq' => $request->get('year'),
                    ])
                    ->with(['stageType:cod_modulo,nm_tipo'])
                    ->whereHas('stageType', static fn ($q) => $q->active())
                    ->orderBySequencial()
                    ->get([
                        'sequencial',
                        'ref_cod_modulo',
                    ])->mapWithKeys(static function ($academicYearStage) {
                        return [$academicYearStage->sequencial => $academicYearStage->sequencial . 'º ' . mb_strtoupper($academicYearStage->stageType->nm_tipo)];
                    }),
            ];
        }

        return [
            'data' => LegacySchoolClassStage::query()
                ->filter([
                    'school-class' => $request->get('school-class'),
                ])
                ->with(['stageType:cod_modulo,nm_tipo'])
                ->whereHas('stageType', static fn ($q) => $q->active())
                ->orderBySequencial()
                ->get([
                    'sequencial',
                    'ref_cod_modulo',
                ])->mapWithKeys(static function ($schoolClassStage) {
                    return [$schoolClassStage->sequencial => $schoolClassStage->sequencial . 'º ' . mb_strtoupper($schoolClassStage->stageType->nm_tipo)];
                }),
        ];
    }
}
