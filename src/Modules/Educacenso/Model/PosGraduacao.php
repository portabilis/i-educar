<?php

namespace iEducar\Modules\Educacenso\Model;

class PosGraduacao
{
    const ESPECIALIZACAO = 1;
    const MESTRADO = 2;
    const DOUTORADO = 3;
    const NAO_POSSUI = 4;

    public static function getDescriptiveValues()
    {
        return [
            self::ESPECIALIZACAO => 'Especialização',
            self::MESTRADO => 'Mestrado',
            self::DOUTORADO => 'Doutorado',
            self::NAO_POSSUI => 'Não tem pós-graduação concluída',
        ];
    }
}
