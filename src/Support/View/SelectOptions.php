<?php

namespace iEducar\Support\View;

use iEducar\Modules\Transport\Period;

class SelectOptions
{
    /**
     * Retorna as opções disponíveis de turno para o módulo de transporte.
     *
     * @return array
     */
    public static function transportPeriods()
    {
        return [0 => 'Selecione'] + Period::getDescriptiveValues();
    }
}
