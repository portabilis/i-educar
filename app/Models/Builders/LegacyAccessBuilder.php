<?php

namespace App\Models\Builders;

use Carbon\Carbon;

class LegacyAccessBuilder extends LegacyBuilder
{
    /**
     * Filtra por instituição
     *
     * @param int $institution
     * @return $this
     */
    public function whereInstitution(int $institution): self
    {
        return $this->whereHas('user', static fn ($q) => $q->where('ref_cod_instituicao', $institution));
    }

    /**
     * Filtra por escola
     *
     * @param int $school
     * @return $this
     */
    public function whereSchool(int $school): self
    {
        return $this->whereHas('user.schools', static fn ($q) => $q->whereKey($school));
    }

    /**
     * Filtra por ativo
     *
     * @param bool $active
     * @return $this
     */
    public function whereActive(bool $active): self
    {
        return $this->whereHas('employee', static fn ($q) => $q->where('ativo', $active));
    }

    /**
     * Filtra por data inicial
     *
     * @param string $start
     * @return $this
     */
    public function whereStart(string $start): self
    {
        return $this->whereDate('data_hora','>=', $start);
    }

    /**
     * Filtra por data final
     *
     * @param string $end
     * @return $this
     */
    public function whereEnd(string $end): self
    {
        return $this->whereDate('data_hora','<=', $end);
    }

    /**
     * Filtra por pessoa
     *
     * @param int $person
     * @return $this
     */
    public function wherePerson(int $person): self
    {
        return $this->where('cod_pessoa', $person);
    }
}
