<?php

namespace iEducar\Modules\Educacenso\Model;

class EstruturaCurricular
{
    public const FORMACAO_GERAL_BASICA = 1;
    public const ITINERARIO_FORMATIVO = 2;
    public const NAO_SE_APLICA = 3;

    public static function getDescriptiveValues()
    {
        return [
            self::FORMACAO_GERAL_BASICA => 'Formação geral básica',
            self::ITINERARIO_FORMATIVO => 'Itinerário formativo',
            self::NAO_SE_APLICA => 'Não se aplica',
        ];
    }
}
