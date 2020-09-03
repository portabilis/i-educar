<?php

namespace App\Services;

use App\Models\LegacyCourse;
use App\Models\LegacyInstitution;
use App\Models\LegacySchool;
use Portabilis_Business_Professor;

class CourseService
{
    /**
     * Retorna os cursos da escola, considerando as alocações do usuário informado
     *
     * @param $year
     * @param $userId
     * @param null $schoolId
     * @return mixed
     */
    public function getCoursesByUserAndSchool($userId, $schoolId = null, $year = null)
    {
        $institutionId = app(LegacyInstitution::class)->getKey();

        $isOnlyProfessor = Portabilis_Business_Professor::isOnlyProfessor($institutionId, $userId);

        if ($isOnlyProfessor) {
            $courses = Portabilis_Business_Professor::cursosAlocado($institutionId, $schoolId, $userId);
            return collect($courses)->reduce(function ($courses, $course) {
                $courses[] = $course['id'];
            }, []);
        }

        if ($schoolId) {
            return LegacySchool::find($schoolId)
                ->courses()
                ->isEja()
                ->when($year, function ($query) use ($year) {
                    $query->whereRaw('? = any(anos_letivos)', ['year' => $year]);
                })
                ->get()
                ->pluck('cod_curso')
                ->all();
        }

        return LegacyCourse::query()
            ->isEja()
            ->get('cod_curso')
            ->pluck('cod_curso')
            ->all();
    }
}

