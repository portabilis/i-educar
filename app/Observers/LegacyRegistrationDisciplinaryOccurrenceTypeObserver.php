<?php

namespace App\Observers;

use App\Models\LegacyRegistrationDisciplinaryOccurrenceType;

class LegacyRegistrationDisciplinaryOccurrenceTypeObserver
{
    public function creating(LegacyRegistrationDisciplinaryOccurrenceType $legacyRegistrationDisciplinaryOccurrenceType)
    {
        $ultimo = LegacyRegistrationDisciplinaryOccurrenceType::query()
            ->where('ref_cod_matricula', $legacyRegistrationDisciplinaryOccurrenceType->ref_cod_matricula)
            ->where('ref_cod_tipo_ocorrencia_disciplinar', $legacyRegistrationDisciplinaryOccurrenceType->ref_cod_tipo_ocorrencia_disciplinar)
            ->latest('sequencial')
            ->value('sequencial');
        $legacyRegistrationDisciplinaryOccurrenceType->sequencial = ++$ultimo;
    }
}
