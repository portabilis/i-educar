<?php

namespace iEducar\Modules\Educacenso\Model;

class TratamentoLixo
{
    public const NAO_FAZ = 1;
    public const SEPARACAO = 2;
    public const REAPROVEITAMENTO = 3;
    public const RECICLAGEM = 4;

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
