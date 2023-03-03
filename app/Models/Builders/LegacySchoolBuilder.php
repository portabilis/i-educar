<?php

namespace App\Models\Builders;

use Illuminate\Support\Collection;

class LegacySchoolBuilder extends LegacyBuilder
{
    /**
     * Retorna o recurso para os selects dos formulários
     *
     * @param array $filters
     *
     * @return Collection
     */
    public function getResource(array $filters = []): Collection
    {
        $this->active()->orderByName()->filter($filters);
        //ref_idpes é usado na query, mas nao aparece no recurso.
        //name não é usado na query, mas é aparece no recurso com adicional
        return $this->setExcept(['ref_idpes'])->resource(['id'], ['name']);
    }

    /**
     * Filtra por nome
     *
     * @param string $name
     *
     * @return LegacySchoolBuilder
     */
    public function whereName(string $name): self
    {
        return $this->whereHas('organization', static fn ($q) => $q->whereRaw('unaccent(fantasia) ~* unaccent(?)', $name));
    }

    /**
     * Ordena por nome
     *
     * @param string $direction
     *
     * @return LegacySchoolBuilder
     */
    public function orderByName(string $direction = 'asc'): self
    {
        return $this->joinOrganization()->orderBy('fantasia', $direction);
    }

    /**
     * Filtra por Instituição
     *
     * @param int $institution
     *
     * @return LegacySchoolBuilder
     */
    public function whereInstitution(int $institution): self
    {
        return $this->where('ref_cod_instituicao', $institution);
    }

    /**
     * Realiza a junçao com organização
     *
     * @return LegacySchoolBuilder
     */
    public function joinOrganization(): self
    {
        return $this->join('cadastro.juridica', 'idpes', 'ref_idpes');
    }

    /**
     * Filtra por Ativo
     *
     * @return LegacySchoolBuilder
     */
    public function active(): self
    {
        return $this->where('escola.ativo', 1);
    }

    public function whereActive(int $active): self
    {
        return $this->where('escola.ativo', $active);
    }
}
