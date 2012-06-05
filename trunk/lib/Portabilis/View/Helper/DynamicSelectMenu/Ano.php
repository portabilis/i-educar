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

require_once 'lib/Portabilis/View/Helper/DynamicSelectMenu/Core.php';


/**
 * Portabilis_View_Helper_DynamicSelectMenu_Ano class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_DynamicSelectMenu_Ano extends Portabilis_View_Helper_DynamicSelectMenu_Core {

  # TODO criar este metodo na classe pai para ser subescrito nas (outras) classes filhas
  protected function getResourceValue($value = null) {
    if (! $value && $this->viewInstance->ano)
      $value = $this->viewInstance->ano;
    else
      $value = date('Y');

    return $value;
  }

  public function integerInput($options = array()) {
    $defaultOptions       = array('options' => array());
    $options              = $this->mergeOptions($options, $defaultOptions);

    $defaultInputOptions = array('id'             => 'ano',
                                 'label'          => 'Ano',
                                 'value'          => $this->getResourceValue($options['options']['value']),
                                 'tamanhovisivel' => 4,
                                 'tamanhomaximo'  => 4,
                                 'required'       => true,
                                 'label_hint'     => '',
                                 'input_hint'     => '',
                                 'script'         => false,
                                 'callback'       => false,
                                 'duplo'          => false,
                                 'disabled'       => false);

    $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);
    call_user_func_array(array($this->viewInstance, 'campoNumero'), $inputOptions);
  }


  # TODO implementar metodo para buscar anos escolares escola não encerrados
  protected function getOptions($resources) {
    $escolaId = $this->getescolaId();

    if ($escolaId and empty($resources)) {

      throw new CoreExt_Exception("Metódo getOptions classe 'Portabilis_View_Helper_DynamicSelectMenu_Ano' não implementado.");

      $resources = array('#TODO');
    }

    return $this->insertInArray(null, "Selecione um ano", $resources);
  }


  public function selectInput($options = array()) {
    $defaultOptions       = array('id' => null, 'options' => array(), 'resources' => array());
    $options              = $this->mergeOptions($options, $defaultOptions);

    $defaultInputOptions = array('id'         => 'ano',
                                 'label'      => 'Ano',
                                 'resources'  => $this->getOptions($options['resources']),
                                 'value'      => $this->getResourceValue($options['value']),
                                 'callback'   => '',
                                 'duplo'      => false,
                                 'label_hint' => '',
                                 'input_hint' => '',
                                 'disabled'   => false,
                                 'required'   => true,
                                 'multiple'   => false);

    $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);
    call_user_func_array(array($this->viewInstance, 'campoLista'), $inputOptions);
  }


  public function ano($options = array()) {
    if ((isset($options['inputType'])  && $options['inputType'] == 'select') ||
        (isset($options['input_type']) && $options['input_type'] == 'select'))
      $this->selectInput($options);

    else
      $this->integerInput($options);
  }
}
?>
