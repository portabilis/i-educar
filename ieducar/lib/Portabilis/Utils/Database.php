<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

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
 * Portabilis_Utils_Database class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_Utils_Database {

  static $_db;

  // wrapper for Portabilis_Array_Utils::merge
  protected static function mergeOptions($options, $defaultOptions) {
    return Portabilis_Array_Utils::merge($options, $defaultOptions);
  }

  public static function db() {
    if (! isset(self::$_db))
      self::$_db = new clsBanco();

    return self::$_db;
  }

  public static function fetchPreparedQuery($sql, $options = array()) {
    $result         = array();

    $defaultOptions = array('params'      => array(),
                            'show_errors' => true,
                            'return_only' => '',
                            'messenger'   => null);

    $options        = self::mergeOptions($options, $defaultOptions);

    // options validations
    //if ($options['show_errors'] and is_null($options['messenger']))
    //  throw new Exception("When 'show_errors' is true you must pass the option messenger too.");


    try {
      if (self::db()->execPreparedQuery($sql, $options['params']) != false) {
        while (self::db()->ProximoRegistro())
          $result[] = self::db()->Tupla();

        if (in_array($options['return_only'], array('first-line', 'first-row', 'first-record')) and count($result) > 0)
          $result = $result[0];
        elseif ($options['return_only'] == 'first-field' and count($result) > 0 and count($result[0]) > 0)
          $result = $result[0][0];
      }
    }
    catch(Exception $e) {
      if ($options['show_errors'] and ! is_null($options['messenger']))
        $options['messenger']->append($e->getMessage(), 'error');
      else
        throw $e;
    }

    return $result;
  }

  // helper para consultas que buscam apenas o primeiro campo,
  // considera o segundo argumento o array de options esperado por fetchPreparedQuery
  // a menos que este não possua um chave params ou não seja um array,
  // neste caso o considera como params
  public static function selectField($sql, $paramsOrOptions = array()) {

    if (! is_array($paramsOrOptions) || ! isset($paramsOrOptions['params']))
      $paramsOrOptions = array('params' => $paramsOrOptions);

    $paramsOrOptions['return_only'] = 'first-field';
    return self::fetchPreparedQuery($sql, $paramsOrOptions);
  }

  // helper para consultas que buscam apenas a primeira linha
  // considera o segundo argumento o array de options esperado por fetchPreparedQuery
  // a menos que este não possua um chave params ou não seja um array,
  // neste caso o considera como params
  public static function selectRow($sql, $paramsOrOptions = array()) {

    if (! is_array($paramsOrOptions) || ! isset($paramsOrOptions['params']))
      $paramsOrOptions = array('params' => $paramsOrOptions);

    $paramsOrOptions['return_only'] = 'first-row';
    return self::fetchPreparedQuery($sql, $paramsOrOptions);
  }
}
