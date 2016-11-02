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
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Controller
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/Controller/Dispatcher/Interface.php';
require_once 'CoreExt/Configurable.php';

/**
 * CoreExt_Controller_Dispatcher_Abstract abstract class.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Controller
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
abstract class CoreExt_Controller_Dispatcher_Abstract
  implements CoreExt_Controller_Dispatcher_Interface, CoreExt_Configurable
{
  /**
   * Instância de CoreExt_Controller_Request_Interface
   * @var CoreExt_Controller_Request_Interface
   */
  protected $_request = NULL;

  /**
   * Opções de configuração geral da classe.
   * @var array
   */
  protected $_options = array(
    'controller_default_name' => 'index',
    'action_default_name' => 'index'
  );

  /**
   * @see CoreExt_Configurable#setOptions($options)
   */
  public function setOptions(array $options = array())
  {
    $defaultOptions = array_keys($this->getOptions());
    $passedOptions  = array_keys($options);

    if (0 < count(array_diff($passedOptions, $defaultOptions))) {
      throw new InvalidArgumentException(
        sprintf('A classe %s não suporta as opções: %s.', get_class($this), implode(', ', $passedOptions))
      );
    }

    $this->_options = array_merge($this->getOptions(), $options);
    return $this;
  }

  /**
   * @see CoreExt_Configurable#getOptions()
   */
  public function getOptions()
  {
    return $this->_options;
  }

  /**
   * Retorna um valor de opção de configuração ou NULL caso a opção não esteja
   * setada.
   *
   * @param string $key
   * @return mixed|NULL
   */
  public function getOption($key)
  {
    return $this->_hasOption($key) ? $this->_options[$key] : NULL;
  }

  /**
   * Verifica se uma opção está setada.
   *
   * @param string $key
   * @return bool
   */
  protected function _hasOption($key)
  {
    return array_key_exists($key, $this->_options);
  }

  /**
   * @see CoreExt_Controller_Dispatcher_Interface#setRequest($request)
   */
  public function setRequest(CoreExt_Controller_Request_Interface $request)
  {
    $this->_request = $request;
    return $this;
  }

  /**
   * @see CoreExt_Controller_Dispatcher_Interface#getRequest()
   */
  public function getRequest()
  {
    if (is_null($this->_request))
    {
      require_once 'CoreExt/Controller/Request.php';
      $this->setRequest(new CoreExt_Controller_Request());
    }

    return $this->_request;
  }

  /**
   * Retorna o componente 'path' de uma URL como array, onde cada item
   * corresponde a um elemento do path.
   *
   * Exemplo:
   * <code>
   * <?php
   * // $_SERVER['REQUEST_URI'] = 'http://www.example.com/path1/path2/path3?qs=1';
   * print_r($this->_getUrlPath());
   * // Array
   * (
   *   [0] => path1
   *   [1] => path2
   *   [2] => path3
   * )
   * </code>
   *
   * @return array
   */
  protected function _getUrlPath()
  {
    $path    = parse_url($this->getRequest()->get('REQUEST_URI'), PHP_URL_PATH);
    $path    = explode('/', $path);

    $baseurl = parse_url($this->getRequest()->getBaseurl(), PHP_URL_PATH);
    $baseurl = explode('/', $baseurl);

    $script  = explode('/', $this->getRequest()->get('SCRIPT_FILENAME'));
    $script  = array_pop($script);

    // Retorna os elementos de path diferentes entre a REQUEST_URI e a baseurl
    $path = array_diff_assoc($path, $baseurl);

    $items = count($path);

    if ($items >= 1) {
      // Combina os elementos em um array cujo o índice começa do '0'
      $path = array_combine(range(0, $items - 1), $path);

      // Caso o primeiro elemento seja o nome do script, remove-o
      if (strtolower($script) === strtolower($path[0]) || '' === $path[0]) {
        array_shift($path);
      }
    }
    else {
      $path = array();
    }

    return $path;
  }

  /**
   * @see CoreExt_Controller_Dispatcher_Interface#getController()
   */
  public function getControllerName()
  {
    $path = $this->_getUrlPath();
    return isset($path[0]) ? $path[0] : $this->getOption('controller_default_name');
  }

  /**
   * @see CoreExt_Controller_Dispatcher_Interface#getAction()
   */
  public function getActionName()
  {
    $path = $this->_getUrlPath();
    return isset($path[1]) ? $path[1] : $this->getOption('action_default_name');
  }
}