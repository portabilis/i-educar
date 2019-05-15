<?php

namespace iEducar\Modules\Educacenso\Model;

class Escolaridade
{
    const NAO_CONCLUIU_ENSINO_FUNDAMENTAL = 1;
    const ENSINO_FUNDAMENTAL = 2;
    const ENSINO_MEDIO = 7;
    const EDUCACAO_SUPERIOR = 6;

    /**
     * @return array
     */
    public static function getDescriptiveValues()
    {
        return [
            self::NAO_CONCLUIU_ENSINO_FUNDAMENTAL => 'Não concluiu o Ensino Fundamental',
            self::ENSINO_FUNDAMENTAL => 'Ensino fundamental',
            self::ENSINO_MEDIO => 'Ensino médio',
            self::EDUCACAO_SUPERIOR => 'Educação superior',
        ];
    }
}
