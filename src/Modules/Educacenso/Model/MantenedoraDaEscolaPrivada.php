<?php

namespace iEducar\Modules\Educacenso\Model;

class MantenedoraDaEscolaPrivada
{
    const GRUPOS_EMPRESARIAIS = 1;
    const SINDICATOS_TRABALHISTAS = 2;
    const ORGANIZACOES_NAO_GOVERNAMENTAIS = 3;
    const INSTITUICOES_SIM_FINS_LUCRATIVOS = 4;
    const SISTEMA_S = 5;
    const OSCIP = 6;

    /**
     * @return array
     */
    public static function getDescriptiveValues()
    {
        return [
            self::GRUPOS_EMPRESARIAIS => 'Empresa, grupos empresariais do setor privado ou pessoa física',
            self::SINDICATOS_TRABALHISTAS => 'Sindicatos de trabalhadores ou patronais, associações ou cooperativas',
            self::ORGANIZACOES_NAO_GOVERNAMENTAIS => 'Organização não governamental - ONG internacional ou nacional',
            self::INSTITUICOES_SIM_FINS_LUCRATIVOS => 'Instituições sem fins lucrativos',
            self::SISTEMA_S => 'Sistema S (Sesi, Senai, Sesc, outros)',
            self::OSCIP => 'Organização da Sociedade Civil de Interesse Público (Oscip)',
        ];
    }
}
