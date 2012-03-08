<?php

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

require_once 'CoreExt/View/Helper/Abstract.php';
require_once 'include/pmieducar/clsPermissoes.inc.php';
require_once 'App/Model/IedFinder.php';
require_once 'lib/Portabilis/View/Helper/ApplicationHelper.php';
// require_once 'App/Model/NivelAcesso.php';
// require_once 'Usuario/Model/UsuarioDataMapper.php';

/**
 * SelectMenusHelper class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class DynamicSelectMenusHelper {

  public function __construct($viewInstance) {
    $this->viewInstance = $viewInstance;
    $dependencies = array('scripts/jquery/jquery.js',
                          '/modules/Portabilis/DynamicSelectMenus/Assets/Javascripts/DynamicSelectMenus.js');

    ApplicationHelper::javascript($this->viewInstance, $dependencies);
  }


  protected function getPermissoes() {
    if (! isset($this->permissoes))
      $this->permissoes = new clsPermissoes();

    return $this->permissoes;
  }


  protected function getDataMapperFor($dataMapper) {
    if (is_string($dataMapper)) {
      if (class_exists($dataMapper))
        $dataMapper = new $dataMapper();
      else
        throw new Core_Controller_Page_Exception('A classe "'. $dataMapper .'" não existe.');
    }
    elseif ($dataMapper instanceof CoreExt_DataMapper) {
      $dataMapper = $dataMapper;
    }
    else {
      throw new CoreExt_Exception_InvalidArgumentException('Argumento inválido. São aceitos apenas argumentos do tipo string e CoreExt_DataMapper');
    }

    return $dataMapper;
  }

  protected function mergeArrayWithDefaults($options, $defaultOptions) {
    foreach($options as $key => $value) {
      if (array_key_exists($key, $defaultOptions))
        $defaultOptions[$key] = $value;
    }

    return $defaultOptions;
  }

  /**
   *
   * <code>
   * </code>
   *
   * @param   type
   * @return  null
   */
  public function instituicaoHidden($options = array()) {
    if (! array_key_exists('instituicaoId', $options))
      $instituicaoId = $this->getPermissoes()->getInstituicao($this->viewInstance->getSession()->id_pessoa);

    $defaultOptions = array('id'    => 'ref_cod_instituicao',
                            'value' => isset($instituicaoId) ? $instituicaoId : null);

    $options = $this->mergeArrayWithDefaults($options, $defaultOptions);
    call_user_func_array(array($this->viewInstance, 'campoOculto'), $options);
  }


  /**
   *
   * <code>
   * </code>
   *
   * @param   type
   * @return  null
   */
  public function instituicaoSelect($options = array()) {

    // TODO obter instituicoes conforme permissoes / tipo usuário
    if (! array_key_exists('instituicoes', $options)) {
      $instituicoes       = App_Model_IedFinder::getInstituicoes();

      // TODO deve ser a primeira opcao
      $instituicoes[null] = "Selecione uma institui&ccedil;&atilde;o";
    }

    $defaultOptions = array('id'           => 'ref_cod_instituicao',
                            'label'        => 'Institui&ccedil;&atilde;o',
                            'instituicoes' => isset($instituicoes) ? $instituicoes : array(),
                            'value'        => null,
                            'callback'     => '',
                            'duplo'        => false,
                            'label_hint'   => '',
                            'input_hint'   => '',
                            'disabled'     => false,
                            'required'     => true,
                            'multiple'     => false);

    $options = $this->mergeArrayWithDefaults($options, $defaultOptions);
    call_user_func_array(array($this->viewInstance, 'campoLista'), $options);
  }


  /**
   *
   * <code>
   * </code>
   *
   * @param   type
   * @return  null
   */
  public function instituicao($options = array()) {
    $nivelAcesso = $this->getPermissoes()->nivel_acesso($this->viewInstance->getSession()->id_pessoa);

    // poli-institucional
    $nivelAcessoMultiplasInstituicoes = 1;

    if ($nivelAcesso == $nivelAcessoMultiplasInstituicoes)
      $this->instituicaoSelect($options);
    else
      $this->instituicaoHidden($options);
  }


  /**
   *
   * <code>
   * </code>
   *
   * @param   type
   * @return  null
   */
  public function escolaText($options = array()) {
    if (! array_key_exists('value', $options)) {
      $escolaId = $this->getPermissoes()->getEscola($this->viewInstance->getSession()->id_pessoa);
      $nomeEscola = App_Model_IedFinder::getEscola($escolaId);
      $nomeEscola = $nomeEscola['nm_escola'];
    }

    $defaultOptions = array('id'    => 'ref_cod_escola',
                            'label'        => 'Escola',
                            'value' => isset($nomeEscola) ? $nomeEscola : '');

    $options = $this->mergeArrayWithDefaults($options, $defaultOptions);
    call_user_func_array(array($this->viewInstance, 'campoRotulo'), $options);

    #TODO incluir escolaHidden
  }


  /**
   *
   * <code>
   * </code>
   *
   * @param   type
   * @return  null
   */
  public function escolaSelect($options = array()) {

    // TODO obter escolas conforme permissoes / tipo usuário
    if (! array_key_exists('escolas', $options)) {
      $instituicaoId = $this->getPermissoes()->getInstituicao($this->viewInstance->getSession()->id_pessoa);
      $escolas       = App_Model_IedFinder::getEscolas($instituicaoId);

      // TODO deve ser a primeira opcao, criar funcao para usar abaixo, getSelectFor...?
      $escolas[null] = "Selecione uma escola";
    }

    $defaultOptions = array('id'           => 'ref_cod_escola',
                            'label'        => 'Escola',
                            'escolas'      => isset($escolas) ? $escolas : array(),
                            'value'        => null,
                            'callback'     => '',
                            'duplo'        => false,
                            'label_hint'   => '',
                            'input_hint'   => '',
                            'disabled'     => false,
                            'required'     => true,
                            'multiple'     => false);

    $options = $this->mergeArrayWithDefaults($options, $defaultOptions);
    call_user_func_array(array($this->viewInstance, 'campoLista'), $options);
  }


  /**
   *
   * <code>
   * </code>
   *
   * @param   type
   * @return  null
   */
  public function escola($options = array()) {
    $nivelAcesso = $this->getPermissoes()->nivel_acesso($this->viewInstance->getSession()->id_pessoa);

    // poli-institucional, institucional
    $niveisAcessoMultiplasEscolas = array(1, 2);

    // escola
    $niveisAcessoEscola = array(4);

    if (in_array($nivelAcesso, $niveisAcessoMultiplasEscolas))
      $this->escolaSelect($options);
    elseif (in_array($nivelAcesso, $niveisAcessoEscola))
      $this->escolaText($options);

    ApplicationHelper::javascript($this->viewInstance, '/modules/Portabilis/DynamicSelectMenus/Assets/Javascripts/DynamicEscolas.js');
  }


  /**
   *
   * <code>
   * </code>
   *
   * @param   type
   * @return  null
   */
  public function bibliotecaText($options = array()) {
    if (! array_key_exists('value', $options)) {
      $bibliotecaId = $this->getPermissoes()->getBiblioteca($this->viewInstance->getSession()->id_pessoa);
      $nomeBiblioteca = App_Model_IedFinder::getBiblioteca($bibliotecaId);
      $nomeBiblioteca = $nomeEscola['nm_biblioteca'];
    }

    $defaultOptions = array('id'    => 'ref_cod_biblioteca',
                            'label' => 'Biblioteca',
                            'value' => isset($nomeBiblioteca) ? $nomeBiblioteca : '');

    $options = $this->mergeArrayWithDefaults($options, $defaultOptions);
    call_user_func_array(array($this->viewInstance, 'campoRotulo'), $options);

    #TODO incluir bibliotecaHidden
  }


  /**
   *
   * <code>
   * </code>
   *
   * @param   type
   * @return  null
   */
  public function bibliotecaSelect($options = array()) {

    // TODO obter bibliotecas conforme permissoes / tipo usuário
    if (! array_key_exists('bibliotecas', $options)) {
      $instituicaoId = $this->getPermissoes()->getInstituicao($this->viewInstance->getSession()->id_pessoa);
      $bibliotecas   = App_Model_IedFinder::getBibliotecas($instituicaoId);

      // TODO deve ser a primeira opcao, criar funcao para usar abaixo, getSelectFor...?
      $bibliotecas[null] = "Selecione uma biblioteca";
    }

    $defaultOptions = array('id'           => 'ref_cod_biblioteca',
                            'label'        => 'Biblioteca',
                            'bibliotecas'  => isset($bibliotecas) ? $bibliotecas : array(),
                            'value'        => null,
                            'callback'     => '',
                            'duplo'        => false,
                            'label_hint'   => '',
                            'input_hint'   => '',
                            'disabled'     => false,
                            'required'     => true,
                            'multiple'     => false);

    $options = $this->mergeArrayWithDefaults($options, $defaultOptions);
    call_user_func_array(array($this->viewInstance, 'campoLista'), $options);
  }


  /**
   *
   * <code>
   * </code>
   *
   * @param   type
   * @return  null
   */
  public function biblioteca($options = array()) {
    $nivelAcesso = $this->getPermissoes()->nivel_acesso($this->viewInstance->getSession()->id_pessoa);

    // poli-institucional, institucional
    $niveisAcessoMultiplasBibliotecas = array(1, 2);

    // escola, biblioteca
    $niveisAcessoBiblioteca = array(4, 8);

    if (in_array($nivelAcesso, $niveisAcessoMultiplasBibliotecas))
      $this->bibliotecaSelect($options);
    elseif(in_array($nivelAcesso, $niveisAcessoBiblioteca))
      $this->bibliotecaText($options);
  }
}
?>
