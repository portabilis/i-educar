<?php

namespace App\Services\SchoolClass;

use App\Models\LegacySchoolClassGrade;
use App\Models\LegacySchoolClass;
use iEducar\Modules\SchoolClass\Validator\MultiGradesValidator;

class MultiGradesService
{
    private $schoolClassGrades;

    public function __construct($schoolClassGrades)
    {
        $this->schoolClassGrades = $schoolClassGrades;
    }

    public function storeSchoolClassGrade()
    {
        $this->deleteGradesOfSchoolClass($this->schoolClassGrades);
        $this->saveSchoolClassGrade($this->schoolClassGrades);
    }

    public function deleteAllGradesOfSchoolClass(LegacySchoolClass $schoolClass) {
        LegacySchoolClassGrade::query()
            ->where('turma_id', $schoolClass->getId())
            ->delete();
    }

    private function saveSchoolClassGrade()
    {
        $validator = new MultiGradesValidator;

        if ($validator->canSaveSchoolClassGrades($this->schoolClassGrades)) {
            foreach ($this->schoolClassGrades as $schoolClassGrade) {
                $schoolGrade = LegacySchoolClassGrade::firstOrNew([
                    'turma_id' => $schoolClassGrade['turma_id'],
                    'escola_id' => $schoolClassGrade['escola_id'],
                    'serie_id' => $schoolClassGrade['serie_id'],
                ]);

                $schoolGrade->boletim_id = $schoolClassGrade['boletim_id'];

                if ($schoolClassGrade['boletim_diferenciado_id']) {
                    $schoolGrade->boletim_diferenciado_id = $schoolClassGrade['boletim_diferenciado_id'];
                }

                $schoolGrade->save();
            }

            return true;
        } else {
            return $validator->getMessage();
        }
    }

    private function deleteGradesOfSchoolClass()
    {
        $schoolClass = $this->schoolClassGrades[0]['turma_id'];
        $gradesToDelete = $this->getGradesToDelete($this->schoolClassGrades);
        LegacySchoolClassGrade::query()
            ->where('turma_id', $schoolClass)
            ->whereIn('serie_id', $gradesToDelete)
            ->delete();
    }
    

    private function getGradesToDelete($schoolClassGrades)
    {
        $schoolClass = $schoolClassGrades[0]['turma_id'];
        $newGrades = array_column($schoolClassGrades, 'serie_id');
        $oldGrades = LegacySchoolClassGrade::query()
            ->where('turma_id', $schoolClass)
            ->get()
            ->pluck('serie_id')
            ->toArray();
        
        return array_diff($oldGrades, $newGrades);
    }
}
