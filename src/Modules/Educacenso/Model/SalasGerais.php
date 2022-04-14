<?php

namespace iEducar\Modules\Educacenso\Model;

class SalasGerais
{
    public const SALA_DIRETORIA = 1;
    public const SALA_SECRETARIA = 2;
    public const SALA_PROFESSORES = 3;
    public const BIBLIOTECA = 4;
    public const AUDITORIO = 5;

    public static function getDescriptiveValues()
    {
        return [
            self::SALA_DIRETORIA => 'Sala de diretoria',
            self::SALA_SECRETARIA => 'Sala de secretaria',
            self::SALA_PROFESSORES => 'Sala de professores',
            self::BIBLIOTECA => 'Biblioteca',
            self::AUDITORIO => 'Auditório',
        ];
    }
}
