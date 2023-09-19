<?php

namespace App\Models\Builders;

class LegacyAccessBuilder extends LegacyBuilder
{
    /**
     * Filtra por instituição
     *
     * @return $this
     */
    public function whereInstitution(int $institution): self
    {
        return $this->whereHas('user', static fn ($q) => $q->where('ref_cod_instituicao', $institution));
    }

    /**
     * Filtra por escola
     *
     * @return $this
     */
    public function whereSchool(int $school): self
    {
        return $this->whereHas('user.schools', static fn ($q) => $q->whereKey($school));
    }

    /**
     * Filtra por ativo
     *
     * @return $this
     */
    public function whereActive(bool $active): self
    {
        return $this->whereHas('employee', static fn ($q) => $q->where('ativo', $active));
    }

    /**
     * Filtra por data inicial
     *
     * @return $this
     */
    public function whereStartAfter(string $start): self
    {
        return $this->whereDate('data_hora', '>=', $start);
    }

    /**
     * Filtra por data final
     *
     * @return $this
     */
    public function whereEndBefore(string $end): self
    {
        return $this->whereDate('data_hora', '<=', $end);
    }

    /**
     * Filtra por pessoa
     *
     * @return $this
     */
    public function wherePerson(int $person): self
    {
        return $this->where('cod_pessoa', $person);
    }
}
