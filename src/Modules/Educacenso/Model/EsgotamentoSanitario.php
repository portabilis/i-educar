<?php

namespace iEducar\Modules\Educacenso\Model;

class EsgotamentoSanitario
{
    const REDE_PUBLICA = 1;
    const FOSSA_SEPTICA = 2;
    const INEXISTENTE = 3;
    const FOSSA_RUDIMENTAR = 4;

    public static function getDescriptiveValues()
    {
        return [
            self::REDE_PUBLICA => 'Rede pública',
            self::FOSSA_SEPTICA => 'Fossa séptica',
            self::INEXISTENTE => 'Não há esgotamento sanitário',
            self::FOSSA_RUDIMENTAR => 'Fossa rudimentar/comum',
        ];
    }
}
