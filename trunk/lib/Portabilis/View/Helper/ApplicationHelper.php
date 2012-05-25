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

require_once 'CoreExt/View/Helper/Abstract.php';

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
class ApplicationHelper extends CoreExt_View_Helper_Abstract {

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
  public static function loadJavascript($viewInstance, $files, $expireCacheDateFormat = 'dmY') {
    if (! is_array($files))
      $files = array($files);

    if ($expireCacheDateFormat)
      $timestamp = '?timestamp=' . date($expireCacheDateFormat);
    else
      $timestamp = '';

    foreach ($files as $file) {
      $file .= $timestamp;
      $viewInstance->appendOutput("<script type='text/javascript' src='$file'></script>");
    }
  }

  /**
   *
   * <code>
   * </code>
   *
   * @param   type ?
   * @return  null
   */
  public static function embedJavascript($viewInstance, $script, $afterReady = false) {

    if ($afterReady) {

      $script = "(function($){
        $(document).ready(function(){
          $script
        });
      })(jQuery);";
    }

    $viewInstance->appendOutput("<script type='text/javascript'>$script</script>");
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
  public static function loadStylesheet($viewInstance, $files, $expireCacheDateFormat = 'dmY') {
    if (! is_array($files))
      $files = array($files);

    if ($expireCacheDateFormat)
      $timestamp = '?timestamp=' . date($expireCacheDateFormat);
    else
      $timestamp = '';

    foreach ($files as $file) {
      $file .= $timestamp;
      $viewInstance->appendOutput("<link type='text/css' rel='stylesheet' href='$file'></script>");
    }
  }
}
?>
