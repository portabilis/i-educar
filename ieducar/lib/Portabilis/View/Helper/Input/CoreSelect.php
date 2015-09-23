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

require_once 'lib/Portabilis/View/Helper/Input/Core.php';

/**
 * Portabilis_View_Helper_DynamicInput_CoreSelect class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_Input_CoreSelect extends Portabilis_View_Helper_Input_Core {

  protected function inputName() {
    return parent::inputName() . '_id';
  }

  public function select($options = array()) {
    // this helper options
    $defaultOptions = array('id'         => null,
                            'objectName' => '',
                            'attrName'   => $this->inputName(),
                            'resources'  => array(),
                            'options'    => array());

    $defaultOptions            = $this->mergeOptions($this->defaultOptions(), $defaultOptions);
    $this->options             = $this->mergeOptions($options, $defaultOptions);
    $this->options['options']  = $this->mergeOptions($this->options['options'], $defaultOptions['options']);

    // select options

    $defaultInputOptions = array('label'     => Portabilis_String_Utils::humanize($this->inputName()),
                                 'value'     => $this->inputValue($this->options['id']),
                                 'resources' => $this->inputOptions($this->options));

    $inputOptions  = $this->mergeOptions($this->options['options'], $defaultInputOptions);
    $helperOptions = array('objectName' => $this->options['objectName']);

    // input
    $this->inputsHelper()->select($this->options['attrName'], $inputOptions, $helperOptions);
  }

  // subscrever no child caso deseje carregar mais opções do banco de dados antes de carregar a página,
  // ou deixar apenas com a opção padrão e carregar via ajax
  protected function inputOptions($options) {
    return $this->insertOption(null,
                               "Selecione um(a) " . Portabilis_String_Utils::humanize($this->inputName()),
                               $resources);
  }

  // overwrite this method in childrens to set additional default options, to be merged with received options,
  // and pass to select helper
  protected function defaultOptions() {
    return array();
  }
}