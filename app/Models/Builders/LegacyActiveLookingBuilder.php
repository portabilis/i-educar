<?php

namespace App\Models\Builders;

class LegacyActiveLookingBuilder extends LegacyBuilder
{
    /**
     * Filtra por Situação
     */
    public function whereSituation(int $situation): self
    {
        return $this->where('resultado_busca_ativa', $situation);
    }
}
