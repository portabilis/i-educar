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

require_once 'CoreExt/View/Helper/Abstract.php';
require_once 'include/pmieducar/clsPermissoes.inc.php';
require_once 'App/Model/IedFinder.php';
require_once 'lib/Portabilis/View/Helper/Application.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/Object/Utils.php';
require_once 'lib/Portabilis/DataMapper/Utils.php';

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
class Portabilis_View_Helper_DynamicSelectMenu_Core {

  public function __construct($viewInstance) {
    $this->viewInstance = $viewInstance;

    Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, 'scripts/jquery/jquery.js');
    Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, 'var $j = jQuery.noConflict();');

    $dependencies = array('/modules/Portabilis/Assets/Javascripts/Utils.js',
                          '/modules/Portabilis/Assets/Javascripts/ClientApi.js',
                          '/modules/Portabilis/Assets/Javascripts/Validator.js',
                          '/modules/DynamicSelectMenus/Assets/Javascripts/DynamicSelectMenus.js');

    Portabilis_View_Helper_Application::loadJavascript($this->viewInstance, $dependencies);
    Portabilis_View_Helper_Application::embedJavascript($this->viewInstance, 'fixupFieldsWidth();');
  }

  protected function getCurrentUserId() {
    if (! isset($this->_currentUserId))
      $this->_currentUserId = $this->viewInstance->getSession()->id_pessoa;

    return $this->_currentUserId;
  }


  protected function getPermissoes() {
    if (! isset($this->_permissoes))
      $this->_permissoes = new clsPermissoes();

    return $this->_permissoes;
  }


  protected function getNivelAcesso() {
    if (! isset($this->_nivelAcesso))
      $this->_nivelAcesso = $this->getPermissoes()->nivel_acesso($this->getCurrentUserId());

    return $this->_nivelAcesso;
  }


  # TODO verificar se é possivel usar a logica de App_Model_NivelAcesso
  protected function hasNivelAcesso($nivelAcessoType) {
    $niveisAcesso = array('POLI_INSTITUCIONAL' => 1,
                          'INSTITUCIONAL'      => 2,
                          'SOMENTE_ESCOLA'     => 4,
                          'SOMENTE_BIBLIOTECA' => 8);

    if (! isset($niveisAcesso[$nivelAcessoType]))
      throw new CoreExt_Exception("Nivel acesso '$nivelAcessoType' not defined.");

    return $this->getNivelAcesso() == $niveisAcesso[$nivelAcessoType];
  }


  protected function getDataMapperFor($packageName, $modelName){
    return Portabilis_DataMapper_Utils::getDataMapperFor($packageName, $modelName);
  }


  // wrapper for Portabilis_Array_Utils::merge
  protected static function mergeOptions($options, $defaultOptions) {
    return Portabilis_Array_Utils::merge($options, $defaultOptions);
  }


  // wrapper for Portabilis_Array_Utils::insertIn
  // TODO renomear para insertOption
  protected static function insertInArray($key, $value, $array) {
    return Portabilis_Array_Utils::insertIn($key, $value, $array);
  }


  protected function getInstituicaoId($instituicaoId = null) {
    if (! $instituicaoId && is_numeric($this->viewInstance->ref_cod_instituicao))
      $instituicaoId = $this->viewInstance->ref_cod_instituicao;

    elseif (! $instituicaoId && is_numeric($this->viewInstance->ref_cod_escola)) {
      $escola        = App_Model_IedFinder::getEscola($this->viewInstance->ref_cod_escola);
      $instituicaoId = $escola['ref_cod_instituicao'];
    }

    elseif (! $instituicaoId && is_numeric($this->viewInstance->ref_cod_biblioteca)) {
      $biblioteca    = App_Model_IedFinder::getBiblioteca($this->viewInstance->ref_cod_biblioteca);
      $instituicaoId = $biblioteca['ref_cod_instituicao'];
    }

    elseif (! $instituicaoId)
      $instituicaoId = $this->getPermissoes()->getInstituicao($this->getCurrentUserId());

    return $instituicaoId;
  }


  protected function getEscolaId($escolaId = null) {
    if (! $escolaId && $this->viewInstance->ref_cod_escola)
      $escolaId = $this->viewInstance->ref_cod_escola;

    elseif (! $escolaId && is_numeric($this->viewInstance->ref_cod_biblioteca)) {
      $biblioteca    = App_Model_IedFinder::getBiblioteca($this->viewInstance->ref_cod_biblioteca);
      $escolaId = $biblioteca['ref_cod_escola'];
    }

    elseif (! $escolaId)
      $escolaId = $this->getPermissoes()->getEscola($this->getCurrentUserId());

    return $escolaId;
  }


  protected function getBibliotecaId($bibliotecaId = null) {
    if (! $bibliotecaId && ! $this->viewInstance->ref_cod_biblioteca) {
      $biblioteca = $this->getPermissoes()->getBiblioteca($this->getCurrentUserId());

      if (is_array($biblioteca) && count($biblioteca) > 0)
        $bibliotecaId = $biblioteca[0]['ref_cod_biblioteca'];
    }

    elseif (! $bibliotecaId)
      $bibliotecaId = $this->viewInstance->ref_cod_biblioteca;

    return $bibliotecaId;
  }

  protected function getCursoId($cursoId = null) {
    if (! $cursoId && $this->viewInstance->ref_cod_curso)
      $cursoId = $this->viewInstance->ref_cod_curso;

    return $cursoId;
  }

  protected function getSerieId($serieId = null) {
    if (! $serieId && $this->viewInstance->ref_cod_serie)
      $serieId = $this->viewInstance->ref_cod_serie;

    return $serieId;
  }
}
?>
