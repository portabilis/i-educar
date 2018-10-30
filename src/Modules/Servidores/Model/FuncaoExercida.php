<?php

namespace iEducar\Modules\Servidores\Model;

class FuncaoExercida
{
    const DOCENTE = 1;
    const AUXILIAR_EDUCACIONAL = 2;
    const MONITOR_ATIVIDADE_COMPLEMENTAR = 3;
    const INTERPRETE_LIBRAS = 4;
    const DOCENTE_TITULAR_EAD = 5;
    const DOCENTE_TUTOR_EAD = 6;

    public static function getDescriptiveValues()
    {
        return [
            self::DOCENTE => 'Docente',
            self::AUXILIAR_EDUCACIONAL => 'Auxiliar/Assistente educacional',
            self::MONITOR_ATIVIDADE_COMPLEMENTAR => 'Profissional/Monitor de atividade complementar',
            self::INTERPRETE_LIBRAS => 'Tradutor Intérprete de LIBRAS',
            self::DOCENTE_TITULAR_EAD => 'Docente titular - Coordenador de tutoria (de módulo ou disciplina) - EAD',
            self::DOCENTE_TUTOR_EAD => 'Docente tutor - Auxiliar (de módulo ou disciplina) - EAD'
        ];
    }

}
