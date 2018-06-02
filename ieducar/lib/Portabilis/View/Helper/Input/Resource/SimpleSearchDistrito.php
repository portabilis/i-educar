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
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/View/Helper/Input/SimpleSearch.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/String/Utils.php';

/**
 * Portabilis_View_Helper_Input_SimpleSearchDistrito class.
 *
 * @author    Lucas Schmoeller da Silva <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão ?
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_Input_Resource_SimpleSearchDistrito extends Portabilis_View_Helper_Input_SimpleSearch {


  public function simpleSearchDistrito($attrName, $options = array()) {
    $defaultOptions = array('objectName'    => 'distrito',
                            'apiController' => 'Distrito',
                            'apiResource'   => 'distrito-search',
                            'showIdOnValue' => false);

    $options        = $this->mergeOptions($options, $defaultOptions);

    parent::simpleSearch($options['objectName'], $attrName, $options);
  }

  protected function resourceValue($id) {
    if ($id) {
      $sql       = "select nome from public.distrito where iddis = $1";
      $options   = array('params' => $id, 'return_only' => 'first-field');
      $distrito = $id. ' - '. Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

      return Portabilis_String_Utils::toLatin1($distrito, array('transform' => true, 'escape' => false));
    }
  }  

  protected function inputPlaceholder($inputOptions) {
    return 'Informe o nome do distrito';
  }
  
  protected function loadAssets() {
    $jsFile = '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/Resource/SimpleSearchDistrito.js';
    Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $jsFile);
  }
}