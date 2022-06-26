<?php

namespace iEducar\Modules\Educacenso\Model;

class FormasContratacaoPoderPublico
{
    public const TERMO_COLABORACAO = 1;
    public const TERMO_FOMENTO = 2;
    public const ACORDO_COOPERACAO = 3;
    public const CONTRATO_PRESTACAO_SERVICO = 4;
    public const TERMO_COOPERACAO_TECNICA = 5;
    public const CONTRATO_CONSORCIO = 6;

    public static function getDescriptiveValues()
    {
        return [
            self::TERMO_COLABORACAO => 'Termo de colaboração (Lei nº 13.019/2014)',
            self::TERMO_FOMENTO => 'Termo de fomento (Lei nº 13.019/2014)',
            self::ACORDO_COOPERACAO => 'Acordo de cooperação (Lei nº 13.019/2014)',
            self::CONTRATO_PRESTACAO_SERVICO => 'Contrato de prestação de serviço',
            self::TERMO_COOPERACAO_TECNICA => 'Termo de cooperação técnica e financeira',
            self::CONTRATO_CONSORCIO => 'Contrato de consórcio público/Convênio de cooperação'
        ];
    }
}
