<?php

namespace iEducar\Modules\Educacenso\Model;

class VeiculoTransporteEscolar
{
    const VAN_KOMBI = 1;
    const MICROONIBUS = 2;
    const ONIBUS = 3;
    const BICICLETA = 4;
    const TRACAO_ANIMAL = 5;
    const OUTRO = 6;
    const CAPACIDADE_5 = 7;
    const CAPACIDADE_5_15 = 8;
    const CAPACIDADE_15_35 = 9;
    const CAPACIDADE_35 = 10;
    const TREM_METRO = 11;

    /**
     * @return array
     */
    public static function getDescriptiveValues()
    {
        return [
            self::VAN_KOMBI => 'Rodoviário - Vans/Kombis',
            self::MICROONIBUS => 'Rodoviário - Microônibus',
            self::ONIBUS => 'Rodoviário - Ônibus',
            self::BICICLETA => 'Rodoviário - Bicicleta',
            self::TRACAO_ANIMAL => 'Rodoviário - Tração animal',
            self::OUTRO => 'Rodoviário - Outro',
            self::CAPACIDADE_5 => 'Aquaviário - Capacidade de até 5 aluno(a)s',
            self::CAPACIDADE_5_15 => 'Aquaviário - Capacidade entre 5 a 15 aluno(a)s',
            self::CAPACIDADE_15_35 => 'Aquaviário - Capacidade entre 15 a 35 aluno(a)s',
            self::CAPACIDADE_35 => 'Aquaviário - Capacidade acima de 35 aluno(a)s',
        ];
    }
}