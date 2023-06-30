<?php

namespace App\Models\Builders;

use App\Models\Individual;
use App\Models\Student;

class LogUnificationBuilder extends LegacyBuilder
{
    /**
     * Filtra por nome do curso
     *
     *
     * @return $this
     */
    public function name(string $name): self
    {
        return $this->whereHas('studentMain.person', function ($query) use ($name) {
            $query->whereRaw('unaccent(name) ~* unaccent(?)', $name);
        });
    }

    /**
     * Filtra por aluno
     *
     * @return $this
     */
    public function student(): self
    {
        return $this->where('type', Student::class);
    }

    /**
     * Filtra por pessoa
     *
     * @return $this
     */
    public function person(): self
    {
        return $this->where('type', Individual::class);
    }
}
