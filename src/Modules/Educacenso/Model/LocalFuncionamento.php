<?php

namespace iEducar\Modules\Educacenso\Model;

class LocalFuncionamento
{
    const PREDIO_ESCOLAR = 3;
    const SALAS_OUTRA_ESCOLA = 7;
    const GALPAO = 8;
    const UNIDADE_ATENDIMENTO_SOCIOEDUCATIVA = 9;
    const UNIDADE_PRISIONAL = 10;
    const OUTROS = 11;

    /**
     * @return array
     */
    public static function getDescriptiveValues()
    {
        return [
            self::PREDIO_ESCOLAR => 'Prédio Escolar',
            self::SALAS_OUTRA_ESCOLA => 'Sala(s) em outra escola',
            self::GALPAO => 'Galpão/rancho/paiol/barracão',
            self::UNIDADE_ATENDIMENTO_SOCIOEDUCATIVA => 'Unidade de atendimento socioeducativa',
            self::UNIDADE_PRISIONAL => 'Unidade prisional',
            self::OUTROS => 'Outros',
        ];
    }
}
