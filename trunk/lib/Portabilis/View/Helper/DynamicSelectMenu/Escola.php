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
 * Portabilis_View_Helper_DynamicSelectMenu_Escola class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_DynamicSelectMenu_Escola extends Portabilis_View_Helper_DynamicSelectMenu_Core {

  public function stringInput($options = array()) {
    $defaultOptions       = array('options' => array());
    $options              = $this->mergeOptions($options, $defaultOptions);

    // subescreve $options['options']['value'] com nome escola
    if (isset($options['options']['value']) && $options['options']['value'])
      $escolaId =  $options['options']['value'];
    else
      $escolaId = $this->getEscolaId($options['id']);

    $escola   = App_Model_IedFinder::getEscola($escolaId);
    $options['options']['value'] = $escola['nome'];

    $defaultInputOptions = array('id'        => 'escola_nome',
                                 'label'     => 'Escola',
                                 'value'     => '',
                                 'duplo'     => false,
                                 'descricao' => '',
                                 'separador' => ':');

    $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);
    call_user_func_array(array($this->viewInstance, 'campoRotulo'), $inputOptions);

    /* adicionado campo oculto manualmente, pois o metodo campoRotulo adiciona
       como value o nome da escola */
    $this->viewInstance->campoOculto("ref_cod_escola", $escolaId);
  }


  protected function getOptions($resources) {
    if (empty($resources))
      $resources = App_Model_IedFinder::getEscolas($this->getInstituicaoId());

    return $this->insertInArray(null, "Selecione uma escola", $resources);
  }


  public function selectInput($options = array()) {
    $defaultOptions       = array('id' => null, 'options' => array(), 'resources' => array());
    $options              = $this->mergeOptions($options, $defaultOptions);

    $defaultInputOptions = array('id'         => 'ref_cod_escola',
                                 'label'      => 'Escola',
                                 'resources'  => $this->getOptions($options['resources']),
                                 'value'      => $this->getEscolaId($options['id']),
                                 'callback'   => '',
                                 'duplo'      => false,
                                 'label_hint' => '',
                                 'input_hint' => '',
                                 'disabled'   => false,
                                 'required'   => true,
                                 'multiple'   => false);

    $inputOptions = $this->mergeOptions($inputOptions, $defaultInputOptions);
    call_user_func_array(array($this->viewInstance, 'campoLista'), $inputOptions);
  }


  public function escola($options = array(), $escolas = array()) {
    if ($this->hasNivelAcesso('POLI_INSTITUCIONAL') || $this->hasNivelAcesso('INSTITUCIONAL'))
      $this->selectInput($options);

    elseif($this->hasNivelAcesso('SOMENTE_ESCOLA') || $this->hasNivelAcesso('SOMENTE_BIBLIOTECA'))
      $this->stringInput($options);

    ApplicationHelper::loadJavascript($this->viewInstance, '/modules/DynamicSelectMenus/Assets/Javascripts/DynamicEscolas.js');
  }
}
?>
