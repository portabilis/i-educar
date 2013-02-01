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
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   App_Date
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'App/Date/Exception.php';

/**
 * App_Date_Utils class.
 *
 * Possui métodos
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   App_Date
 * @since     Classe disponível desde a versão 1.2.0
 * @version   @@package_version@@
 */
class App_Date_Utils
{
  /**
   * Retorna o ano de uma string nos formatos dd/mm/yyyy e dd/mm/yyyy hh:ii:ss.
   * @param string $date
   * @param int
   */
  public static function getYear($date)
  {
    $parts = explode('/', $date);
    $year  = explode(' ', $parts[2]);

    if (is_array($year)) {
      $year = $year[0];
    }

    return (int) $year;
  }

  /**
   * Verifica se ao menos uma das datas de um array é do ano especificado.
   * @param   array  $dates Datas nos formatos dd/mm/yyyy [hh:ii:ss].
   * @param   int    $year  Ano esperado.
   * @param   int    $at    Quantidade mínima de datas esperadas no ano $year.
   * @return  bool   TRUE se ao menos uma das datas estiver no ano esperado.
   * @throws  App_Date_Exception
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
      return TRUE;
    }

    throw new App_Date_Exception(sprintf(
      'Ao menos "%d" das datas informadas deve ser do ano "%d". Datas: "%s".',
      $at, $year, implode('", "', $dates)
    ));
  }
}