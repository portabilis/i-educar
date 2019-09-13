<?php

namespace iEducar\Modules\Educacenso\Model;

class Deficiencias
{
    const CEGUEIRA = 1;
    const BAIXA_VISAO = 2;
    const SURDEZ = 3;
    const DEFICIENCIA_AUDITIVA = 4;
    const SURDOCEGUEIRA = 5;
    const DEFICIENCIA_FISICA = 6;
    const DEFICIENCIA_INTELECTUAL = 7;
    const TRANSTORNO_ESPECTRO_AUTISTA = 25;
    const ALTAS_HABILIDADES_SUPERDOTACAO = 13;

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
        ];
    }
}
