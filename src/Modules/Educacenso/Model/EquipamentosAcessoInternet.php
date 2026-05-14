<?php

namespace iEducar\Modules\Educacenso\Model;

class EquipamentosAcessoInternet
{
    public const COMPUTADOR_MESA = 1;

    public const DISPOSITIVOS_PESSOAIS = 2;

    public const AMBOS = 3;

    public static function getDescriptiveValues()
    {
        return [
            self::COMPUTADOR_MESA => 'Computadores de mesa, portáteis e tablets da escola(laboratório de informática, biblioteca, salas de aula, etc)',
            self::DISPOSITIVOS_PESSOAIS => 'Dispositivos pessoais (computadores portáteis, celulares, tablets, etc.)',
            self::AMBOS => 'Computadores de mesa, portáteis e tablets da escola e Dispositivos pessoais',
        ];
    }
}
