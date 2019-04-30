<?php

namespace iEducar\Modules\Educacenso\Model;

class TipoAtendimentoTurma
{
    const ESCOLARIZACAO = 0;
    const CLASSE_HOSPITALAR = 1;
    const ATIVIDADE_COMPLEMENTAR = 4;
    const AEE = 5;

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