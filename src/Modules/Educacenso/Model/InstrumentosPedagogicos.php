<?php

namespace iEducar\Modules\Educacenso\Model;

class InstrumentosPedagogicos
{
    public const ACERVO_MULTIMIDIA = 1;
    public const BRINQUEDROS_EDUCACAO_INFANTIL = 2;
    public const MATERIAIS_CIENTIFICOS = 3;
    public const AMPLIFICACAO_DIFUSAO_SOM = 4;
    public const INSTRUMENTOS_MUSICAIS = 5;
    public const JOGOS_EDUCATIVOS = 6;
    public const MATERIAIS_ATIVIDADES_CULTURAIS = 7;
    public const MATERIAIS_PRATICA_DESPORTIVA = 8;
    public const MATERIAIS_EDUCACAO_INDIGENA = 9;
    public const MATERIAIS_RELACOES_ETNICOS_RACIAIS = 10;
    public const MATERIAIS_EDUCACAO_CAMPO = 11;
    public const NENHUM_DOS_INSTRUMENTOS_LISTADOS = 12;
    public const MATERIAL_EDUCACAO_PROFISSIONAL = 13;

    public static function getDescriptiveValues()
    {
        return [
            self::ACERVO_MULTIMIDIA => 'Acervo multimídia',
            self::BRINQUEDROS_EDUCACAO_INFANTIL => 'Brinquedos para Educação Infantil',
            self::MATERIAIS_CIENTIFICOS => 'Conjunto de materiais científicos',
            self::AMPLIFICACAO_DIFUSAO_SOM => 'Equipamento para amplificação e difusão de som/áudio',
            self::INSTRUMENTOS_MUSICAIS => 'Instrumentos musicais para conjunto, banda/fanfarra e/ou aulas de música',
            self::JOGOS_EDUCATIVOS => 'Jogos educativos',
            self::MATERIAIS_ATIVIDADES_CULTURAIS => 'Materiais para atividades culturais e artísticas',
            self::MATERIAL_EDUCACAO_PROFISSIONAL => 'Material para educação profissional',
            self::MATERIAIS_PRATICA_DESPORTIVA => 'Materiais para prática desportiva e recreação',
            self::MATERIAIS_EDUCACAO_INDIGENA => 'Materiais pedagógicos para a educação escolar indígena',
            self::MATERIAIS_RELACOES_ETNICOS_RACIAIS => 'Materiais pedagógicos para a educação das Relações Étnicos Raciais',
            self::MATERIAIS_EDUCACAO_CAMPO => 'Materiais pedagógicos para a educação do campo',
            self::NENHUM_DOS_INSTRUMENTOS_LISTADOS => 'Nenhum dos instrumentos listados',
        ];
    }
}
