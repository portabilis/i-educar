<?php

namespace iEducar\Modules\Educacenso\Model;

class InstrumentosPedagogicos
{
    const ACERVO_MULTIMIDIA = 1;
    const BRINQUEDROS_EDUCACAO_INFANTIL = 2;
    const MATERIAIS_CIENTIFICOS = 3;
    const AMPLIFICACAO_DIFUSAO_SOM = 4;
    const INSTRUMENTOS_MUSICAIS = 5;
    const JOGOS_EDUCATIVOS = 6;
    const MATERIAIS_ATIVIDADES_CULTURAIS = 7;
    const MATERIAIS_PRATICA_DESPORTIVA = 8;
    const MATERIAIS_EDUCACAO_INDIGENA = 9;
    const MATERIAIS_RELACOES_ETNICOS_RACIAIS = 10;
    const MATERIAIS_EDUCACAO_CAMPO = 11;

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
            self::MATERIAIS_PRATICA_DESPORTIVA => 'Materiais para prática desportiva e recreação',
            self::MATERIAIS_EDUCACAO_INDIGENA => 'Materiais pedagógicos para a educação escolar indígena',
            self::MATERIAIS_RELACOES_ETNICOS_RACIAIS => 'Materiais pedagógicos para a educação das Relações Étnicos Raciais',
            self::MATERIAIS_EDUCACAO_CAMPO => 'Materiais pedagógicos para a educação do campo',
        ];
    }
}