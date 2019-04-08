<?php

namespace iEducar\Modules\Educacenso\Model;

class Equipamentos
{
    const COMPUTADORES = 1;
    const IMPRESSORAS = 2;
    const IMPRESSORAS_MULTIFUNCIONAIS = 3;
    const COPIADORA = 4;
    const SCANNER = 5;
    const ANTENA_PARABOLICA = 6;

    public static function getDescriptiveValues()
    {
        return [
            self::COMPUTADORES => 'Computadores',
            self::IMPRESSORAS => 'Impressoras',
            self::IMPRESSORAS_MULTIFUNCIONAIS => 'Impressoras multifuncionais',
            self::COPIADORA => 'Copiadora',
            self::SCANNER => 'Scanner',
            self::ANTENA_PARABOLICA => 'Antena parabólica',
        ];
    }
}