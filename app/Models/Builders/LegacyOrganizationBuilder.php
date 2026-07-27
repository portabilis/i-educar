<?php

namespace App\Models\Builders;

class LegacyOrganizationBuilder extends LegacyBuilder
{
    /**
     * Filtra por CNPJ (numérico ou alfanumérico), com busca parcial ignorando máscara e caixa.
     */
    public function whereCnpj(string $cnpj): self
    {
        return $this->whereRaw('cnpj LIKE ?', ['%' . limpaCnpj($cnpj) . '%']);
    }
}
