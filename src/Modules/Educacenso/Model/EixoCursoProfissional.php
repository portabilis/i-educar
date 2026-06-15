<?php

namespace iEducar\Modules\Educacenso\Model;

class EixoCursoProfissional
{
    public const AMBIENTE_E_SAUDE = 1;

    public const DESENVOLVIMENTO_EDUCACIONAL_E_SOCIAL = 2;

    public const CONTROLE_E_PROCESSOS_INDUSTRIAIS = 3;

    public const GESTAO_E_NEGOCIOS = 4;

    public const TURISMO_HOSPITALIDADE_E_LAZER = 5;

    public const INFORMACAO_E_COMUNICACAO = 6;

    public const INFRAESTRUTURA = 7;

    public const MILITAR = 8;

    public const PRODUCAO_ALIMENTICIA = 9;

    public const PRODUCAO_CULTURAL_E_DESIGN = 10;

    public const PRODUCAO_INDUSTRIAL = 11;

    public const RECURSOS_NATURAIS = 12;

    public const SEGURANCA = 13;

    public static function getDescriptiveValues()
    {
        return [
            self::AMBIENTE_E_SAUDE => 'Ambiente e saúde',
            self::DESENVOLVIMENTO_EDUCACIONAL_E_SOCIAL => 'Desenvolvimento educacional e social',
            self::CONTROLE_E_PROCESSOS_INDUSTRIAIS => 'Controle e processos industriais',
            self::GESTAO_E_NEGOCIOS => 'Gestão e negócios',
            self::TURISMO_HOSPITALIDADE_E_LAZER => 'Turismo, hospitalidade e lazer',
            self::INFORMACAO_E_COMUNICACAO => 'Informação e comunicação',
            self::INFRAESTRUTURA => 'Infraestrutura',
            self::MILITAR => 'Militar',
            self::PRODUCAO_ALIMENTICIA => 'Produção alimentícia',
            self::PRODUCAO_CULTURAL_E_DESIGN => 'Produção cultural e design',
            self::PRODUCAO_INDUSTRIAL => 'Produção industrial',
            self::RECURSOS_NATURAIS => 'Recursos naturais',
            self::SEGURANCA => 'Segurança',
        ];
    }
}
