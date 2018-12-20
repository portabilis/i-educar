<?php

require_once 'App/Date/Exception.php';

class App_Date_Utils
{
    /**
     * Retorna o ano de uma string nos formatos dd/mm/yyyy e dd/mm/yyyy hh:ii:ss.
     *
     * @param string $date
     *
     * @return int
     */
    public static function getYear($date)
    {
        $parts = explode('/', $date);
        $year = explode(' ', $parts[2]);

        if (is_array($year)) {
            $year = $year[0];
        }

        return (int) $year;
    }

    /**
     * Verifica se ao menos uma das datas de um array é do ano especificado.
     *
     * @param array $dates Datas nos formatos dd/mm/yyyy [hh:ii:ss].
     * @param int   $year  Ano esperado.
     * @param int   $at    Quantidade mínima de datas esperadas no ano $year.
     *
     * @return bool TRUE se ao menos uma das datas estiver no ano esperado.
     *
     * @throws App_Date_Exception
     */
    public static function datesYearAtLeast(array $dates, $year, $at = 1)
    {
        $matches = 0;

        foreach ($dates as $date) {
            $dateYear = self::getYear($date);

            if ($year == $dateYear) {
                $matches++;
            }
        }

        if ($matches >= $at) {
            return true;
        }

        throw new App_Date_Exception(sprintf(
            'Ao menos "%d" das datas informadas deve ser do ano "%d". Datas: "%s".',
            $at,
            $year,
            implode('", "', $dates)
        ));
    }
}
