<?php

namespace iEducar\Modules\Educacenso\Model;

class FormacaoContinuada
{
    public const CRECHE = 1;
    public const PRE_ESCOLA = 2;
    public const ANOS_INICIAIS = 3;
    public const ANOS_FINAIS = 4;
    public const ENSINO_MEDIO = 5;
    public const EJA = 6;
    public const EDUCACAO_ESPECIAL = 7;
    public const EDUCACAO_INDIGENA = 8;
    public const EDUCACAO_DO_CAMPO = 9;
    public const EDUCACAO_AMBIENTAL = 10;
    public const EDUCACAO_DIREITOS_HUMANOS = 11;
    public const GENERO_DIVERSIDADE_SEXUAL = 12;
    public const DIREITOS_CRIANCA_ADOLESCENTE = 13;
    public const RELACOES_ETNICO_RACIAIS = 14;
    public const OUTROS = 15;
    public const NENHUM = 16;
    public const GESTAO_ESCOLAR = 17;

    public static function getDescriptiveValues()
    {
        return [
            self::CRECHE => 'Creche (0 a 3 anos)',
            self::PRE_ESCOLA => 'Pré-escola (4 e 5 anos)',
            self::ANOS_INICIAIS => 'Anos iniciais do ensino fundamental',
            self::ANOS_FINAIS => 'Anos finais do ensino fundamental',
            self::ENSINO_MEDIO => 'Ensino médio',
            self::EJA => 'Educação de jovens e adultos',
            self::EDUCACAO_ESPECIAL => 'Educação especial',
            self::EDUCACAO_INDIGENA => 'Educação indígena',
            self::EDUCACAO_DO_CAMPO => 'Educação do campo',
            self::EDUCACAO_AMBIENTAL => 'Educação ambiental',
            self::EDUCACAO_DIREITOS_HUMANOS => 'Educação em direitos humanos',
            self::GENERO_DIVERSIDADE_SEXUAL => 'Gênero e diversidade sexual',
            self::DIREITOS_CRIANCA_ADOLESCENTE => 'Direitos de criança e adolescente',
            self::RELACOES_ETNICO_RACIAIS => 'Educação para as relações étnico-raciais e História e cultura Afro-Brasileira e Africana',
            self::GESTAO_ESCOLAR => 'Gestão Escolar',
            self::OUTROS => 'Outros',
            self::NENHUM => 'Nenhum'
        ];
    }
}
