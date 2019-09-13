<?php

namespace iEducar\Modules\Educacenso\Model;

class TratamentoLixo
{
    const NAO_FAZ = 1;
    const SEPARACAO = 2;
    const REAPROVEITAMENTO = 3;
    const RECICLAGEM = 4;

    public static function getDescriptiveValues()
    {
        return [
            self::SEPARACAO => 'Separação do lixo/resíduos',
            self::REAPROVEITAMENTO => 'Reaproveitamento/reutilização',
            self::RECICLAGEM => 'Reciclagem',
            self::NAO_FAZ => 'Não faz tratamento',
        ];
    }
}