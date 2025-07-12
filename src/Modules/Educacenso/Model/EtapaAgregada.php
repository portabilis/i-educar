<?php

namespace iEducar\Modules\Educacenso\Model;

class EtapaAgregada
{
    public const EDUCACAO_INFANTIL = 301;

    public const ENSINO_FUNDAMENTAL = 302;

    public const MULTI_CORRECAO_FLUXO = 303;

    public const ENSINO_MEDIO = 304;

    public const ENSINO_MEDIO_NORMAL_MAGISTERIO = 305;

    public const EDUCACAO_JOVENS_ADULTOS = 306;

    public const CURSO_TECNICO_FIC = 308;

    public static function getDescriptiveValues()
    {
        return [
            self::EDUCACAO_INFANTIL => 'Educação Infantil',
            self::ENSINO_FUNDAMENTAL => 'Ensino Fundamental',
            self::MULTI_CORRECAO_FLUXO => 'Multi e correção de fluxo',
            self::ENSINO_MEDIO => 'Ensino Médio',
            self::ENSINO_MEDIO_NORMAL_MAGISTERIO => 'Ensino Médio - Normal/ Magistério',
            self::EDUCACAO_JOVENS_ADULTOS => 'Educação de Jovens e Adultos',
            self::CURSO_TECNICO_FIC => 'Curso Técnico e FIC - Concomitante ou Subsequente',
        ];
    }
}
