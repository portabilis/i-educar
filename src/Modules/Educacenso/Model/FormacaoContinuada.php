<?php

namespace iEducar\Modules\Educacenso\Model;

class FormacaoContinuada
{
    const CRECHE = 1;
    const PRE_ESCOLA = 2;
    const ANOS_INICIAIS = 3;
    const ANOS_FINAIS = 4;
    const ENSINO_MEDIO = 5;
    const EJA = 6;
    const EDUCACAO_ESPECIAL = 7;
    const EDUCACAO_INDIGENA = 8;
    const EDUCACAO_DO_CAMPO = 9;
    const EDUCACAO_AMBIENTAL = 10;
    const EDUCACAO_DIREITOS_HUMANOS = 11;
    const GENERO_DIVERSIDADE_SEXUAL = 12;
    const DIREITOS_CRIANCA_ADOLESCENTE = 13;
    const RELACOES_ETNICO_RACIAIS = 14;
    const OUTROS = 15;
    const NENHUM = 16;
    const GESTAO_ESCOLAR = 17;

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
