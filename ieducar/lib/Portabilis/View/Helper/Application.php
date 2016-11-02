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

require_once 'CoreExt/View/Helper/Abstract.php';
require_once 'Portabilis/Assets/Version.php';

/**
 * ApplicationHelper class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */

class Portabilis_View_Helper_Application extends CoreExt_View_Helper_Abstract {

  // previne carregar mais de uma vez o mesmo asset js ou css
  protected static $javascriptsLoaded = array();
  protected static $stylesheetsLoaded = array();


  /**
   * Construtor singleton.
   */
  protected function __construct()
  {
  }


  /**
   * Retorna uma instância singleton.
   * @return CoreExt_View_Helper_Abstract
   */
  public static function getInstance()
  {
    return self::_getInstance(__CLASS__);
  }



  /**
   * Adiciona elementos chamadas scripts javascript para instancia da view recebida, exemplo:
   *
   * <code>
   * $applicationHelper->javascript($viewInstance, array('/modules/ModuleName/Assets/Javascripts/ScriptName.js', '...'));
   * </code>
   *
   * @param   object   $viewInstance  Istancia da view a ser carregado os scripts.
   * @param   array ou string  $files  Lista de scripts a serem carregados.
   * @return  null
   */
  public static function loadJavascript($viewInstance, $files, $appendAssetsVersionParam = true) {
    if (! is_array($files))
      $files = array($files);

    foreach ($files as $file) {
      // somente carrega o asset uma vez
      if (! in_array($file, self::$javascriptsLoaded)) {
        self::$javascriptsLoaded[] = $file;

        if ($appendAssetsVersionParam)
          $file .= '?assets_version=' . Portabilis_Assets_Version::VERSION;

        $viewInstance->appendOutput("<script type='text/javascript' src='$file'></script>");
      }
    }
  }


  /**
   * Adiciona links css para instancia da view recebida, exemplo:
   *
   * <code>
   * $applicationHelper->stylesheet($viewInstance, array('/modules/ModuleName/Assets/Stylesheets/StyleName.css', '...'));
   * </code>
   *
   * @param   object   $viewInstance1  Istancia da view a ser adicionado os links para os estilos.
   * @param   array ou string  $files  Lista de estilos a serem carregados.
   * @return  null
   */
  public static function loadStylesheet($viewInstance, $files, $appendAssetsVersionParam = true) {
    if (! is_array($files))
      $files = array($files);

    foreach ($files as $file) {
      // somente carrega o asset uma vez
      if (! in_array($file, self::$stylesheetsLoaded)) {
        self::$stylesheetsLoaded[] = $file;

        if ($appendAssetsVersionParam)
          $file .= '?assets_version=' . Portabilis_Assets_Version::VERSION;

        $viewInstance->appendOutput("<link type='text/css' rel='stylesheet' href='$file'></script>");
      }
    }
  }


  public static function embedJavascript($viewInstance, $script, $afterReady = false) {
    if ($afterReady) {
      self::loadJQueryLib($viewInstance);

      $script = "(function($){
        $(document).ready(function(){
          $script
        });
      })(jQuery);";
    }

    $viewInstance->appendOutput("<script type='text/javascript'>$script</script>");
  }


  public static function embedStylesheet($viewInstance, $css) {
    $viewInstance->appendOutput("<style type='text/css'>$css</style>");
  }

  public static function embedJavascriptToFixupFieldsWidth($viewInstance) {
    Portabilis_View_Helper_Application::loadJQueryLib($viewInstance);

    Portabilis_View_Helper_Application::loadJavascript(
      $viewInstance, '/modules/Portabilis/Assets/Javascripts/Utils.js'
    );

    Portabilis_View_Helper_Application::embedJavascript(
      $viewInstance, 'fixupFieldsWidth();', $afterReady = true
    );

  }

  // load lib helpers

  public static function loadJQueryLib($viewInstance) {
    self::loadJavascript($viewInstance, '//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js', false);
    self::embedJavascript($viewInstance, "if (typeof(\$j) == 'undefined') { var \$j = jQuery.noConflict(); }");
  }


  public static function loadJQueryFormLib($viewInstance) {
    self::loadJavascript($viewInstance, 'scripts/jquery/jquery.form.js', false);
  }


  public static function loadJQueryUiLib($viewInstance) {
    self::loadJavascript($viewInstance, '//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js', false);
    self::loadStylesheet($viewInstance, '//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/ui-lightness/jquery-ui.css', false);

    // ui-autocomplete fixup
    self::embedStylesheet($viewInstance, ".ui-autocomplete { font-size: 11px; }");
  }

  public static function loadChosenLib($viewInstance) {
    self::loadStylesheet($viewInstance, '/modules/Portabilis/Assets/Plugins/Chosen/chosen.css', false);
    self::loadJavascript($viewInstance, '/modules/Portabilis/Assets/Plugins/Chosen/chosen.jquery.min.js', false);
  }

  public static function loadAjaxChosenLib($viewInstance) {
    // AjaxChosen requires this fixup, see https://github.com/meltingice/ajax-chosen
    $fixupCss = ".chzn-container .chzn-results .group-result { display: list-item; }";
    Portabilis_View_Helper_Application::embedStylesheet($viewInstance, $fixupCss);

    self::loadJavascript($viewInstance, '/modules/Portabilis/Assets/Plugins/AjaxChosen/ajax-chosen.min.js', false);
  }
}
