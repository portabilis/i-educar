<?php

namespace iEducar\Modules\Educacenso\Model;

class OrgaosColegiados
{
    const OUTROS = 1;
    const ASSOCIACAO_PAIS = 2;
    const ASSOCIACAO_PAIS_E_MESTRES = 3;
    const CONSELHO_ESCOLAR = 4;
    const GREMIO_ESTUDANTIL = 5;
    const NENHUM = 6;

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