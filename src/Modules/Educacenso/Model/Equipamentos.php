<?php

namespace iEducar\Modules\Educacenso\Model;

class Equipamentos
{
    public const COMPUTADORES = 1;
    public const IMPRESSORAS = 2;
    public const IMPRESSORAS_MULTIFUNCIONAIS = 3;
    public const COPIADORA = 4;
    public const SCANNER = 5;
    public const ANTENA_PARABOLICA = 6;

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
