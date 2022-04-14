<?php

namespace iEducar\Modules\Educacenso\Model;

class RecursosRealizacaoProvas
{
    public const NENHUM = 14;
    public const AUXILIO_LEDOR = 1;
    public const AUXILIO_TRANSCRICAO = 2;
    public const GUIA_INTERPRETE = 3;
    public const TRADUTOR_INTERPRETE_DE_LIBRAS = 4;
    public const LEITURA_LABIAL = 5;
    public const PROVA_AMPLIADA_FONTE_18 = 10;
    public const PROVA_SUPERAMPLIADA_FONTE_24 = 8;
    public const MATERIAL_DIDATICO_E_PROVA_EM_BRAILLE = 9;
    public const CD_COM_AUDIO_PARA_DEFICIENTE_VISUAL = 11;
    public const PROVA_LINGUA_PORTUGUESA_SEGUNDA_LINGUA_SURDOS = 12;
    public const PROVA_EM_VIDEO_EM_LIBRAS = 13;

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
