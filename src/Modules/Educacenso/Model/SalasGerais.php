<?php

namespace iEducar\Modules\Educacenso\Model;

class SalasGerais
{
    const SALA_DIRETORIA = 1;
    const SALA_SECRETARIA = 2;
    const SALA_PROFESSORES = 3;
    const BIBLIOTECA = 4;
    const AUDITORIO = 5;

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