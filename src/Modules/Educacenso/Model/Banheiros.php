<?php

namespace iEducar\Modules\Educacenso\Model;

class Banheiros
{
    public const BANHEIRO = 1;
    public const BANHEIRO_FUNCIONARIOS = 2;
    public const BANHEIRO_CHUVEIRO = 3;
    public const BANHEIRO_EDUCACAO_INFANTIL = 4;
    public const BANHEIRO_ACESSIVEL = 5;

    public static function getDescriptiveValues()
    {
        return [
            self::BANHEIRO => 'Banheiro',
            self::BANHEIRO_FUNCIONARIOS => 'Banheiro exclusivo para os funcionários',
            self::BANHEIRO_CHUVEIRO => 'Banheiro ou vestuário com chuveiro',
            self::BANHEIRO_EDUCACAO_INFANTIL => 'Banheiro adequado à educação infantil',
            self::BANHEIRO_ACESSIVEL => 'Banheiro acessível adequado ao uso de pessoas com deficiência ou mobilidade reduzida',
        ];
    }
}
