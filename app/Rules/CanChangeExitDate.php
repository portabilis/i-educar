<?php

namespace App\Rules;

use App\Support\ExternalServices\iDiario;
use Illuminate\Contracts\Validation\Rule;

class CanChangeExitDate implements Rule
{
    use iDiario;
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $iDiarioService = $this->getIdiarioService();
        $studentActivity = $iDiarioService->getStudentActivity($value['student_id'], $value['exit_date']);
        $hasActivity = count($studentActivity['student_activity']) > 0;

        if ($hasActivity) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Existe lançamento de faltas e notas no i-Diário para a data de saída informada.';
    }
}
