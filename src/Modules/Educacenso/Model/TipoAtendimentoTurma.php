<?php

namespace iEducar\Modules\Educacenso\Model;

use iEducar\Support\DescriptionValue;

class TipoAtendimentoTurma
{
    use DescriptionValue;

    public const ESCOLARIZACAO = 0;
    public const CLASSE_HOSPITALAR = 1;
    public const ATIVIDADE_COMPLEMENTAR = 4;
    public const AEE = 5;

    /**
     * @return array
     */
    public static function getDescriptiveValues()
    {
        return [
            self::ESCOLARIZACAO => 'Escolarização',
            self::ATIVIDADE_COMPLEMENTAR => 'Atividade complementar',
            self::AEE => 'Atendimento educacional especializado (AEE)',
        ];
    }
}
