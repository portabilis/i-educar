<?php

namespace App\Models\Builders;

class LegacyTransferRequestBuilder extends LegacyBuilder
{
    public function active(): LegacyBuilder
    {
        return $this->where('ativo', 1);
    }

    public function unattended(): LegacyBuilder
    {
        return $this->whereNull('ref_cod_matricula_entrada');
    }
}
