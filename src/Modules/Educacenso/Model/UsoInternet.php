<?php

namespace iEducar\Modules\Educacenso\Model;

class UsoInternet
{
    public const NAO_POSSUI = 1;
    public const ADMINISTRATIVO = 2;
    public const PROCESSOS_ENSINO = 3;
    public const ALUNOS = 4;
    public const COMUNIDADE = 5;

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
