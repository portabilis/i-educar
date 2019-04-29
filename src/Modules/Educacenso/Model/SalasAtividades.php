<?php

namespace iEducar\Modules\Educacenso\Model;

class SalasAtividades
{
    const LEITURA = 1;
    const ATELIE = 2;
    const MUSICA = 3;
    const ESTUDIO_DANCA = 4;
    const MULTIUSO = 5;
    const RECURSOS_AEE = 6;
    const REPOUSO_ALUNO = 7;

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