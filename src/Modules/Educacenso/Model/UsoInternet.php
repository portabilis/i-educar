<?php

namespace iEducar\Modules\Educacenso\Model;

class UsoInternet
{
    const NAO_POSSUI = 1;
    const ADMINISTRATIVO = 2;
    const PROCESSOS_ENSINO = 3;
    const ALUNOS = 4;
    const COMUNIDADE = 5;

    public static function getDescriptiveValues()
    {
        return [
            self::NAO_POSSUI => 'Não possui acesso à internet',
            self::ADMINISTRATIVO => 'Para uso administrativo',
            self::PROCESSOS_ENSINO => 'Para uso nos processos de ensino e aprendizagem',
            self::ALUNOS => 'Para uso dos alunos',
            self::COMUNIDADE => 'Para uso da comunidade',
        ];
    }
}