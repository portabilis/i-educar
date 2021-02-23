<?php

namespace App\Services\SchoolClass;

use App\Models\LegacySchoolClassGrade;
use App\Models\LegacySchoolClass;
use App\Rules\DuplicateMultiGrades;
use App\Rules\IncompatibleAbsenceType;
use App\Rules\IncompatibleDescriptiveOpinion;

class MultiGradesService
{
    private function validate($schoolClassGrades)
    {
        validator([
            'grades' => $schoolClassGrades,
        ], [
            'grades' => [
                'min:2',
                new DuplicateMultiGrades(),
                new IncompatibleAbsenceType(),
                new IncompatibleDescriptiveOpinion(),
            ]
        ], [
            'grades.min' => 'Você deve selecionar pelo menos 2 séries em turmas multisseriadas',
        ])->validate();
    }

    public function storeSchoolClassGrade(LegacySchoolClass $schoolClass, $schoolClassGrades)
    {
        $this->validate($schoolClassGrades);
        $this->deleteGradesOfSchoolClass($schoolClass, $schoolClassGrades);
        $this->saveSchoolClassGrade($schoolClass, $schoolClassGrades);
    }

    public function deleteAllGradesOfSchoolClass(LegacySchoolClass $schoolClass) {
        LegacySchoolClassGrade::query()
            ->where('turma_id', $schoolClass->getId())
            ->delete();
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
