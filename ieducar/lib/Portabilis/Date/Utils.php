<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis_Date
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

/**
 * Portabilis_Date_Utils class.
 *
 * Possui métodos
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   App_Date
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */

class Portabilis_Date_Utils
{
  /**
   * Recebe uma data no formato dd/mm/yyyy e retorna no formato postgres yyyy-mm-dd.
   * @param string $date
   */
  public static function brToPgSQL($date) {
    if (! $date)
      return $date;

    // #TODO usar classe nativa datetime http://www.phptherightway.com/#date_and_time ?
    list($dia, $mes, $ano) = explode("/", $date);
    return "$ano-$mes-$dia";
  }

  /**
   * Recebe uma data no formato dd/mm e retorna no formato postgres yyyy-mm-dd.
   * @param string $date
   */
  public static function brToPgSQL_ddmm($date) {
    if (! $date)
      return $date;

    // #TODO usar classe nativa datetime http://www.phptherightway.com/#date_and_time ?
    list($dia, $mes) = explode("/", $date);
    $ano = '1900';
    return "$ano-$mes-$dia";
  }

  /**
   * Recebe uma data no formato postgres yyyy-mm-dd hh:mm:ss.uuuu e retorna no formato br dd/mm/yyyy hh:mm:ss.
   * @param string $timestamp
   */
  public static function pgSQLToBr($timestamp) {
    $pgFormat = 'Y-m-d';
    $brFormat = 'd/m/Y';

    $hasTime  = strpos($timestamp, ':') > -1;

    if ($hasTime) {
      $pgFormat .= ' H:i:s';
      $brFormat .= ' H:i:s';

      $hasMicroseconds = strpos($timestamp, '.') > -1;

      if ($hasMicroseconds)
        $pgFormat .= '.u';
    }

    $d = DateTime::createFromFormat($pgFormat, $timestamp);

    return ($d ? $d->format($brFormat) : null);
  }

  /**
   * Recebe uma data no formato postgres yyyy-mm-dd hh:mm:ss.uuuu e retorna no formato br dd/mm.
   * @param string $timestamp
   */
  public static function pgSQLToBr_ddmm($timestamp) {
    $pgFormat = 'Y-m-d';
    $brFormat = 'd/m';

    $d = DateTime::createFromFormat($pgFormat, $timestamp);

    return ($d ? $d->format($brFormat) : null);
  }
  public static function validaData($date){
    $date_arr = explode('/', $date);
    if(count($date_arr) == 3){
        if(checkdate($date_arr[1], $date_arr[0], $date_arr[2])){
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
  }

  /**
   * Recebe uma data no formato yyyy-mm-dd e verifica se é uma data válida considerando o ano bissexto.
   * @param string $timestamp
   */
  public static function checkDateBissexto($data) {
    $data  = date_parse_from_format("Y-m-d", $data);
    $day   = (int)$data["day"];
    $month = (int)$data["month"];
    $year  = (int)$data["year"];

    return ($day == 29 && !checkdate($month, $day, $year));
  }

  public static function isDateValid($date, $format = 'Y-m-d')
  {
    return (boolean) DateTime::createFromFormat($format, $date);
  }

}
