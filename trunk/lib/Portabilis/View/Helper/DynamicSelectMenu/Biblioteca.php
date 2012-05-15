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

  public function bibliotecaText($options = array()) {
    if (! isset($options['value']) || ! $options['value'])
      $bibliotecaId = $this->getBibliotecaId();
    else
      $bibliotecaId = $options['value'];

    $biblioteca = App_Model_IedFinder::getBiblioteca($bibliotecaId);
    $options['value'] = $biblioteca['nm_biblioteca'];

    $defaultOptions = array('id'    => 'biblioteca_nome',
                            'label' => 'Biblioteca',
                            'value' => '');

    $options = $this->mergeOptions($options, $defaultOptions);

    call_user_func_array(array($this->viewInstance, 'campoRotulo'), $options);

    /* adicionado campo oculto manualmente, pois o metodo campoRotulo adiciona
       como value o nome da biblioteca */
    $this->viewInstance->campoOculto("ref_cod_biblioteca", $bibliotecaId);
  }


  public function bibliotecaSelect($options = array(), $bibliotecas = array()) {
    if (empty($bibliotecas)) {
      $instituicaoId = $this->getPermissoes()->getInstituicao($this->viewInstance->getSession()->id_pessoa);
      $bibliotecas   = App_Model_IedFinder::getBibliotecas($instituicaoId);
    }

    $bibliotecas = $this->insertInArray(null, "Selecione uma biblioteca", $bibliotecas);

    $defaultOptions = array('id'           => 'ref_cod_biblioteca',
                            'label'        => 'Biblioteca',
                            'bibliotecas'  => $bibliotecas,
                            'value'        => null,
                            'callback'     => '',
                            'duplo'        => false,
                            'label_hint'   => '',
                            'input_hint'   => '',
                            'disabled'     => false,
                            'required'     => true,
                            'multiple'     => false);

    $options = $this->mergeOptions($options, $defaultOptions);
    call_user_func_array(array($this->viewInstance, 'campoLista'), $options);
  }


  public function biblioteca($options = array(), $bibliotecas = array()) {
    $nivelAcesso = $this->getPermissoes()->nivel_acesso($this->viewInstance->getSession()->id_pessoa);

    // poli-institucional, institucional
    $niveisAcessoMultiplasBibliotecas = array(1, 2);

    // escola, biblioteca
    $niveisAcessoBiblioteca = array(4, 8);

    if (in_array($nivelAcesso, $niveisAcessoMultiplasBibliotecas))
      $this->bibliotecaSelect($options, $bibliotecas);
    elseif(in_array($nivelAcesso, $niveisAcessoBiblioteca))
      $this->bibliotecaText($options);

    ApplicationHelper::loadJavascript($this->viewInstance, '/modules/DynamicSelectMenus/Assets/Javascripts/DynamicBibliotecas.js');
  }
}
?>
