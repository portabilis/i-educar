<?php

namespace iEducar\Modules\Educacenso\Model;

use iEducar\Support\DescriptionValue;

class TipoAtendimentoTurma
{
    use DescriptionValue;

    public const CURRICULAR_ETAPA_ENSINO = 0;

    public const CLASSE_HOSPITALAR = 1;

    public const ATIVIDADE_COMPLEMENTAR = 4;

    public const AEE = 5;

    public const CURRICULAR_ETAPA_ENSINO_COM_ATIVIDADE_COMPLEMENTAR = 9;

    /**
     * @return array
     */
    public static function getDescriptiveValues()
    {
        return [
            self::CURRICULAR_ETAPA_ENSINO => 'Curricular (etapa de ensino)',
            self::ATIVIDADE_COMPLEMENTAR => 'Atividade complementar',
            self::AEE => 'Atendimento educacional especializado (AEE)',
            self::CURRICULAR_ETAPA_ENSINO_COM_ATIVIDADE_COMPLEMENTAR => 'Curricular (etapa de ensino) com Atividade Complementar',
        ];
    }

    public static function getDescription($types)
    {
        return collect($types)
            ->map(fn ($type) => self::getDescriptiveValues()[$type] ?? null)
            ->filter()
            ->implode(', ');
    }
}
