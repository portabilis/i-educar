<?php

namespace App\Models\Builders;

class LegacyTransferRequestBuilder extends LegacyBuilder
{
    public function unattended(): LegacyBuilder
    {
        return $this->whereNull('ref_cod_matricula_entrada');
    }
}
