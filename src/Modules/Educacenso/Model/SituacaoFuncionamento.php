<?php

namespace iEducar\Modules\Educacenso\Model;

class SituacaoFuncionamento
{
    public const EM_ATIVIDADE = 1;
    public const PARALISADA = 2;
    public const EXTINTA = 3;

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
