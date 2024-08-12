<?php

namespace App\Models\Builders;
class LegacyStageTypeBuilder extends LegacyBuilder
{
    /**
     * Filtra por registros ativos
     *
     * @return self
     */
    public function active(): self
    {
        return $this->where('ativo', 1);
    }
}
