<?php

namespace iEducar\Modules\Educacenso\Model;

class AreasExternas
{
    const QUADRA_COBERTA = 1;
    const QUADRA_DESCOBERTA = 2;
    const PATIO_COBERTO = 3;
    const PATIO_DESCOBERTO = 4;
    const PARQUE_INFANTIL = 5;
    const PISCINA = 6;
    const AREA_VERDE = 7;
    const TERREIRAO = 8;
    const VIVEIRO = 9;

    public static function getDescriptiveValues()
    {
        return [
            self::QUADRA_COBERTA => 'Quadra de esportes coberta',
            self::QUADRA_DESCOBERTA => 'Quadra de esportes descoberta',
            self::PATIO_COBERTO => 'Pátio coberto',
            self::PATIO_DESCOBERTO => 'Pátio descoberto',
            self::PARQUE_INFANTIL => 'Parque infantil',
            self::PISCINA => 'Piscina',
            self::AREA_VERDE => 'Área verde',
            self::TERREIRAO => 'Terreirão (área para prática desportiva e recreação sem cobertura, sem piso e sem edificações)',
            self::VIVEIRO => 'Viveiro/criação de animais',
        ];
    }
}