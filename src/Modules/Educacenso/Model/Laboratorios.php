<?php

namespace iEducar\Modules\Educacenso\Model;

class Laboratorios
{
    const INFORMATICA = 1;
    const CIENCIAS = 2;

    public static function getDescriptiveValues()
    {
        return [
            self::INFORMATICA => 'Laboratório de informática',
            self::CIENCIAS => 'Laboratório de ciências',
        ];
    }
}