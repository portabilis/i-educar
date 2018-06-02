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
 * @since     11/2013
 * @version   $Id$
 */

require_once 'lib/Portabilis/View/Helper/Input/MultipleSearch.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/String/Utils.php';

/**
 * Portabilis_View_Helper_Input_MultipleSearchBeneficios class.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     11/2013
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_Input_Resource_MultipleSearchBeneficios extends Portabilis_View_Helper_Input_MultipleSearch {

  protected function getOptions($resources) {
    if (empty($resources)) {
      $resources = new clsPmieducarAlunoBeneficio();
      $resources = $resources->lista();
      $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'cod_aluno_beneficio', 'nm_beneficio');
    }

    return $this->insertOption(null, '', $resources);
  }

  public function multipleSearchBeneficios($attrName, $options = array()) {
    $defaultOptions = array('objectName'    => 'beneficios',
                            'apiController' => 'Beneficio',
                            'apiResource'   => 'beneficio-search');

    $options                         = $this->mergeOptions($options, $defaultOptions);
    $options['options']['resources'] = $this->getOptions($options['options']['resources']);

    //var_dump($options['options']['options']);

    $this->placeholderJs($options);

    parent::multipleSearch($options['objectName'], $attrName, $options);
  }

  protected function placeholderJs($options) {
    $optionsVarName = "multipleSearch" . Portabilis_String_Utils::camelize($options['objectName']) . "Options";
    $js             = "if (typeof $optionsVarName == 'undefined') { $optionsVarName = {} };
                       $optionsVarName.placeholder = 'Selecione os benefícios';";

    Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, $js, $afterReady = true);
  }
}
