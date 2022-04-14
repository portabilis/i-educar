<?php

namespace iEducar\Modules\Educacenso\Model;

class OrgaosColegiados
{
    public const OUTROS = 1;
    public const ASSOCIACAO_PAIS = 2;
    public const ASSOCIACAO_PAIS_E_MESTRES = 3;
    public const CONSELHO_ESCOLAR = 4;
    public const GREMIO_ESTUDANTIL = 5;
    public const NENHUM = 6;

    public static function getDescriptiveValues()
    {
        return [
            self::ASSOCIACAO_PAIS => 'Associação de Pais',
            self::ASSOCIACAO_PAIS_E_MESTRES => 'Associação de Pais e Mestres',
            self::CONSELHO_ESCOLAR => 'Conselho Escolar',
            self::GREMIO_ESTUDANTIL => 'Grêmio Estudantil',
            self::OUTROS => 'Outros',
            self::NENHUM => 'Não há órgãos colegiados em funcionamento',
        ];
    }
}
