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
 * Portabilis_Report_ReportCore class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */

class Portabilis_Report_ReportCore
{

 function __construct() {
    $this->requiredArgs = array();
    $this->args         = array();

    // set required args on construct, because ReportCoreController access it args before call dumps
    $this->requiredArgs();
  }

  // wrapper for Portabilis_Array_Utils::merge
  protected static function mergeOptions($options, $defaultOptions) {
    return Portabilis_Array_Utils::merge($options, $defaultOptions);
  }

  function addArg($name, $value) {
    if (is_string($value))
      $value = $value;

    $this->args[$name] = $value;
  }

  function addRequiredArg($name) {
    $this->requiredArgs[] = $name;
  }

  function validatesPresenseOfRequiredArgs() {
    foreach($this->requiredArgs as $requiredArg) {

      if (! isset($this->args[$requiredArg]) || empty($this->args[$requiredArg]))
        throw new Exception("The required arg '{$requiredArg}' wasn't set or is empty!");
    }
  }

  function dumps($options = array()) {
    $defaultOptions = array('report_factory' => null, 'options' => array());
    $options        = self::mergeOptions($options, $defaultOptions);

    $this->validatesPresenseOfRequiredArgs();

    $reportFactory = ! is_null($options['report_factory']) ? $options['report_factory'] : $this->reportFactory();

    return $reportFactory->dumps($this, $options['options']);
  }

  function reportFactory() {
    $factoryClassName = $GLOBALS['coreExt']['Config']->report->default_factory;
    $factoryClassPath = str_replace('_', '/', $factoryClassName) . '.php';

    if (! $factoryClassName)
      throw new CoreExt_Exception("No report.default_factory defined in configurations!");

    // don't fail if path not exists.
    include_once $factoryClassPath;

    if (! class_exists($factoryClassName))
      throw new CoreExt_Exception("Class '$factoryClassName' not found in path '$factoryClassPath'");

    return new $factoryClassName();
  }

  // methods that must be overridden

  function templateName() {
    throw new Exception("The method 'templateName' must be overridden!");
  }

  function requiredArgs() {
    throw new Exception("The method 'requiredArgs' must be overridden!");
  }
}
