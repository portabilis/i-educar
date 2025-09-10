<?php

namespace iEducar\Modules\Educacenso\Model;

/**
 * Class Transtornos
 * Os valores das constantes foram iniciados em 50,
 * pois não podem coincidir com os valores presentes na classe de Deficiências.
 */
class Transtornos
{
    public const DISCALCULIA = 50;

    public const DISGRAFIA = 51;

    public const DISLALIA = 52;

    public const DISLEXIA = 53;

    public const TDAH = 54;

    public const TPAC = 55;

    public const OUTROS = 999;

    public static function getDescriptiveValues()
    {
        return [
            self::DISCALCULIA => 'Discalculia ou outro transtorno da matemática e raciocínio lógico',
            self::DISGRAFIA => 'Disgrafia, Disortografia ou outro transtorno da escrita e ortografia',
            self::DISLALIA => 'Dislalia ou outro transtorno da linguagem e comunicação',
            self::DISLEXIA => 'Dislexia',
            self::TDAH => 'Transtorno do Déficit de Atenção com Hiperatividade (TDAH)',
            self::TPAC => 'Transtorno do Processamento Auditivo Central (TPAC)',
            self::OUTROS => 'Outros',
        ];
    }
}
