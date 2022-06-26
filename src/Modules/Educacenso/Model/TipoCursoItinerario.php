<?php

namespace iEducar\Modules\Educacenso\Model;

class TipoCursoItinerario
{
    public const CURSO_TECNICO = 1;
    public const QUALIFICACAO_PROFISSIONAL = 2;

    public static function getDescriptiveValues()
    {
        return [
            self::CURSO_TECNICO => 'Curso técnico',
            self::QUALIFICACAO_PROFISSIONAL => 'Qualificação profissional técnica',
        ];
    }
}
