<?php

namespace iEducar\Modules\Educacenso\Model;

class RecursosAcessibilidade
{
    public const NENHUM = 1;
    public const CORRIMAO = 2;
    public const ELEVADOR = 3;
    public const PISOS_TATEIS = 4;
    public const PORTAS_VAO_LIVRE = 5;
    public const RAMPAS = 6;
    public const SINALIZACAO_SONORA = 7;
    public const SINALIZACAO_TATIL = 8;
    public const SINALIZACAO_VISUAL = 9;

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
