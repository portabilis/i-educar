<?php

namespace iEducar\Modules\Educacenso\Model;

class ModalidadeCurso
{
    public const ENSINO_REGULAR = 1;
    public const EDUCACAO_ESPECIAL = 2;
    public const EJA = 3;
    public const EDUCACAO_PROFISSIONAL = 4;

    /**
     * @return array
     */
    public static function getDescriptiveValues()
    {
        return [
            self::ENSINO_REGULAR => 'Ensino regular',
            self::EDUCACAO_ESPECIAL => 'Educação especial',
            self::EJA => 'Educação de Jovens e Adultos (EJA)',
            self::EDUCACAO_PROFISSIONAL => 'Educação profissional',
        ];
    }
}
