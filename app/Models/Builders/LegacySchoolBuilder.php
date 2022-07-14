<?php

namespace App\Models\Builders;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class LegacySchoolBuilder extends LegacyBuilder
{

    /**
     * Retorna o recurso para os selects dos formulários
     *
     * @param array $filters
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
     * Filtra por Instituição
     *
     * @param int $institution
     * @return $this
     */
    public function filterInstitution(int $institution): self
    {
        return $this->whereInstitution($institution);
    }
}
