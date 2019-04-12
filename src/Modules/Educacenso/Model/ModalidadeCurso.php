<?php

namespace iEducar\Modules\Educacenso\Model;

class ModalidadeCurso
{
    const ENSINO_REGULAR = 1;
    const EDUCACAO_ESPECIAL = 2;
    const EJA = 3;
    const EDUCACAO_PROFISSIONAL = 4;

    /**
     * @return array
     */
    public static function getDescriptiveValues()
    {
        return [
            self::ENSINO_REGULAR => 'Ensino regular',
            self::EDUCACAO_ESPECIAL => 'Educação Especial - Modalidade Substitutiva',
            self::EJA => 'Educação de Jovens e Adultos (EJA)',
            self::EDUCACAO_PROFISSIONAL => 'Educação profissional',
        ];
    }
}