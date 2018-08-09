<?php

namespace iEducar\Modules\AuditoriaGeral\Model;

class Operacoes
{
    const NOVO = 1;
    const EDICAO = 2;
    const EXCLUSAO = 3;

    public static function getDescriptiveValues()
    {
        return [
            self::NOVO => 'Novo',
            self::EDICAO => 'Edição',
            self::EXCLUSAO => 'Exclusão'
        ];
    }
}
