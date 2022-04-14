<?php

namespace iEducar\Modules\Educacenso\Model;

class SalasFuncionais
{
    public const COZINHA = 1;
    public const REFEITORIO = 2;
    public const DESPENSA = 3;
    public const ALMOXARIFADO = 4;

    public static function getDescriptiveValues()
    {
        return [
            self::COZINHA => 'Cozinha',
            self::REFEITORIO => 'Refeitório',
            self::DESPENSA => 'Despensa',
            self::ALMOXARIFADO => 'Almoxarifado',
        ];
    }
}
