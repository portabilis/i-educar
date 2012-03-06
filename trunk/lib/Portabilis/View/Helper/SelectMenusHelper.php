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
//require_once 'Usuario/Model/UsuarioDataMapper.php';

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
class SelectMenusHelper extends CoreExt_View_Helper_Abstract {

  /**
   * Construtor singleton.
   */
  protected function __construct() {
  }

  /**
   * Retorna uma instância singleton.
   * @return CoreExt_View_Helper_Abstract
   */
  public static function getInstance() {
    return self::_getInstance(__CLASS__);
  }


  protected static function getPermissoes() {
    $instance = self::getInstance();
    if (! isset($instance->permissoes))
      $instance->permissoes = new clsPermissoes();

    return $instance->permissoes;
  }


  protected static function getDataMapperFor($dataMapper) {
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

  /**
   *
   * <code>
   * </code>
   *
   * @param   type
   * @return  null
   */
  public static function instituicaoHidden($viewInstance, $options = array()) {
    if (! array_key_exists('instituicaoId', $options))
      $instituicaoId = self::getPermissoes()->getInstituicao($viewInstance->getSession()->id_pessoa);

    $defaultOptions = array('id'    => 'ref_cod_instituicao',
                            'value' => isset($instituicaoId) ? $instituicaoId : null);

    $options = array_merge($defaultOptions, $options);
    call_user_func_array(array($viewInstance, 'campoOculto'), $options);
  }


  /**
   *
   * <code>
   * </code>
   *
   * @param   type
   * @return  null
   */
  public static function instituicaoSelect($viewInstance, $options = array()) {

    // TODO obter instituicoes conforme permissoes / tipo usuário
    if (! array_key_exists('instituicoes', $options))
      $instituicoes = array("1" => "teste");

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

    $options = array_merge($defaultOptions, $options);
    call_user_func_array(array($viewInstance, 'campoLista'), $options);
  }


  /**
   *
   * <code>
   * </code>
   *
   * @param   type
   * @return  null
   */
  public static function instituicao($viewInstance, $options = array()) {
    if (self::getPermissoes()->nivel_acesso($viewInstance->getSession()->id_pessoa) == 1)
      self::instituicaoSelect($viewInstance, $options);
    else
      self::instituicaoHidden($viewInstance, $options);
  }
}
?>
