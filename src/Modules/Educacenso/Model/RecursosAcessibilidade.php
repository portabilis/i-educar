<?php

namespace iEducar\Modules\Educacenso\Model;

class RecursosAcessibilidade
{
    const NENHUM = 1;
    const CORRIMAO = 2;
    const ELEVADOR = 3;
    const PISOS_TATEIS = 4;
    const PORTAS_VAO_LIVRE = 5;
    const RAMPAS = 6;
    const SINALIZACAO_SONORA = 7;
    const SINALIZACAO_TATIL = 8;
    const SINALIZACAO_VISUAL = 9;

    public static function getDescriptiveValues()
    {
        return [
            self::CORRIMAO => 'Corrimão e guarda-corpos',
            self::ELEVADOR => 'Elevador',
            self::PISOS_TATEIS => 'Pisos táteis',
            self::PORTAS_VAO_LIVRE => 'Portas com vão livre de no mínimo 80cm',
            self::RAMPAS => 'Rampas',
            self::SINALIZACAO_SONORA => 'Sinalização sonora',
            self::SINALIZACAO_TATIL => 'Sinalização tátil (piso/paredes)',
            self::SINALIZACAO_VISUAL => 'Sinalização visual (piso/paredes)',
            self::NENHUM => 'Nenhum dos recursos de acessibilidade listados',
        ];
    }
}