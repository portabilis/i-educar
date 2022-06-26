<?php

namespace iEducar\Modules\Educacenso\Model;

class TipoItinerarioFormativo
{
    public const LINGUANGENS = 1;
    public const MATEMATICA = 2;
    public const CIENCIAS_NATUREZA = 3;
    public const CIENCIAS_HUMANAS = 4;
    public const FORMACAO_TECNICA = 5;
    public const ITINERARIO_INTEGRADO = 6;

    public static function getDescriptiveValues()
    {
        return [
            self::LINGUANGENS => 'Linguagens e suas tecnologias',
            self::MATEMATICA => 'Matemática e suas tecnologias',
            self::CIENCIAS_NATUREZA => 'Ciências da natureza e suas tecnologias',
            self::CIENCIAS_HUMANAS => 'Ciências humanas e sociais aplicadas',
            self::FORMACAO_TECNICA => 'Formação técnica e profissional',
            self::ITINERARIO_INTEGRADO => 'Itinerário formativo integrado',
        ];
    }

    public static function getDescriptiveValuesOfItineraryComposition()
    {
        return [
            self::LINGUANGENS => 'Linguagens e suas tecnologias',
            self::MATEMATICA => 'Matemática e suas tecnologias',
            self::CIENCIAS_NATUREZA => 'Ciências da natureza e suas tecnologias',
            self::CIENCIAS_HUMANAS => 'Ciências humanas e sociais aplicadas',
            self::FORMACAO_TECNICA => 'Formação técnica e profissional',
        ];
    }
}
