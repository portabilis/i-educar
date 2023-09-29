<?php

namespace App\Models\Builders;

use Illuminate\Support\Collection;

class LegacySchoolBuilder extends LegacyBuilder
{
    /**
     * Retorna o recurso para os selects dos formulários
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
     */
    public function whereName(string $name): self
    {
        return $this->whereHas('organization', static fn ($q) => $q->whereRaw('unaccent(fantasia) ~* unaccent(?)', $name));
    }

    /**
     * Ordena por nome
     */
    public function orderByName(string $direction = 'asc'): self
    {
        return $this->joinOrganization()->orderBy('fantasia', $direction);
    }

    /**
     * Filtra por Instituição
     */
    public function whereInstitution(int $institution): self
    {
        return $this->where('ref_cod_instituicao', $institution);
    }

    /**
     * Filtra por Escola
     */
    public function whereSchool(int $school): self
    {
        return $this->whereKey($school);
    }

    /**
     * Filtra por zona de localização
     */
    public function whereLocalizationZone(int $localizationZone): self
    {
        return $this->where('zona_localizacao', $localizationZone);
    }

    /**
     * Filtra por localizacao diferenciada
     */
    public function whereDifferentiatedLocalizationArea(int $differentiatedLocalizationArea): self
    {
        return $this->where('localizacao_diferenciada', $differentiatedLocalizationArea);
    }

    /**
     * Realiza a junçao com organização
     */
    public function joinOrganization(): self
    {
        return $this->join('cadastro.juridica', 'idpes', 'ref_idpes');
    }

    /**
     * Filtra por Ativo
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
