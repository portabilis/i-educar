<?php

namespace iEducar\Modules\Educacenso\Model;

class OrganizacaoCurricular
{
    public const FORMACAO_GERAL_BASICA = 1;

    public const ITINERARIO_FORMATIVO_APROFUNDAMENTO = 4;

    public const ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL = 5;

    public static function getDescriptiveValues()
    {
        return [
            self::FORMACAO_GERAL_BASICA => 'Formação geral básica',
            self::ITINERARIO_FORMATIVO_APROFUNDAMENTO => 'Itinerário formativo de aprofundamento',
            self::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL => 'Itinerário de formação técnica e profissional',
        ];
    }
}
