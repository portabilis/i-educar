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

require_once 'lib/Portabilis/View/Helper/DynamicInput/CoreSelect.php';


/**
 * Portabilis_View_Helper_DynamicInput_Instituicao class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_DynamicInput_Instituicao extends Portabilis_View_Helper_DynamicInput_CoreSelect {

  protected function inputValue($value = null) {
    return $this->getInstituicaoId($value);
  }

  protected function inputName() {
    return 'ref_cod_instituicao';
  }

  protected function inputOptions($options) {
    $resources     = $options['resources'];

    if (empty($resources))
      $resources = App_Model_IedFinder::getInstituicoes();

    return $this->insertOption(null, "Selecione uma institui&ccedil;&atilde;o", $resources);
  }


  protected function defaultOptions(){
    return array('options' => array('label' => 'Institui&ccedil;&atilde;o'));
  }


  public function selectInput($options = array()) {
    parent::select($options);
  }


  public function hiddenInput($options = array()) {
    $defaultOptions       = array('id' => null, 'options' => array());
    $options              = $this->mergeOptions($options, $defaultOptions);

    $defaultInputOptions = array('id'    => 'ref_cod_instituicao',
                                 'value' => $this->getInstituicaoId($options['id']));

    $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);
    call_user_func_array(array($this->viewInstance, 'campoOculto'), $inputOptions);
  }


  public function instituicao($options = array()) {
    if ($this->hasNivelAcesso('POLI_INSTITUCIONAL'))
      $this->selectInput($options);
    else
      $this->hiddenInput($options);
  }
}