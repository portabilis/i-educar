<?php

namespace iEducar\Modules\AuditoriaGeral\Model;

class Operacoes
{
    public const NOVO = 1;
    public const EDICAO = 2;
    public const EXCLUSAO = 3;

    public static function getDescriptiveValues()
    {
        return [
            self::NOVO => 'Novo',
            self::EDICAO => 'Edição',
            self::EXCLUSAO => 'Exclusão'
        ];
    }
}
