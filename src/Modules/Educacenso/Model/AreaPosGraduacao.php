<?php

namespace iEducar\Modules\Educacenso\Model;

class AreaPosGraduacao
{
    public const EDUCACAO = 1;
    public const ARTES_HUMANIDADE = 2;
    public const CIENCIAS_SOCIAIS = 3;
    public const NEGOCIOS = 4;
    public const CENCIAS_NATURAIS = 5;
    public const COMPUTACAO = 6;
    public const ENGENHARIA = 7;
    public const AGRICULTURA = 8;
    public const SAUDE = 9;
    public const SERVICOS = 10;
    public const PROGRAMAS_BASICOS = 99;

    public static function getDescriptiveValues()
    {
        return [
            self::EDUCACAO => 'Educação',
            self::ARTES_HUMANIDADE => 'Artes e humanidades',
            self::CIENCIAS_SOCIAIS => 'Ciências sociais, comunicação e informação',
            self::NEGOCIOS => 'Negócios, administração e direito',
            self::CENCIAS_NATURAIS => 'Ciências naturais, matemática e estatística',
            self::COMPUTACAO => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
            self::ENGENHARIA => 'Engenharia, produção e construção',
            self::AGRICULTURA => 'Agricultura, silvicultura, pesca e veterinária',
            self::SAUDE => 'Saúde e bem-estar',
            self::SERVICOS => 'Serviços',
            self::PROGRAMAS_BASICOS => 'Programas básicos',
        ];
    }
}
