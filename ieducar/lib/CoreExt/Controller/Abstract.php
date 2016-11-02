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
 * @version   $Id: /ieducar/branches/teste/ieducar/lib/CoreExt/Controller/Front.php 645 2009-11-12T20:08:26.084511Z eriksen  $
 */

require_once 'CoreExt/Controller/Interface.php';

/**
 * CoreExt_Controller_Abstract abstract class.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Controller
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
abstract class CoreExt_Controller_Abstract implements CoreExt_Controller_Interface
{
  /**
   * Uma instância de CoreExt_Controller_Request_Interface
   * @var CoreExt_Controller_Request_Interface
   */
  protected $_request = NULL;

  /**
   * Uma instância de CoreExt_Session_Abstract
   * @var CoreExt_Session_Abstract
   */
  protected $_session = NULL;

  /**
   * Uma instância de CoreExt_Controller_Dispatcher_Interface
   * @var CoreExt_Controller_Dispatcher_Interface
   */
  protected $_dispatcher = NULL;

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
   * Setter.
   * @param CoreExt_Controller_Request_Interface $request
   * @return CoreExt_Controller_Interface
   */
  public function setRequest(CoreExt_Controller_Request_Interface $request)
  {
    $this->_request = $request;
    return $this;
  }

  /**
   * Getter para uma instância de CoreExt_Controller_Request_Interface.
   *
   * Instância via lazy initialization uma instância de
   * CoreExt_Controller_Request_Interface caso nenhuma seja explicitamente
   * atribuída a instância atual.
   *
   * @return CoreExt_Controller_Request_Interface
   */
  public function getRequest()
  {
    if (is_null($this->_request)) {
      require_once 'CoreExt/Controller/Request.php';
      $this->setRequest(new CoreExt_Controller_Request());
    }
    return $this->_request;
  }

  /**
   * Setter.
   * @param CoreExt_Session_Abstract $session
   * @return CoreExt_Controller_Interface
   */
  public function setSession(CoreExt_Session_Abstract $session)
  {
    $this->_session = $session;
    return $this;
  }

  /**
   * Getter para uma instância de CoreExt_Session.
   *
   * Instância via lazy initialization uma instância de CoreExt_Session caso
   * nenhuma seja explicitamente atribuída a instância atual.
   *
   * @return CoreExt_Session
   */
  public function getSession()
  {
    if (is_null($this->_session)) {
      require_once 'CoreExt/Session.php';
      $this->setSession(new CoreExt_Session());
    }
    return $this->_session;
  }

  /**
   * Setter.
   * @param CoreExt_Controller_Dispatcher_Interface $dispatcher
   * @return CoreExt_Controller_Interface Provê interface fluída
   */
  public function setDispatcher(CoreExt_Controller_Dispatcher_Interface $dispatcher)
  {
    $this->_dispatcher = $dispatcher;
    return $this;
  }

  /**
   * Getter.
   * @return CoreExt_Controller_Dispatcher_Interface
   */
  public function getDispatcher()
  {
    if (is_null($this->_dispatcher)) {
      require_once 'CoreExt/Controller/Dispatcher/Standard.php';
      $this->setDispatcher(new CoreExt_Controller_Dispatcher_Standard());
    }
    return $this->_dispatcher;
  }

  /**
   * Redirect HTTP simples (espartaníssimo).
   *
   * Se a URL for relativa, prefixa o caminho com o baseurl configurado para
   * o objeto CoreExt_Controller_Request.
   *
   * @param string $url
   * @todo Implementar opções de configuração de código de status de
   *       redirecionamento. {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html}
   */
  public function redirect($url)
  {
    $parsed = parse_url($url, PHP_URL_HOST);

    if ('' == $parsed['host']) {
      $url = $this->getRequest()->getBaseurl() . '/' . $url;
    }

    header(sprintf('Location: %s', $url));
  }
}