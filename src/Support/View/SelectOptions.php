<?php

namespace iEducar\Support\View;

use iEducar\Modules\Transport\Period;

class SelectOptions
{
    /**
     * Retorna a opção default para os selects.
     *
     * @return array
     */
    public static function getDefaultOption()
    {
        return ['' => 'Selecione'];
    }

    /**
     * Retorna as opções disponíveis de turno para o módulo de transporte.
     *
     * @return array
     */
    public static function transportPeriods()
    {
        return self::getDefaultOption() + Period::getDescriptiveValues();
    }
}
