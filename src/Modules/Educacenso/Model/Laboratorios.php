<?php

namespace iEducar\Modules\Educacenso\Model;

class Laboratorios
{
    public const INFORMATICA = 1;
    public const CIENCIAS = 2;
    public const EDUCACAO_PROFISSIONAL = 3;

    public static function getDescriptiveValues()
    {
        return [
            self::INFORMATICA => 'Laboratório de informática',
            self::CIENCIAS => 'Laboratório de ciências',
            self::EDUCACAO_PROFISSIONAL => 'Laboratório específico para a educação profissional',
        ];
    }
}
