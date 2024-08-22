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
        /** @var LegacyBuilder $return */
        $return = $this->whereNull('ref_cod_matricula_entrada');

        return $return;
    }
}
