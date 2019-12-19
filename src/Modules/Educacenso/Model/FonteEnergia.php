<?php

namespace iEducar\Modules\Educacenso\Model;

class FonteEnergia
{
    const REDE_PUBLICA = 1;
    const GERADOR_COMBUSTIVEL_FOSSIL = 2;
    const FONTES_RENOVAVEIS = 3;
    const INEXISTENTE = 4;

    public static function getDescriptiveValues()
    {
        return [
            self::REDE_PUBLICA => 'Rede pública',
            self::GERADOR_COMBUSTIVEL_FOSSIL => 'Gerador movido a combustível fóssil',
            self::FONTES_RENOVAVEIS => 'Fontes de energia renováveis ou alternativas (gerador a biocombustível e/ou biodigestores, eólica, solar, outras)',
            self::INEXISTENTE => 'Não há energia elétrica',
        ];
    }
}
