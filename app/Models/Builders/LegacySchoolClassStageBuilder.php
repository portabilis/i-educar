<?php

namespace App\Models\Builders;

class LegacySchoolClassStageBuilder extends LegacyBuilder
{
    /**
     * Filtra por escola
     *
     * @param int $schoolClass
     *
     * @return LegacySchoolClassStageBuilder
     */
    public function whereSchoolClass(int $schoolClass): self
    {
        return $this->where('ref_cod_turma', $schoolClass);
    }

    /**
     * Ordena por Sequencial
     *
     * @return LegacySchoolClassStageBuilder
     */
    public function orderBySequencial(): self
    {
        return $this->orderBy('sequencial');
    }
}
