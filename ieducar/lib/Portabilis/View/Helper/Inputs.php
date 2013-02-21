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
 * Portabilis_View_Helper_Inputs class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */


class Portabilis_View_Helper_Inputs {

  public function __construct($viewInstance) {
    $this->viewInstance = $viewInstance;
  }


  // dynamic inputs helper

  /* adiciona inputs de seleção dinamica ao formulário, recebendo diretamente as opcoes do input,
     sem necessidade de passar um array com um array de opções, ex:

     Ao invés de:
     $this->inputsHelper()->dynamic('instituicao', array('options' => array(required' => false)));

     Pode-se usar:
     $this->inputsHelper()->dynamic('instituicao', array(required' => false));

     Ou
     $this->inputsHelper()->dynamic('instituicao', array(), array('options' => array(required' => false)));

     Ou
     $this->inputsHelper()->dynamic(array('instituicao', 'escola', 'pesquisaAluno'));
  */

  public function dynamic($helperNames, $inputOptions = array(), $helperOptions = array()) {
    $options = $this->mergeInputOptions($inputOptions, $helperOptions);

    if (! is_array($helperNames))
      $helperNames = array($helperNames);

    foreach($helperNames as $helperName) {
      $helperClassName = "Portabilis_View_Helper_DynamicInput_" . ucfirst($helperName);
      $this->includeHelper($helperClassName);

      $helper = new $helperClassName($this->viewInstance, $this);
      $helper->$helperName($options);
    }
  }


  // input helpers

  public function input($helperName, $attrName, $inputOptions = array(), $helperOptions = array()) {
    $helperClassName = "Portabilis_View_Helper_Input_" . ucfirst($helperName);

    $this->includeHelper($helperClassName);
    $helper = new $helperClassName($this->viewInstance, $this);
    $helper->$helperName($attrName, $this->mergeInputOptions($inputOptions, $helperOptions));
  }

  public function text($attrNames, $inputOptions = array(), $helperOptions = array()) {
    if (! is_array($attrNames))
      $attrNames = array($attrNames);

    foreach($attrNames as $attrName) {
      $this->input('text', $attrName, $inputOptions, $helperOptions);
    }
  }

  public function numeric($attrName, $inputOptions = array(), $helperOptions = array()) {
    $this->input('numeric', $attrName, $inputOptions, $helperOptions);
  }

  public function integer($attrName, $inputOptions = array(), $helperOptions = array()) {
    $this->input('integer', $attrName, $inputOptions, $helperOptions);
  }

  public function select($attrName, $inputOptions = array(), $helperOptions = array()) {
    $this->input('select', $attrName, $inputOptions, $helperOptions);
  }

  public function search($attrName, $inputOptions = array(), $helperOptions = array()) {
    $this->input('search', $attrName, $inputOptions, $helperOptions);
  }

  public function hidden($attrName, $inputOptions = array(), $helperOptions = array()) {
    $this->input('hidden', $attrName, $inputOptions, $helperOptions);
  }

  public function checkbox($attrName, $inputOptions = array(), $helperOptions = array()) {
    $this->input('checkbox', $attrName, $inputOptions, $helperOptions);
  }

  public function date($attrName, $inputOptions = array(), $helperOptions = array()) {
    $this->input('date', $attrName, $inputOptions, $helperOptions);
  }

  public function textArea($attrName, $inputOptions = array(), $helperOptions = array()) {
    $this->input('textArea', $attrName, $inputOptions, $helperOptions);
  }


  // simple search input helper

  public function simpleSearch($objectName, $attrName, $inputOptions = array(), $helperOptions = array()) {
    $options = $this->mergeInputOptions($inputOptions, $helperOptions);

    $helperClassName = 'Portabilis_View_Helper_Input_SimpleSearch';
    $this->includeHelper($helperClassName);

    $helper = new $helperClassName($this->viewInstance, $this);
    $helper->simpleSearch($objectName, $attrName, $options);
  }


  // simple search resource input helper

  public function simpleSearchPessoa($attrName, $inputOptions = array(), $helperOptions = array()) {
    $this->simpleSearchResourceInput('simpleSearchPessoa', $attrName, $inputOptions, $helperOptions);
  }

  public function simpleSearchPais($attrName, $inputOptions = array(), $helperOptions = array()) {
    $this->simpleSearchResourceInput('simpleSearchPais', $attrName, $inputOptions, $helperOptions);
  }

  public function simpleSearchMunicipio($attrName, $inputOptions = array(), $helperOptions = array()) {
    $this->simpleSearchResourceInput('simpleSearchMunicipio', $attrName, $inputOptions, $helperOptions);
  }

  public function simpleSearchMatricula($attrName, $inputOptions = array(), $helperOptions = array()) {
    $this->simpleSearchResourceInput('simpleSearchMatricula', $attrName, $inputOptions, $helperOptions);
  }

  public function simpleSearchAluno($attrName, $inputOptions = array(), $helperOptions = array()) {
    $this->simpleSearchResourceInput('simpleSearchAluno', $attrName, $inputOptions, $helperOptions);
  }

  // multiple search resource input helper


  /*public function multipleSearchDeficienciasAjax($attrName, $inputOptions = array(), $helperOptions = array()) {
    $this->multipleSearchResourceInput('multipleSearchDeficiencias', $attrName, $inputOptions, $helperOptions);
  }*/

  public function multipleSearchDeficiencias($attrName, $inputOptions = array(), $helperOptions = array()) {
    $this->multipleSearchResourceInput('multipleSearchDeficiencias', $attrName, $inputOptions, $helperOptions);
  }

  // resource input helpers

  public function religiao($inputOptions = array(), $helperOptions = array()) {
    $this->resourceInput('religiao', $this->mergeInputOptions($inputOptions, $helperOptions));
  }

  public function beneficio($inputOptions = array(), $helperOptions = array()) {
    $this->resourceInput('beneficio', $this->mergeInputOptions($inputOptions, $helperOptions));
  }

  public function estadoCivil($inputOptions = array(), $helperOptions = array()) {
    $this->resourceInput('estadoCivil', $this->mergeInputOptions($inputOptions, $helperOptions));
  }

  public function turmaTurno($inputOptions = array(), $helperOptions = array()) {
    $this->resourceInput('turmaTurno', $this->mergeInputOptions($inputOptions, $helperOptions));
  }

  public function uf($inputOptions = array(), $helperOptions = array()) {
    $this->resourceInput('uf', $this->mergeInputOptions($inputOptions, $helperOptions));
  }

  public function tipoLogradouro($inputOptions = array(), $helperOptions = array()) {
    $this->resourceInput('tipoLogradouro', $this->mergeInputOptions($inputOptions, $helperOptions));
  }

  // protected methods

  protected function resourceInput($helperName, $options = array()) {
    $helperClassName = "Portabilis_View_Helper_Input_Resource_" . ucfirst($helperName);

    $this->includeHelper($helperClassName);
    $helper = new $helperClassName($this->viewInstance, $this);
    $helper->$helperName($options);
  }

  protected function simpleSearchResourceInput($helperName, $attrName, $inputOptions = array(), $helperOptions = array()) {
    $options = $this->mergeInputOptions($inputOptions, $helperOptions);

    $helperClassName = 'Portabilis_View_Helper_Input_Resource_' . ucfirst($helperName);
    $this->includeHelper($helperClassName);

    $helper = new $helperClassName($this->viewInstance, $this);
    $helper->$helperName($attrName, $options);
  }

  protected function multipleSearchResourceInput($helperName, $attrName, $inputOptions = array(), $helperOptions = array()) {
    $this->simpleSearchResourceInput($helperName, $attrName, $inputOptions, $helperOptions);
  }

  protected function includeHelper($helperClassName) {
    $classPath       = str_replace('_', '/', $helperClassName) . '.php';

    // usado include_once para continuar execução script mesmo que o path inexista.
    include_once $classPath;

    if (! class_exists($helperClassName))
      throw new CoreExt_Exception("Class '$helperClassName' not found in path '$classPath'");
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
