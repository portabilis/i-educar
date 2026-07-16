<?php

namespace App\Models\Builders;

class LegacyAgendaBuilder extends LegacyBuilder
{
    /**
     * Filtra pela pessoa dona da agenda
     */
    public function whereOwner(int $person): self
    {
        return $this->where('ref_ref_cod_pessoa_own', $person);
    }

    /**
     * Filtra pela pessoa responsável pela agenda
     */
    public function whereResponsible(int $person): self
    {
        return $this->whereHas('responsibles', fn ($q) => $q->where('ref_ref_cod_pessoa_fj', $person));
    }
}
