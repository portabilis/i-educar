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

require_once 'lib/Portabilis/View/Helper/Input/MultipleSearch.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/String/Utils.php';

/**
 * Portabilis_View_Helper_Input_MultipleSearchDeficiencias class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_Input_Resource_MultipleSearchDeficiencias extends Portabilis_View_Helper_Input_MultipleSearch {

  /*protected function resourceValue($id) {
    if ($id) {
      $sql     = "select nm_deficiencia from cadastro.deficiencia where cod_deficiencia = $1";
      $options = array('params' => $id, 'return_only' => 'first-field');
      $nome    = Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);

      return Portabilis_String_Utils::toLatin1($nome, array('transform' => true, 'escape' => false));
    }
  }*/

  protected function getOptions($resources) {
    if (empty($resources)) {
      $resources = new clsCadastroDeficiencia();
      $resources = $resources->lista();
      $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'cod_deficiencia', 'nm_deficiencia');
    }

    return $this->insertOption(null, '', $resources);
  }

  public function multipleSearchDeficiencias($attrName, $options = array()) {
    $defaultOptions = array('objectName'    => 'deficiencias',
                            'apiController' => 'Deficiencia',
                            'apiResource'   => 'deficiencia-search');

    $options                         = $this->mergeOptions($options, $defaultOptions);
    $options['options']['resources'] = $this->getOptions($options['options']['resources']);

    //var_dump($options['options']['options']);

    $this->placeholderJs($options);

    parent::multipleSearch($options['objectName'], $attrName, $options);
  }

  protected function placeholderJs($options) {
    $optionsVarName = "multipleSearch" . Portabilis_String_Utils::camelize($options['objectName']) . "Options";
    $js             = "if (typeof $optionsVarName == 'undefined') { $optionsVarName = {} };
                       $optionsVarName.placeholder = safeUtf8Decode('Selecione as deficiências');";

    Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = true);
  }
}