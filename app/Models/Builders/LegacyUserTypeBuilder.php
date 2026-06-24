<?php

namespace App\Models\Builders;

class LegacyUserTypeBuilder extends LegacyBuilder
{
    /**
     * Filtra por ativo
     */
    public function active(): self
    {
        return $this->where('ativo', 1);
    }

    /**
     * Filtra por Nome
     */
    public function whereName(string $name): self
    {
        return $this->whereRaw('f_unaccent(nm_tipo) ILIKE f_unaccent(?)', ["%{$name}%"]);
    }

    /**
     * Filtra por Descrição
     */
    public function whereDescription(string $description): self
    {
        return $this->whereRaw('f_unaccent(descricao) ILIKE f_unaccent(?)', ["%{$description}%"]);
    }

    /**
     * Filtra por Nível
     */
    public function whereLevel(int $level): self
    {
        return $this->where('nivel', $level);
    }

    /**
     * Filtra por nível de acesso igual ou inferior (número maior ou igual ao informado)
     */
    public function whereLevelAtLeast(int $level): self
    {
        return $this->where('nivel', '>=', $level);
    }
}
