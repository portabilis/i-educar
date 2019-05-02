<?php

namespace iEducar\Modules\Educacenso\Model;

use App\Models\ManagerAccessCriteria;

class SchoolManagerAccessCriteria
{
    const PROPRIETARIO = 1;
    const CONCURSO = 4;
    const PROCESSO_ELEITORAL_COMUNIDADE = 5;
    const PROCESSO_SELETIVO_COMUNIDADE = 6;
    const OUTRO = 7;

    public static function getDescriptiveValues()
    {
        return ManagerAccessCriteria::all()->getKeyValueArray('name');
    }
}
