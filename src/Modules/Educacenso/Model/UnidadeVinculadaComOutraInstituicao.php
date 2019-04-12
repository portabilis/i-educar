<?php

namespace iEducar\Modules\Educacenso\Model;

class UnidadeVinculadaComOutraInstituicao
{
    const SEM_VINCULO = 0;
    const EDUCACAO_BASICA = 1;
    const ENSINO_SUPERIOR = 2;

    /**
     * @return array
     */
    public static function getDescriptiveValues()
    {
        return [
            self::SEM_VINCULO => 'Sem vínculo com outra instituição',
            self::EDUCACAO_BASICA => 'Unidade vinculada a escola de Educação Básica',
            self::ENSINO_SUPERIOR => 'Unidade Ofertante de Educação Superior'
        ];
    }
}
