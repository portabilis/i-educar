<?php

namespace iEducar\Modules\Educacenso\Model;

class CategoriaEscolaPrivada
{
    public const PARTICULAR = 1;
    public const COMUNITARIA = 2;
    public const CONFESSIONAL = 3;
    public const FILANTROPICA = 4;

    public static function getDescriptiveValues()
    {
        return [
            self::PARTICULAR => 'Particular',
            self::COMUNITARIA => 'Comunitária',
            self::CONFESSIONAL => 'Confessional',
            self::FILANTROPICA => 'Filantrópica',
        ];
    }
}
