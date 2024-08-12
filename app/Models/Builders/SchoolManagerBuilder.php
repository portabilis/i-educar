<?php

namespace App\Models\Builders;
class SchoolManagerBuilder extends LegacyBuilder
{
    /**
     * Filtra pelo ID da escola
     *
     * @param int $schoolId
     * @return self
     */
    public function ofSchool(int $schoolId): self
    {
        return $this->where('school_id', $schoolId);
    }
}
