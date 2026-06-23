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

    /**
     * Indica se o tipo de atendimento inclui Curricular (etapa de ensino), considerando
     * tanto o código próprio quanto o combinado (Curricular com Atividade Complementar).
     */
    public static function possuiCurricular(array $tiposAtendimento): bool
    {
        return in_array(self::CURRICULAR_ETAPA_ENSINO, $tiposAtendimento)
            || in_array(self::CURRICULAR_ETAPA_ENSINO_COM_ATIVIDADE_COMPLEMENTAR, $tiposAtendimento);
    }

    /**
     * Indica se o tipo de atendimento inclui Atividade Complementar, considerando
     * tanto o código próprio quanto o combinado (Curricular com Atividade Complementar).
     */
    public static function possuiAtividadeComplementar(array $tiposAtendimento): bool
    {
        return in_array(self::ATIVIDADE_COMPLEMENTAR, $tiposAtendimento)
            || in_array(self::CURRICULAR_ETAPA_ENSINO_COM_ATIVIDADE_COMPLEMENTAR, $tiposAtendimento);
    }

    /**
     * Indica se o tipo de atendimento inclui Atendimento Educacional Especializado (AEE).
     */
    public static function possuiAee(array $tiposAtendimento): bool
    {
        return in_array(self::AEE, $tiposAtendimento);
    }
}
