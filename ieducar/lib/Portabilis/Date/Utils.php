<?php

class Portabilis_Date_Utils
{
    /**
     * Recebe uma data no formato dd/mm/yyyy e retorna no formato postgres
     * yyyy-mm-dd.
     *
     * @param string $date
     *
     * @return string
     */
    public static function brToPgSQL($date)
    {
        if (!$date) {
            return $date;
        }

        list($dia, $mes, $ano) = explode('/', $date);

        return "$ano-$mes-$dia";
    }

    /**
     * Recebe uma data no formato dd/mm e retorna no formato postgres
     * yyyy-mm-dd.
     *
     * @param string $date
     *
     * @return string
     */
    public static function brToPgSQL_ddmm($date)
    {
        if (!$date) {
            return $date;
        }

        list($dia, $mes) = explode('/', $date);

        $ano = '1900';

        return "$ano-$mes-$dia";
    }

    /**
     * Recebe uma data no formato postgres yyyy-mm-dd hh:mm:ss.uuuu e retorna
     * no formato br dd/mm/yyyy hh:mm:ss.
     *
     * @param string $timestamp
     *
     * @return null|string
     */
    public static function pgSQLToBr($timestamp)
    {
        $pgFormat = 'Y-m-d';
        $brFormat = 'd/m/Y';

        $hasTime = strpos($timestamp, ':') > -1;

        if ($hasTime) {
            $pgFormat .= ' H:i:s';
            $brFormat .= ' H:i:s';

            $hasMicroseconds = strpos($timestamp, '.') > -1;

            if ($hasMicroseconds) {
                $pgFormat .= '.u';
            }
        }

        $d = DateTime::createFromFormat($pgFormat, $timestamp);

        return ($d ? $d->format($brFormat) : null);
    }

    /**
     * Recebe uma data no formato postgres yyyy-mm-dd hh:mm:ss.uuuu e retorna
     * no formato br dd/mm.
     *
     * @param string $timestamp
     *
     * @return null|string
     */
    public static function pgSQLToBr_ddmm($timestamp)
    {
        $pgFormat = 'Y-m-d';
        $brFormat = 'd/m';

        $d = DateTime::createFromFormat($pgFormat, $timestamp);

        return ($d ? $d->format($brFormat) : null);
    }

    public static function validaData($date)
    {
        $dateCheck = (bool) strtotime($date);

        if ($dateCheck) {
            $date_arr = explode('/', $date);

            if (count($date_arr) == 3) {
                if (checkdate($date_arr[1], $date_arr[0], $date_arr[2])) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Recebe uma data no formato yyyy-mm-dd e verifica se é uma data válida
     * considerando o ano bissexto.
     *
     * @param string $data
     *
     * @return bool
     */
    public static function checkDateBissexto($data)
    {
        $data = date_parse_from_format('Y-m-d', $data);
        $day = (int) $data['day'];
        $month = (int) $data['month'];
        $year = (int) $data['year'];

        return ($day == 29 && !checkdate($month, $day, $year));
    }

    public static function isDateValid($date, $format = 'Y-m-d')
    {
        return (boolean) DateTime::createFromFormat($format, $date);
    }
}
