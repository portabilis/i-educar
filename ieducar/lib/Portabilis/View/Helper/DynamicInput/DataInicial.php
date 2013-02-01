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

require_once 'lib/Portabilis/View/Helper/DynamicInput/Core.php';


/**
 * Portabilis_View_Helper_DynamicInput_DataInicial class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_DynamicInput_DataInicial extends Portabilis_View_Helper_DynamicInput_Core {
  protected function inputValue($value = null) {
    if (! $value && $this->viewInstance->data_inicial)
      $value = $this->viewInstance->data_inicial;
    else
      $value = date('01/m/Y');

    return $value;
  }

  public function dataInicial($options = array()) {
    $defaultOptions       = array('options' => array());
    $options              = $this->mergeOptions($options, $defaultOptions);

    $defaultInputOptions = array('id'         => 'data_inicial',
                                 'label'      => 'Data inicial',
                                 'value'      => $this->inputValue($options['options']['value']),
                                 'required'   => true,
                                 'label_hint' => '',
                                 'inline'     => false,
                                 'callback'   => false,
                                 'disabled'   => false);

    $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);
    call_user_func_array(array($this->viewInstance, 'campoData'), $inputOptions);
  }
}