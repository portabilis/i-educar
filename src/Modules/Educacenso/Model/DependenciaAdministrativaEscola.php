<?php

namespace iEducar\Modules\Educacenso\Model;

class DependenciaAdministrativaEscola
{
    const FEDERAL = 1;
    const ESTADUAL = 2;
    const MUNICIPAL = 3;
    const PRIVADA = 4;

    /**
     * @return array
     */
    public static function getDescriptiveValues()
    {
        return [
            self::FEDERAL => 'Federal',
            self::ESTADUAL => 'Estadual',
            self::MUNICIPAL => 'Municipal',
            self::PRIVADA => 'Privada'
        ];
    }
}
