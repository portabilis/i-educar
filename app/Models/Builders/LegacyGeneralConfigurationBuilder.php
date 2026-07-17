<?php

namespace App\Models\Builders;

class LegacyGeneralConfigurationBuilder extends LegacyBuilder
{
    /**
     * Filtra a configuração da última instituição ativa
     */
    public function forActiveInstitution(): self
    {
        return $this->whereHas('institution', fn ($q) => $q->where('ativo', 1))
            ->orderByDesc('ref_cod_instituicao');
    }
}
