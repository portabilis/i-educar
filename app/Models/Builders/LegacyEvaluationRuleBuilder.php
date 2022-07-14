<?php

namespace App\Models\Builders;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class LegacyEvaluationRuleBuilder extends LegacyBuilder
{

    /**
     * Retorna o recurso para os selects dos formulários
     *
     * @param array $filters
     * @return AnonymousResourceCollection
     */
    public function getResource(array $filters = []): AnonymousResourceCollection
    {
        $this->orderByName()->filter($filters);
        $resource = $this->resource(['id', 'name']);

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
