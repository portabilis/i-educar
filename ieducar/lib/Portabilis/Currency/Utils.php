<?php

class Portabilis_Currency_Utils
{

    /**
     * Converte um valor numérico de moeda brasileira (ex: 2,32) para
     * estrangeira (2.32).
     */
    public static function moedaBrToUs($valor)
    {
        return str_replace(',', '.', (str_replace('.', '', $valor)));
    }

    /**
     * Converte um valor numérico de moeda estrangeira (ex: 2.32) para
     * brasileira (2,32).
     */
    public static function moedaUsToBr($valor)
    {
        return str_replace('.', ',', $valor);
    }
}
