<?php

namespace iEducar\Modules\Educacenso\Model;

class OrganizacaoEnsino
{
    public const SERIE_ANO = 1;
    public const PERIODOS_SEMESTRAIS = 2;
    public const CLICLOS_ENSINO_FUNDAMENTAL = 3;
    public const GRUPOS_NAO_SERIADOS = 4;
    public const MODULOS = 5;
    public const ALTERNANCIA_REGULAR = 6;

    public static function getDescriptiveValues()
    {
        return [
            self::SERIE_ANO => 'Série/Ano (séries anuais)',
            self::PERIODOS_SEMESTRAIS => 'Períodos semestrais',
            self::CLICLOS_ENSINO_FUNDAMENTAL => 'Ciclo(s) do Ensino Fundamental',
            self::GRUPOS_NAO_SERIADOS => 'Grupos não-seriados com base na idade ou competência (art. 23 LDB)',
            self::MODULOS => 'Módulos',
            self::ALTERNANCIA_REGULAR => 'Alternância regular de períodos de estudos (proposta pedagógica de formação por alternância com tempo-escola e tempo-comunidade)',
        ];
    }
}
