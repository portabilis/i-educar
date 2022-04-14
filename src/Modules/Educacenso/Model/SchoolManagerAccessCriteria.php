<?php

namespace iEducar\Modules\Educacenso\Model;

use App\Models\ManagerAccessCriteria;

class SchoolManagerAccessCriteria
{
    public const PROPRIETARIO = 1;
    public const CONCURSO = 4;
    public const PROCESSO_ELEITORAL_COMUNIDADE = 5;
    public const PROCESSO_SELETIVO_COMUNIDADE = 6;
    public const OUTRO = 7;

    public static function getDescriptiveValues()
    {
        return ManagerAccessCriteria::all()->getKeyValueArray('name');
    }
}
