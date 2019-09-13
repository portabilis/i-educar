<?php

namespace iEducar\Modules\Educacenso\Model;

class SalasFuncionais
{
    const COZINHA = 1;
    const REFEITORIO = 2;
    const DESPENSA = 3;
    const ALMOXARIFADO = 4;

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