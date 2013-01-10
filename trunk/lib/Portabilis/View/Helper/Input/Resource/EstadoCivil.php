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

require_once 'lib/Portabilis/View/Helper/Input/Select.php';


/**
 * Portabilis_View_Helper_Input_Select class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_Input_Resource_EstadoCivil extends Portabilis_View_Helper_Input_Select {

  protected function getResourceId($id = null) {
    return $id;
  }

  protected function getOptions($resources) {
    if (empty($resources)) {
      $resources = array();

      $_resources = new clsEstadoCivil();
      $_resources = $_resources->lista();

      foreach ($_resources as $resource) {
        $resources[$resource['ideciv']] = $resource['descricao'];
      }
    }

    return $this->insertOption(null, "Selecione", $resources);
  }

  public function estadoCivil($options = array()) {
    // options
    $defaultOptions      = array('objectName' => '',
                                 'attrName'   => 'estado_civil_id',
                                 'resources'  => array(),
                                 'options'    => array());

    $options             = $this->mergeOptions($options, $defaultOptions);

    $defaultInputOptions = array('label' => 'Estado civil', 'value' => $this->viewInstance->{$options['attrName']});
    $options['options']  = $this->mergeOptions($options['options'], $defaultInputOptions);

    // text input

    $defaultInputOptions = array('value'     => $this->getResourceId($options['id']),
                                 'resources' => $this->getOptions($options['resources']));

    $textInputOptions    = $this->mergeOptions($options['options'], $defaultInputOptions);
    $textHelperOptions   = array('objectName' => $options['objectName']);

    $this->inputsHelper()->select($options['attrName'], $textInputOptions, $textHelperOptions);
  }
}