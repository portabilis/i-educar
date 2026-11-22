<?php

namespace App\Models\Builders;

class LegacyIndividualBuilder extends LegacyBuilder
{
    public function whereRace(int $race): self
    {
        return $this->whereHas('races', fn ($q) => $q->where('cod_raca', $race));
    }

    public function whereCpf(int $cpf): self
    {
        return $this->where('cpf', $cpf);
    }

    public function whereBirthdate(string $birthdate): self
    {
        return $this->where('data_nasc', $birthdate);
    }

    public function whereSimilarNameAndBirthdate(string $name, string $birthdate, ?int $exceptId = null): self
    {
        return $this->whereBirthdate($birthdate)
            ->whereHas('person', fn ($query) => $query->whereRaw('f_unaccent(nome) ILIKE f_unaccent(?)', [$name]))
            ->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId));
    }
}
