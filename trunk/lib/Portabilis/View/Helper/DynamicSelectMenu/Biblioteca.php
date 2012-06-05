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
 * Portabilis_View_Helper_DynamicSelectMenu_Biblioteca class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_DynamicSelectMenu_Biblioteca extends Portabilis_View_Helper_DynamicSelectMenu_Core {

  public function stringInput($options = array()) {
    $defaultOptions       = array('options' => array());
    $options              = $this->mergeOptions($options, $defaultOptions);

    // subescreve $options['options']['value'] com nome escola
    if (isset($options['options']['value']) && $options['options']['value'])
      $bibliotecaId =  $options['options']['value'];
    else
      $bibliotecaId = $this->getBibliotecaId($options['id']);

    $biblioteca = App_Model_IedFinder::getBiblioteca($bibliotecaId);
    $options['options']['value'] = $biblioteca['nm_biblioteca'];

    $defaultInputOptions = array('id'        => 'biblioteca_nome',
                                 'label'     => 'Biblioteca',
                                 'value'     => '',
                                 'duplo'     => false,
                                 'descricao' => '',
                                 'separador' => ':');

    $inputOptions = $this->mergeOptions($options['options'], $defaultInputOptions);
    call_user_func_array(array($this->viewInstance, 'campoRotulo'), $inputOptions);

    /* adicionado campo oculto manualmente, pois o metodo campoRotulo adiciona
       como value o nome da biblioteca */
    $this->viewInstance->campoOculto("ref_cod_biblioteca", $bibliotecaId);
  }


  protected function getOptions($resources) {
    $instituicaoId = $this->getInstituicaoId();
    $escolaId      = $this->getEscolaId();

    if ($instituicaoId and $escolaId and empty($resources)) {
      // se possui id escola então filtra bibliotecas pelo id desta escola
      $resources = App_Model_IedFinder::getBibliotecas($instituicaoId, $escolaId);
    }

    return $this->insertInArray(null, "Selecione uma biblioteca", $resources);
  }


  public function selectInput($options = array()) {
    $defaultOptions       = array('id' => null, 'options' => array(), 'resources' => array());
    $options              = $this->mergeOptions($options, $defaultOptions);

    $defaultInputOptions = array('id'         => 'ref_cod_biblioteca',
                                 'label'      => 'Biblioteca',
                                 'resources'  => $this->getOptions($options['resources']),
                                 'value'      => $this->getBibliotecaId($options['id']),
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


  public function biblioteca($options = array()) {
    if ($this->hasNivelAcesso('POLI_INSTITUCIONAL') || $this->hasNivelAcesso('INSTITUCIONAL'))
      $this->selectInput($options);

    elseif($this->hasNivelAcesso('SOMENTE_ESCOLA') || $this->hasNivelAcesso('SOMENTE_BIBLIOTECA'))
      $this->stringInput($options);

    Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, '/modules/DynamicSelectMenus/Assets/Javascripts/DynamicBibliotecas.js');
  }
}
?>
