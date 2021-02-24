<?php

namespace App\Services\SchoolClass;

use App\Models\LegacySchoolClassGrade;
use App\Models\LegacySchoolClass;
use App\Rules\DuplicateMultiGrades;
use App\Rules\IncompatibleAbsenceType;
use App\Rules\IncompatibleDescriptiveOpinion;
use App\Rules\ExistsEnrollmentsInSchoolClassGrades;

class MultiGradesService
{
    private function validate(LegacySchoolClass $schoolClass, $schoolClassGrades)
    {
        $gradesToDelete = [
            'turma' => $schoolClass,
            'grades_delete' => $this->getGradesToDelete($schoolClass, $schoolClassGrades),
        ];

        validator([
            'grades' => $schoolClassGrades,
            'grades_delete' => $gradesToDelete,
        ], [
            'grades' => [
                'min:2',
                new DuplicateMultiGrades(),
                new IncompatibleAbsenceType(),
                new IncompatibleDescriptiveOpinion(),
            ],
            'grades_delete' => [
                new ExistsEnrollmentsInSchoolClassGrades(),
            ],
        ], [
            'grades.min' => 'Você deve selecionar pelo menos 2 séries em turmas multisseriadas',
        ])->validate();
    }

    private function validateDeleteAllGrades(LegacySchoolClass $schoolClass, $gradesToDelete)
    {
        validator([
            'delete_all_grades' => [
                'turma' => $schoolClass,
                'grades_delete' => $gradesToDelete,
            ],
        ], [
            'delete_all_grades' => [
                new ExistsEnrollmentsInSchoolClassGrades(),
            ],
        ])->validate();
    }

    public function storeSchoolClassGrade(LegacySchoolClass $schoolClass, $schoolClassGrades)
    {
        $this->validate($schoolClass, $schoolClassGrades);
        $this->deleteGradesOfSchoolClass($schoolClass, $schoolClassGrades);
        $this->saveSchoolClassGrade($schoolClass, $schoolClassGrades);
    }

    public function deleteAllGradesOfSchoolClass(LegacySchoolClass $schoolClass, $excludeGrades) {
        $query = LegacySchoolClassGrade::query()
            ->where('turma_id', $schoolClass->getKey());

        $gradesToDelete = $query->whereNotIn('serie_id', $excludeGrades)
            ->get()
            ->pluck('serie_id')
            ->toArray();

        $this->validateDeleteAllGrades($schoolClass, $gradesToDelete);

        $query->delete();
    }

    private function saveSchoolClassGrade(LegacySchoolClass $schoolClass, $schoolClassGrades)
    {
        foreach ($schoolClassGrades as $schoolClassGrade) {
            LegacySchoolClassGrade::query()->firstOrCreate([
                'turma_id' => $schoolClass->getKey(),
                'escola_id' => $schoolClass->school->getKey(),
                'serie_id' => $schoolClassGrade['serie_id'],
            ], [
                'boletim_id' => $schoolClassGrade['boletim_id'],
                'boletim_diferenciado_id' => $schoolClassGrade['boletim_diferenciado_id'] ?: null,
            ]);
        }
    }

    private function deleteGradesOfSchoolClass(LegacySchoolClass $schoolClass, $schoolClassGrades)
    {
        $gradesToDelete = $this->getGradesToDelete($schoolClass, $schoolClassGrades);
        LegacySchoolClassGrade::query()
            ->where('turma_id', $schoolClass->getKey())
            ->whereIn('serie_id', $gradesToDelete)
            ->delete();
    }

    private function getGradesToDelete(LegacySchoolClass $schoolClass, $schoolClassGrades)
    {
        $newGrades = array_column($schoolClassGrades, 'serie_id');
        $oldGrades = LegacySchoolClassGrade::query()
            ->where('turma_id', $schoolClass->getKey())
            ->get()
            ->pluck('serie_id')
            ->toArray();
        
        return array_diff($oldGrades, $newGrades);
    }
}
