<?php

namespace App\Rules;

use App\Models\LegacyDiscipline;
use App\Support\ExternalServices\iDiario;
use Illuminate\Contracts\Validation\Rule;

class CanChangeExitDate implements Rule
{
    use iDiario;

    protected $msg;

    private const ACTIVITY_TYPE_DESCRIPTION = [
        'daily_note' => 'Diário de avaliações',
        'conceptual_exam' => 'Diário de avaliações conceituais',
        'avaliation_exemption' => 'Dispensa de avaliações',
        'transfer_note' => 'Notas de transferência',
        'complementary_exam' => 'Recuperação de avaliações',
        'recovery_diary_record_student' => 'Recuperação de etapas',
        'school_term_recovery_diary_record' => 'Diário de avaliações complementares',
        'observation_diary_record' => 'Diário de observações',
    ];

    private const IGNORED_ACTIVITY_TYPES = ['descriptive_exam', 'general_descriptive_exam'];

    public function passes($attribute, $value)
    {
        $iDiarioService = $this->getIdiarioService();
        if ($iDiarioService) {
            $studentActivity = $iDiarioService->getStudentActivity($value['student_id'], $value['exit_date']);
            $studentActivity = $this->removeIgnoredTypes($studentActivity);
            $hasActivity = count($studentActivity['student_activity']) > 0;
            if ($hasActivity) {
                $this->msg = $this->buildHtmlMessage($studentActivity);

                return false;
            }
        }

        return true;
    }

    private function buildHtmlMessage(array $studentActivity)
    {
        $html = '';
        foreach ($studentActivity['student_activity'] as $activityType) {
            $html .= '<b>' . self::ACTIVITY_TYPE_DESCRIPTION[$activityType->type] . '</b>';
            if (isset($activityType->disciplines)) {
                $html .= ': (';
                $disciplinesName = [];
                $contDisciplines = count($activityType->disciplines);
                for ($i = 0; $i < $contDisciplines; $i++) {
                    $disciplinesName[] = $this->getDisciplineNameById($activityType->disciplines[$i]);
                    if ($i === 4) {
                        break;
                    }
                }
                $html .= implode(', ', $disciplinesName);
                $html .= $contDisciplines > 5 ? ' e outras) <br><br>' : ')<br><br>';
            } else {
                $html .= ': Sim <br><br>';
            }
        }

        return $html;
    }

    private function getDisciplineNameById($disciplinaId)
    {
        return LegacyDiscipline::find($disciplinaId)->nome;
    }

    private function removeIgnoredTypes(array $studentActivity)
    {
        foreach ($studentActivity['student_activity'] as $key => $item) {
            if (in_array($item->type, ['descriptive_exam', 'general_descriptive_exam'])) {
                unset($studentActivity['student_activity'][$key]);
            }
        }

        return $studentActivity;
    }

    public function message()
    {
        return 'Existem lançamentos no i-Diário: <br><br>'.$this->msg;
    }
}
