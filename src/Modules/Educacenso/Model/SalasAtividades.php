<?php

namespace iEducar\Modules\Educacenso\Model;

class SalasAtividades
{
    public const LEITURA = 1;
    public const ATELIE = 2;
    public const MUSICA = 3;
    public const ESTUDIO_DANCA = 4;
    public const MULTIUSO = 5;
    public const RECURSOS_AEE = 6;
    public const REPOUSO_ALUNO = 7;

    public static function getDescriptiveValues()
    {
        return [
            self::LEITURA => 'Sala de leitura',
            self::ATELIE => 'Sala/ateliê de artes',
            self::MUSICA => 'Sala de música/coral',
            self::ESTUDIO_DANCA => 'Sala/estúdio de dança',
            self::MULTIUSO => 'Sala multiúso (música, dança e artes)',
            self::RECURSOS_AEE => 'Sala de recursos multifuncionais para Atendimento Educacional Especializado (AEE)',
            self::REPOUSO_ALUNO => 'Sala de repouso para aluno(a)',
        ];
    }
}
