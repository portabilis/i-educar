<?php

namespace App\Models\Builders;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class LegacySchoolBuilder extends LegacyBuilder
{

    /**
     * Retorna o recurso para os selects dos formulários
     *
     * @param array $filters
     * @return AnonymousResourceCollection
     */
    public function getResource(array $filters = []): AnonymousResourceCollection
    {
        $this->active()->orderByName()->filter($filters);
        //ref_idpes é usado na query, mas nao aparece no recurso.
        //name não é usado na query, mas é aparece no recurso com adicional
        $resource = $this->setExcept(['ref_idpes'])->resource(['id'], ['name']);

        return JsonResource::collection($resource);
    }

    /**
     * Filtra por Instituição
     *
     * @param int|null $institution
     * @return $this
     */
    public function filterInstitution(int $institution = null): self
    {
        return $this->when($institution, fn($q) => $q->whereInstitution($institution));
    }
}
