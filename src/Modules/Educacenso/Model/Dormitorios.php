<?php

namespace iEducar\Modules\Educacenso\Model;

class Dormitorios
{
    const ALUNO = 1;
    const PROFESSOR = 2;

    public static function getDescriptiveValues()
    {
        return [
            self::ALUNO => 'Dormitório de aluno(a)',
            self::PROFESSOR => 'Dormitório de professor(a)',
        ];
    }
}