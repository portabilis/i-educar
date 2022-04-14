<?php

namespace iEducar\Modules\Educacenso\Model;

class Deficiencias
{
    public const CEGUEIRA = 1;
    public const BAIXA_VISAO = 2;
    public const SURDEZ = 3;
    public const DEFICIENCIA_AUDITIVA = 4;
    public const SURDOCEGUEIRA = 5;
    public const DEFICIENCIA_FISICA = 6;
    public const DEFICIENCIA_INTELECTUAL = 7;
    public const TRANSTORNO_ESPECTRO_AUTISTA = 25;
    public const ALTAS_HABILIDADES_SUPERDOTACAO = 13;
    public const OUTRAS = 999;

    public static function getDescriptiveValues()
    {
        return [
            self::CEGUEIRA => 'Cegueira',
            self::BAIXA_VISAO => 'Baixa visão',
            self::SURDEZ => 'Surdez',
            self::DEFICIENCIA_AUDITIVA => 'Deficiência auditiva',
            self::SURDOCEGUEIRA => 'Surdocegueira',
            self::DEFICIENCIA_FISICA => 'Deficiência física',
            self::DEFICIENCIA_INTELECTUAL => 'Deficiência intelectual',
            self::TRANSTORNO_ESPECTRO_AUTISTA => 'Transtorno do espectro autista',
            self::ALTAS_HABILIDADES_SUPERDOTACAO => 'Altas habilidades/Superdotação',
            self::OUTRAS => 'Outras',
        ];
    }
}
