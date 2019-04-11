<?php

namespace iEducar\Modules\Educacenso\Model;

class OrgaoVinculadoEscola
{
    const OUTRO = 1;
    const EDUCACAO = 2;
    const SEGURANCA = 3;
    const SAUDE = 4;

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