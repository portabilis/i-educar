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

    ApplicationHelper::loadJavascript($this->viewInstance, 'scripts/jquery/jquery.js');
    ApplicationHelper::embedJavascript($this->viewInstance, 'var $j = jQuery.noConflict();');

    $dependencies = array('/modules/Portabilis/Assets/Javascripts/ClientApi.js',
                          '/modules/Portabilis/DynamicSelectMenus/Assets/Javascripts/DynamicSelectMenus.js');

    ApplicationHelper::loadJavascript($this->viewInstance, $dependencies);
    ApplicationHelper::embedJavascript($this->viewInstance, 'fixupFieldsWidth();');
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
      //if (array_key_exists($key, $defaultOptions))
      $defaultOptions[$key] = $value;
    }

    return $defaultOptions;
  }

  /* Insere uma chave => valor no inicio de um array, preservando os indices
     inteiros do array */
  protected function insertInArray($key, $value, $array) {
    $newArray = array($key => $value);

    foreach($array as $key => $value) {
      $newArray[$key] = $value;
    }

    return $newArray;
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
    if (! isset($options['value']) || ! $options['value'])
      $options['value'] = $this->getPermissoes()->getInstituicao($this->viewInstance->getSession()->id_pessoa);

    $defaultOptions = array('id'    => 'ref_cod_instituicao',
                            'value' => null);

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
  public function instituicaoSelect($options = array(), $instituicoes = array()) {
    if (empty($instituicoes))
      $instituicoes = App_Model_IedFinder::getInstituicoes();

    $instituicoes = $this->insertInArray(null, "Selecione uma institui&ccedil;&atilde;o", $instituicoes);

    $defaultOptions = array('id'           => 'ref_cod_instituicao',
                            'label'        => 'Institui&ccedil;&atilde;o',
                            'instituicoes' => $instituicoes,
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
  public function instituicao($options = array(), $instituicoes = array()) {
    $nivelAcesso = $this->getPermissoes()->nivel_acesso($this->viewInstance->getSession()->id_pessoa);

    // poli-institucional
    $nivelAcessoMultiplasInstituicoes = 1;

    if ($nivelAcesso == $nivelAcessoMultiplasInstituicoes)
      $this->instituicaoSelect($options, $instituicoes);
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
    if (! isset($options['value']) || ! $options['value'])
      $escolaId = $this->getPermissoes()->getEscola($this->viewInstance->getSession()->id_pessoa);
    else
      $escolaId = $options['value'];

    $escola = App_Model_IedFinder::getEscola($escolaId);
    $options['value'] = $escola['nome'];

    $defaultOptions = array('id'        => 'escola_nome',
                            'label'     => 'Escola',
                            'value'     => '',
                            'duplo'     => false,
                            'descricao' => '',
                            'separador' => ':');

    $options = $this->mergeArrayWithDefaults($options, $defaultOptions);

    call_user_func_array(array($this->viewInstance, 'campoRotulo'), $options);

    /* adicionado campo oculto manualmente, pois o metodo campoRotulo adiciona
       como value o nome da escola */
    $this->viewInstance->campoOculto("ref_cod_escola", $escolaId);
  }


  /**
   *
   * <code>
   * </code>
   *
   * @param   type
   * @return  null
   */
  public function escolaSelect($options = array(), $escolas = array()) {
    if (empty($escolas)) {
      $instituicaoId = $this->getPermissoes()->getInstituicao($this->viewInstance->getSession()->id_pessoa);
      $escolas       = App_Model_IedFinder::getEscolas($instituicaoId);
    }

    $escolas = $this->insertInArray(null, "Selecione uma escola", $escolas);

    $defaultOptions = array('id'           => 'ref_cod_escola',
                            'label'        => 'Escola',
                            'escolas'      => $escolas,
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
  public function escola($options = array(), $escolas = array()) {
    $nivelAcesso = $this->getPermissoes()->nivel_acesso($this->viewInstance->getSession()->id_pessoa);

    // poli-institucional, institucional
    $niveisAcessoMultiplasEscolas = array(1, 2);

    // escola
    $niveisAcessoEscola = array(4, 8);

    if (in_array($nivelAcesso, $niveisAcessoMultiplasEscolas))
      $this->escolaSelect($options, $escolas);
    elseif (in_array($nivelAcesso, $niveisAcessoEscola))
      $this->escolaText($options);

    ApplicationHelper::loadJavascript($this->viewInstance, '/modules/Portabilis/DynamicSelectMenus/Assets/Javascripts/DynamicEscolas.js');
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
    if (! isset($options['value']) || ! $options['value']) {
      $biblioteca = $this->getPermissoes()->getBiblioteca($this->viewInstance->getSession()->id_pessoa);
      $bibliotecaId = $biblioteca[0]['ref_cod_biblioteca'];
    }
    else
      $bibliotecaId = $options['value'];

    $biblioteca = App_Model_IedFinder::getBiblioteca($bibliotecaId);
    $options['value'] = $biblioteca['nm_biblioteca'];

    $defaultOptions = array('id'    => 'biblioteca_nome',
                            'label' => 'Biblioteca',
                            'value' => '');

    $options = $this->mergeArrayWithDefaults($options, $defaultOptions);

    call_user_func_array(array($this->viewInstance, 'campoRotulo'), $options);

    /* adicionado campo oculto manualmente, pois o metodo campoRotulo adiciona
       como value o nome da biblioteca */
    $this->viewInstance->campoOculto("ref_cod_biblioteca", $bibliotecaId);
  }


  /**
   *
   * <code>
   * </code>
   *
   * @param   type
   * @return  null
   */
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

    ApplicationHelper::loadJavascript($this->viewInstance, '/modules/Portabilis/DynamicSelectMenus/Assets/Javascripts/DynamicBibliotecas.js');
  }


  /**
   *
   * <code>
   * </code>
   *
   * @param   type
   * @return  null
   */
  public function bibliotecaSituacao($bibliotecaId = 0, $options = array(), $situacoes = array()) {
    if (empty($situacoes)) {

      if (! $bibliotecaId) {
        $biblioteca = $this->getPermissoes()->getBiblioteca($this->viewInstance->getSession()->id_pessoa);
        // if (is_array($biblioteca) && count($biblioteca) > 0)
        $bibliotecaId = $biblioteca[0]['ref_cod_biblioteca'];
      }

      $situacoes = App_Model_IedFinder::getBibliotecaSituacoes($bibliotecaId);
    }

    $situacoes = $this->insertInArray(null, "Selecione uma situa&ccedil;&atilde;o", $situacoes);

    $defaultOptions = array('id'         => 'ref_cod_situacao',
                            'label'      => 'Situa&ccedil;&atilde;o',
                            'situacoes'  => $situacoes,
                            'value'      => null,
                            'callback'   => '',
                            'duplo'      => false,
                            'label_hint' => '',
                            'input_hint' => '',
                            'disabled'   => false,
                            'required'   => true,
                            'multiple'   => false);

    $options = $this->mergeArrayWithDefaults($options, $defaultOptions);
    call_user_func_array(array($this->viewInstance, 'campoLista'), $options);

    ApplicationHelper::loadJavascript($this->viewInstance, '/modules/Portabilis/DynamicSelectMenus/Assets/Javascripts/DynamicBibliotecaSituacoes.js');
  }


  /**
   *
   * <code>
   * </code>
   *
   * @param   type
   * @return  null
   */
  public function bibliotecaFonte($bibliotecaId = 0, $options = array(), $fontes = array()) {
    if (empty($fontes)) {

      if (! $bibliotecaId) {
        $biblioteca = $this->getPermissoes()->getBiblioteca($this->viewInstance->getSession()->id_pessoa);
        // if (is_array($biblioteca) && count($biblioteca) > 0)
        $bibliotecaId = $biblioteca[0]['ref_cod_biblioteca'];
      }

      $fontes = App_Model_IedFinder::getBibliotecaFontes($bibliotecaId);
    }

    $fontes = $this->insertInArray(null, "Selecione uma fonte", $fontes);

    $defaultOptions = array('id'         => 'ref_cod_fonte',
                            'label'      => 'Fonte',
                            'fontes'     => $fontes,
                            'value'      => null,
                            'callback'   => '',
                            'duplo'      => false,
                            'label_hint' => '',
                            'input_hint' => '',
                            'disabled'   => false,
                            'required'   => true,
                            'multiple'   => false);

    $options = $this->mergeArrayWithDefaults($options, $defaultOptions);
    call_user_func_array(array($this->viewInstance, 'campoLista'), $options);

    ApplicationHelper::loadJavascript($this->viewInstance, '/modules/Portabilis/DynamicSelectMenus/Assets/Javascripts/DynamicBibliotecaFontes.js');
  }


  /**
   *
   * <code>
   * </code>
   *
   * @param   type
   * @return  null
   */
  public function bibliotecaPesquisaCliente($clienteId, $options = array()) {
    $inputHint = "<img border='0' onclick='pesquisaCliente();' id='lupa_pesquisa_cliente' name='lupa_pesquisa_cliente' src='imagens/lupa.png' />";

    $defaultOptions = array('id'         => 'nome_cliente',
                            'label'      => 'Cliente',
                            'value'      => '',
                            'size'       => '30',
                            'maxLength'  => '255',
                            'required'   => false,
                            'expressao'  => false,
                            'duplo'      => false,
                            'label_hint' => '',
                            'input_hint' => $inputHint,
                            'callback'   => '',
                            'event'      => 'onKeyUp',
                            'disabled'   => true);


		//"nm_cliente1", "Cliente", $this->nm_cliente, 30, 255, false, false, false, "", "<img border=\"0\" onclick=\"pesquisa_cliente();\" id=\"ref_cod_cliente_lupa\" name=\"ref_cod_cliente_lupa\" src=\"imagens/lupa.png\"\/>","","",true

    $options = $this->mergeArrayWithDefaults($options, $defaultOptions);
    call_user_func_array(array($this->viewInstance, 'campoTexto'), $options);

    $this->viewInstance->campoOculto("ref_cod_cliente", $clienteId);

    ApplicationHelper::embedJavascript($this->viewInstance, "
      function pesquisaCliente() {
        if (validatesPresenseOfValueInRequiredFields()) {
	        var bibliotecaId = document.getElementById('ref_cod_biblioteca').value;
	        pesquisa_valores_popless('educar_pesquisa_cliente_lst.php?campo1=ref_cod_cliente&campo2=nome_cliente&ref_cod_biblioteca='+bibliotecaId);
        }
      }
");
  }
}
?>
