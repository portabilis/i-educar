<?php

namespace iEducar\Modules\Educacenso\Model;

class DestinacaoLixo
{
    const SERVICO_COLETA = 1;
    const QUEIMA = 2;
    const DESCARTA_OUTRA_AREA = 3;
    const DESTINACAO_LICENCIADA = 5;
    const ENTERRA = 7;

    public static function getDescriptiveValues()
    {
        return [
            self::SERVICO_COLETA => 'Serviço de coleta',
            self::QUEIMA => 'Queima',
            self::ENTERRA => 'Enterra',
            self::DESTINACAO_LICENCIADA => 'Leva a uma destinação final licenciada pelo poder público',
            self::DESCARTA_OUTRA_AREA => 'Descarta em outra área',
        ];
    }
}
