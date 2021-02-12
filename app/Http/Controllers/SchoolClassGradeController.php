<?php

namespace App\Http\Controllers;

use App\Models\LegacySchoolClassGrade;
use App\Models\LegacySchoolClass;
use Illuminate\Http\Request;

class SchoolClassGradeController extends Controller
{
    public function storeSchoolClassGrade(Request $request, LegacySchoolClass $schoolClass)
    {
        $schoolClassGrades = [];
        $school = $request->get('ref_cod_escola') ?? $request->get('ref_cod_escola_');
    
        foreach ($request->get('mult_serie_id') as $key => $serieId) {
            $schoolClassGrades[] = [
                'escola_id' => $school,
                'serie_id' => $serieId,
                'turma_id' => $schoolClass->getKey(),
                'boletim_id' => $request->get('mult_boletim_id')[$key],
                'boletim_diferenciado_id' => $request->get('mult_boletim_diferenciado_id')[$key],
            ];
        }

        $this->deleteGradesOfSchoolClass($schoolClassGrades);
        $this->saveSchoolClassGrade($schoolClassGrades);
    }

    public function deleteAllGradesOfSchoolClass(Request $request, LegacySchoolClass $schoolClass) {
        LegacySchoolClassGrade::query()
            ->where('turma_id', $schoolClass->getId())
            ->delete();
    }

    private function saveSchoolClassGrade($schoolClassGrades)
    {
        foreach ($schoolClassGrades as $schoolClassGrade) {
            $schoolGrade = LegacySchoolClassGrade::firstOrNew([
                'turma_id' => $schoolClassGrade['turma_id'],
                'escola_id' => $schoolClassGrade['escola_id'],
                'serie_id' => $schoolClassGrade['serie_id'],
            ]);

            $schoolGrade->boletim_id = $schoolClassGrade['boletim_id'];
            $schoolGrade->boletim_diferenciado_id = $schoolClassGrade['boletim_diferenciado_id'];
            $schoolGrade->save();
        }
    }

    private function deleteGradesOfSchoolClass($schoolClassGrades)
    {
        $schoolClass = $schoolClassGrades[0]['turma_id'];
        $gradesToDelete = $this->getGradesToDelete($schoolClassGrades);
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
