<?php

namespace iEducar\Modules\Educacenso\Model;

class EquipamentosAcessoInternet
{
    public const COMPUTADOR_MESA = 1;
    public const DISPOSITIVOS_PESSOAIS = 2;

    public static function getDescriptiveValues()
    {
        return [
            self::COMPUTADOR_MESA => 'Computadores de mesa, portáteis e tablets da escola (no laboratório de informática, biblioteca, sala de aula, etc.)',
            self::DISPOSITIVOS_PESSOAIS => 'Dispositivos pessoais (computadores portáteis, celulares, tablets, etc.)',
        ];
    }
}
