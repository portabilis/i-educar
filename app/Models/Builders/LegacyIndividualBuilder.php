<?php

namespace App\Models\Builders;

class LegacyIndividualBuilder extends LegacyBuilder
{
    public function whereRace(int $race): self
    {
        return $this->whereHas('races', fn ($q) => $q->where('cod_raca', $race));
    }
}
