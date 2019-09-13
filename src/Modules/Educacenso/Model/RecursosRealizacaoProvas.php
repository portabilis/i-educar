<?php

namespace iEducar\Modules\Educacenso\Model;

class RecursosRealizacaoProvas
{
    const NENHUM = 14;
    const AUXILIO_LEDOR = 1;
    const AUXILIO_TRANSCRICAO = 2;
    const GUIA_INTERPRETE = 3;
    const TRADUTOR_INTERPRETE_DE_LIBRAS = 4;
    const LEITURA_LABIAL = 5;
    const PROVA_AMPLIADA_FONTE_18 = 10;
    const PROVA_SUPERAMPLIADA_FONTE_24 = 8;
    const MATERIAL_DIDATICO_E_PROVA_EM_BRAILLE = 9;
    const CD_COM_AUDIO_PARA_DEFICIENTE_VISUAL = 11;
    const PROVA_LINGUA_PORTUGUESA_SEGUNDA_LINGUA_SURDOS = 12;
    const PROVA_EM_VIDEO_EM_LIBRAS = 13;

    public static function getDescriptiveValues()
    {
        return [
            self::NENHUM => 'Nenhum',
            self::AUXILIO_LEDOR => 'Auxílio ledor',
            self::AUXILIO_TRANSCRICAO => 'Auxílio transcrição',
            self::GUIA_INTERPRETE => 'Guia-intérprete',
            self::TRADUTOR_INTERPRETE_DE_LIBRAS => 'Tradutor-Intérprete de Libras',
            self::LEITURA_LABIAL => 'Leitura labial',
            self::PROVA_AMPLIADA_FONTE_18 => 'Prova Ampliada (Fonte 18)',
            self::PROVA_SUPERAMPLIADA_FONTE_24 => 'Prova superampliada (Fonte 24)',
            self::MATERIAL_DIDATICO_E_PROVA_EM_BRAILLE => 'Material didático e Prova em Braille',
            self::CD_COM_AUDIO_PARA_DEFICIENTE_VISUAL => 'CD com áudio para deficiente visual',
            self::PROVA_LINGUA_PORTUGUESA_SEGUNDA_LINGUA_SURDOS => 'Prova de Língua Portuguesa como segunda língua para surdos e deficientes auditivos',
            self::PROVA_EM_VIDEO_EM_LIBRAS => 'Prova em Vídeo em Libras',
        ];
    }
}