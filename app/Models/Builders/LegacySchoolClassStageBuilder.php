<?php

namespace App\Models\Builders;

class LegacySchoolClassStageBuilder extends LegacyBuilder
{
    /**
     * Filtra por escola
     */
    public function whereSchoolClass(int $schoolClass): self
    {
        return $this->where('ref_cod_turma', $schoolClass);
    }

    /**
     * Filtra por Etapa
     */
    public function whereStageType(int $stageType): self
    {
        return $this->where('ref_cod_modulo', $stageType);
    }

    /**
     * Ordena por Sequencial
     */
    public function orderBySequencial(): self
    {
        return $this->orderBy('sequencial');
    }
}
