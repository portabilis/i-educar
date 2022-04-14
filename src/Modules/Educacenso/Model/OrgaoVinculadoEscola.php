<?php

namespace iEducar\Modules\Educacenso\Model;

class OrgaoVinculadoEscola
{
    public const OUTRO = 1;
    public const EDUCACAO = 2;
    public const SEGURANCA = 3;
    public const SAUDE = 4;

    public static function getDescriptiveValues()
    {
        return [
            self::EDUCACAO => 'Secretaria de Educação/Ministério de Educação',
            self::SEGURANCA => 'Secretaria de Segurança Pública/Forças Armadas/Militar',
            self::SAUDE => 'Secretaria da Saúde/Ministério da Saúde',
            self::OUTRO => 'Outro órgão da administração pública'
        ];
    }
}
