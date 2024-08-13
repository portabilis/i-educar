<?php

namespace App\Models\Builders;

class SchoolManagerBuilder extends LegacyBuilder
{
    /**
     * Filtra pelo ID da escola
     */
    public function ofSchool(int $schoolId): self
    {
        return $this->where('school_id', $schoolId);
    }
}
