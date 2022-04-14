<?php

namespace App\Rules;

use App\Models\LegacyActiveLooking;
use Illuminate\Contracts\Validation\Rule;

class CanStoreActiveLooking implements Rule
{
    private $msg;

    public function passes($attribute, $value)
    {
        $legacyRegistration = $value['registration'];
        $legacyActiveLooking = $value['active_looking'];
        $registrationDate = $legacyRegistration->data_matricula;

        if ($legacyActiveLooking->data_inicio->lt($registrationDate)) {
            $this->msg = 'Data de início não pode ser menor que a data matrícula.';

            return false;
        }

        if ($legacyActiveLooking->data_fim) {
            if ($legacyActiveLooking->data_fim->lt($legacyActiveLooking->data_inicio)) {
                $this->msg = 'Data de retorno não pode ser menor que a data de início';

                return false;
            }
            if ($legacyActiveLooking->data_fim->lt($registrationDate)) {
                $this->msg = 'Data de retorno não pode ser menor que a data da matrícula';

                return false;
            }
        }

        $activeLookingInSameDate = LegacyActiveLooking::query()
            ->whereRaw("
                (
                    CASE WHEN data_fim is null THEN data_inicio <= '{$legacyActiveLooking->getStartDate()}'::date
	                ELSE '{$legacyActiveLooking->getStartDate()}'::date between data_inicio and data_fim END
                )
            ")
            ->where('ref_cod_matricula', $legacyRegistration->cod_matricula);

        if ($legacyActiveLooking->id) {
            $activeLookingInSameDate->where('id', '<>', $legacyActiveLooking->id);
        }

        if ($activeLookingInSameDate->exists()) {
            $this->msg = 'Já existe uma busca ativa lançada para o período selecionado.';

            return false;
        }

        return true;
    }

    public function message()
    {
        return $this->msg;
    }
}
