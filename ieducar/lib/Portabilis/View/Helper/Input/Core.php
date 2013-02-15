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

require_once 'include/pmieducar/clsPermissoes.inc.php';
require_once 'App/Model/IedFinder.php';
require_once 'lib/Portabilis/View/Helper/Application.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Object/Utils.php';
require_once 'lib/Portabilis/DataMapper/Utils.php';

/**
 * Portabilis_View_Helper_Input_Core class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_Input_Core {

  public function __construct($viewInstance, $inputsHelper) {
    $this->viewInstance  = $viewInstance;
    $this->_inputsHelper = $inputsHelper;

    $this->loadCoreAssets();
    $this->loadAssets();
  }


  protected function inputsHelper() {
    return $this->_inputsHelper;
  }


  protected function helperName() {
    return end(explode('_', get_class($this)));
  }


  protected function inputName() {
    return Portabilis_String_Utils::underscore($this->helperName());
  }


  protected function inputValue($value = null) {
    if (! $value && $this->viewInstance->{$this->inputName()})
      $value = $this->viewInstance->{$this->inputName()};

    return $value;
  }

  protected function inputPlaceholder($inputOptions) {
    return isset($inputOptions['placeholder']) ? $inputOptions['placeholder'] : $inputOptions['label'];
  }

  protected function fixupPlaceholder($inputOptions) {
    $id          = $inputOptions['id'];
    $placeholder = $this->inputPlaceholder($inputOptions);

    $script = "
      var \$input = \$j('#" . $id . "');
      if (\$input.is(':enabled'))
        \$input.attr('placeholder', '" . $placeholder . "');
    ";
    Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $script, $afterReady = true);
  }


  protected function loadCoreAssets() {
    // carrega estilo para feedback messages, devido algumas validações de inuts
    // adicionarem mensagens

    $style = "/modules/Portabilis/Assets/Stylesheets/Frontend.css";
    Portabilis_View_Helper_Application::loadStylesheet($this->viewInstance, $style);


    Portabilis_View_Helper_Application::loadJQueryLib($this->viewInstance);
    Portabilis_View_Helper_Application::loadJQueryUiLib($this->viewInstance);

    $dependencies = array('/modules/Portabilis/Assets/Javascripts/Utils.js',
                          '/modules/Portabilis/Assets/Javascripts/ClientApi.js',
                          '/modules/Portabilis/Assets/Javascripts/Validator.js');

    Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $dependencies);
  }


  protected function loadAssets() {
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    $style    = "/modules/DynamicInput/Assets/Stylesheets/{$this->helperName()}.css";
    $script   = "/modules/DynamicInput/Assets/Javascripts/{$this->helperName()}.js";

    if (file_exists($rootPath . $style))
      Portabilis_View_Helper_Application::loadStylesheet($this->viewInstance, $style);

    if (file_exists($rootPath . $script))
      Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $script);
  }

  // wrappers

  protected function getCurrentUserId() {
    return Portabilis_Utils_User::currentUserId();
  }

  protected function getPermissoes() {
    return Portabilis_Utils_User::getClsPermissoes();
  }

  protected function hasNivelAcesso($nivelAcessoType) {
    return Portabilis_Utils_User::hasNivelAcesso($nivelAcessoType);
  }

  protected function getDataMapperFor($packageName, $modelName){
    return Portabilis_DataMapper_Utils::getDataMapperFor($packageName, $modelName);
  }

  protected static function mergeOptions($options, $defaultOptions) {
    return Portabilis_Array_Utils::merge($options, $defaultOptions);
  }

  protected static function insertOption($key, $value, $array) {
    return Portabilis_Array_Utils::insertIn($key, $value, $array);
  }

  // ieducar helpers

  protected function getInstituicaoId($instituicaoId = null) {
    if (! $instituicaoId && is_numeric($this->viewInstance->ref_cod_instituicao))
      $instituicaoId = $this->viewInstance->ref_cod_instituicao;

    elseif (! $instituicaoId && is_numeric($this->viewInstance->ref_cod_escola)) {
      $escola        = App_Model_IedFinder::getEscola($this->viewInstance->ref_cod_escola);
      $instituicaoId = $escola['ref_cod_instituicao'];
    }

    elseif (! $instituicaoId && is_numeric($this->viewInstance->ref_cod_biblioteca)) {
      $biblioteca    = App_Model_IedFinder::getBiblioteca($this->viewInstance->ref_cod_biblioteca);
      $instituicaoId = $biblioteca['ref_cod_instituicao'];
    }

    elseif (! $instituicaoId)
      $instituicaoId = $this->getPermissoes()->getInstituicao($this->getCurrentUserId());

    return $instituicaoId;
  }


  protected function getEscolaId($escolaId = null) {
    if (! $escolaId && $this->viewInstance->ref_cod_escola)
      $escolaId = $this->viewInstance->ref_cod_escola;

    elseif (! $escolaId && is_numeric($this->viewInstance->ref_cod_biblioteca)) {
      $biblioteca    = App_Model_IedFinder::getBiblioteca($this->viewInstance->ref_cod_biblioteca);
      $escolaId = $biblioteca['ref_cod_escola'];
    }

    elseif (! $escolaId)
      $escolaId = $this->getPermissoes()->getEscola($this->getCurrentUserId());

    return $escolaId;
  }


  protected function getBibliotecaId($bibliotecaId = null) {
    if (! $bibliotecaId && ! $this->viewInstance->ref_cod_biblioteca) {
      $biblioteca = $this->getPermissoes()->getBiblioteca($this->getCurrentUserId());

      if (is_array($biblioteca) && count($biblioteca) > 0)
        $bibliotecaId = $biblioteca[0]['ref_cod_biblioteca'];
    }

    elseif (! $bibliotecaId)
      $bibliotecaId = $this->viewInstance->ref_cod_biblioteca;

    return $bibliotecaId;
  }

  protected function getCursoId($cursoId = null) {
    if (! $cursoId && $this->viewInstance->ref_cod_curso)
      $cursoId = $this->viewInstance->ref_cod_curso;

    return $cursoId;
  }

  protected function getSerieId($serieId = null) {
    if (! $serieId && $this->viewInstance->ref_cod_serie)
      $serieId = $this->viewInstance->ref_cod_serie;

    return $serieId;
  }
}