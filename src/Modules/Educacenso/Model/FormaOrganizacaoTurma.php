<?php

namespace iEducar\Modules\Educacenso\Model;

class FormaOrganizacaoTurma
{
    public const SERIE_ANO = 1;
    public const SEMESTRAL = 2;
    public const CICLOS = 3;
    public const NAO_SERIADO = 4;
    public const MODULES = 5;
    public const ALTERNANCIA_REGULAR = 6;

    public static function getDescriptiveValues()
    {
        return [
            self::SERIE_ANO => 'Série/ano (séries anuais)',
            self::SEMESTRAL => 'Períodos semestrais',
            self::CICLOS => 'Ciclo(s)',
            self::NAO_SERIADO => 'Grupos não seriados com base na idade ou competência',
            self::MODULES => 'Módulos',
            self::ALTERNANCIA_REGULAR => 'Alternância regular de períodos de estudos'
        ];
    }
}
