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

/**
 * SelectMenusHelper class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */


/* permite adicionar inputs de seleção dinamica ao formulário,
   recebendo diretamente as opcoes do input, sem necessidade de passar
   um array com um array de opções, ex:

   Ao invés de:
     $this->inputsHelper()->dynamicInput('instituicao', array('options' => array(required' => false)));

   Pode-se usar:
     $this->inputsHelper()->dynamicInput('instituicao', array(required' => false));

     Ou
     $this->inputsHelper()->dynamicInput('instituicao', array(), array('options' => array(required' => false)));

     Ou
     $this->inputsHelper()->dynamicInput(array('instituicao', 'escola', 'pesquisaAluno'));
*/

class Portabilis_View_Helper_Inputs {

  public function __construct($viewInstance) {
    $this->viewInstance = $viewInstance;
  }


  public function dynamicInput($helperNames, $inputOptions = array(), $helperOptions = array()) {
    $options = $this->mergeInputOptions($inputOptions, $helperOptions);

    if (! is_array($helperNames))
      $helperNames = array($helperNames);

    foreach($helperNames as $helperName) {
      $helperClassName = "Portabilis_View_Helper_DynamicInput_" . ucfirst($helperName);
      $this->includeHelper($helperClassName);

      $helper = new $helperClassName($this->viewInstance);
      $helper->$helperName($options);
    }
  }


  public function textInput($objectName, $attrNames, $inputOptions = array(), $helperOptions = array()) {
    $helperName = 'text';
    $options    = $this->mergeInputOptions($inputOptions, $helperOptions);

    if (! is_array($attrNames))
      $attrNames = array($attrNames);

    foreach($attrNames as $attrName) {
      $this->input($helperName, $objectName, $attrName, $options);
    }
  }


  public function selectInput($objectName, $attrName, $inputOptions = array(), $helperOptions = array()) {
    $helperName = 'select';
    $options    = $this->mergeInputOptions($inputOptions, $helperOptions);

    $this->input($helperName, $objectName, $attrName, $options);
  }


  public function searchInput($objectName, $attrName, $inputOptions = array(), $helperOptions = array()) {
    $helperName = 'search';
    $options    = $this->mergeInputOptions($inputOptions, $helperOptions);

    $this->input($helperName, $objectName, $attrName, $options);
  }


  protected function input($helperName, $objectName, $attrName, $options = array()) {
    $helperClassName = "Portabilis_View_Helper_Input_" . ucfirst($helperName);

    $this->includeHelper($helperClassName);
    $helper = new $helperClassName($this->viewInstance);
    $helper->$helperName($objectName, $attrName, $options);
  }


  protected function includeHelper($helperClassName) {
    $classPath       = str_replace('_', '/', $helperClassName) . '.php';

    // usado include_once para continuar execução script mesmo que o path inexista.
    include_once $classPath;

    if (! class_exists($helperClassName))
      throw new CoreExt_Exception("Class '$helperClassName' not found in path $classPath.");
  }

  protected function mergeInputOptions($inputOptions = array(), $helperOptions = array()) {
    if (! empty($inputOptions) && isset($helperOptions['options']))
      throw new Exception("Don't send \$inputOptions and \$helperOptions['options'] at the same time!");

    $defaultOptions = array('options' => $inputOptions);
    $options        = Portabilis_Array_Utils::merge($helperOptions, $defaultOptions);

    //foreach($helperOptions as $k => $v) {
    //  $options[$k] = $v;
    //}

    return $options;
  }
}
?>
