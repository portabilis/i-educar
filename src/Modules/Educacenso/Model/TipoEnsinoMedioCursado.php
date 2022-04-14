<?php

namespace iEducar\Modules\Educacenso\Model;

class TipoEnsinoMedioCursado
{
    public const FORMACAO_GERAL = 1;
    public const MODALIDADE_NORMAL = 2;
    public const CURSO_TECNICO = 3;
    public const MAGISTERIO_INDIGENA = 4;

    /**
     * @return array
     */
    public static function getDescriptiveValues()
    {
        return [
            self::FORMACAO_GERAL => 'Formação Geral',
            self::MODALIDADE_NORMAL => 'Modalidade Normal (Magistério)',
            self::CURSO_TECNICO => 'Curso Técnico',
            self::MAGISTERIO_INDIGENA => 'Magistério Indígena Modalidade Normal',
        ];
    }
}
