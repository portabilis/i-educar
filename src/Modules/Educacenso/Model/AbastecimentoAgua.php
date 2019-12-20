<?php

namespace iEducar\Modules\Educacenso\Model;

class AbastecimentoAgua
{
    const REDE_PUBLICA = 1;
    const POCO_ARTESIANO = 2;
    const CACIMBA_CISTERNA_POCO = 3;
    const FONTE = 4;
    const INEXISTENTE = 5;

    public static function getDescriptiveValues()
    {
        return [
            self::REDE_PUBLICA => 'Rede pública',
            self::POCO_ARTESIANO => 'Poço artesiano',
            self::CACIMBA_CISTERNA_POCO => 'Cacimba/cisterna/poço',
            self::FONTE => 'Fonte/rio/igarapé/riacho/córrego',
            self::INEXISTENTE => 'Não há abastecimento de água',
        ];
    }
}
