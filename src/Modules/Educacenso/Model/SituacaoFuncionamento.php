<?php

namespace iEducar\Modules\Educacenso\Model;

class SituacaoFuncionamento
{
    const EM_ATIVIDADE = 1;
    const PARALISADA = 2;
    const EXTINTA = 3;

    /**
     * @return array
     */
    public static function getDescriptiveValues()
    {
        return [
            self::EM_ATIVIDADE => 'Em atividade',
            self::PARALISADA => 'Paralisada',
            self::EXTINTA => 'Extinta'
        ];
    }
}
