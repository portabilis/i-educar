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
 * @package   Portabilis
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'lib/Portabilis/Array/Utils.php';

/**
 * Portabilis_Utils_Float class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_Utils_Float {

  // wrapper for Portabilis_Array_Utils::merge
  protected static function mergeOptions($options, $defaultOptions) {
    return Portabilis_Array_Utils::merge($options, $defaultOptions);
  }

  /* Limita as casas decimais de um numero floar, SEM arredonda-lo,
     ex: para 4.96, usando decimal_points = 1, será retornado 4.9 e não 5.
  */
  public static function limitDecimal($value, $options = array()) {

    if (! is_numeric($value))
      throw new Exception("Value must be numeric!");

    elseif(is_integer($value))
      return (float)$value;

    $defaultOptions = array('decimal_points' => 2);
    $options        = self::mergeOptions($options, $defaultOptions);

    // converts a float value to string, eg: 4.96789 will be '4,96789'
    $newValue  = (string)$value;

    // splits the values after and before de decimal point,
    // eg: '4,96789', will be splited in '4' and '96789'
    $digits    = explode(',', $newValue);

    // limits the decimal points, using the decimal_points option (defaults to 2),
    // eg: .96789 will be limited to .96
    $digits[1] = substr($digits[1], 0, $options['decimal_points']);

    // joint the the digits into a float (string) value, eg: '4' and '96', will be '4.96'
    $newValue  = $digits[0] . '.' . $digits[1];

    return (float)$newValue;
  }
}
?>
