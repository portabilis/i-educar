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
 * @package   CoreExt_Session
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/Session/Storage/Abstract.php';

/**
 * CoreExt_Session_Storage_Default class.
 *
 * Storage de session padrão de CoreExt_Session, usa o mecanismo built-in do
 * PHP.
 *
 * Opções disponíveis:
 * - session_use_cookies: se é para setar um cookie de session no browser do
 *   usuário. Usa o valor configurado no php.ini caso não informado
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Session
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class CoreExt_Session_Storage_Default extends CoreExt_Session_Storage_Abstract
{
  /**
   * @see CoreExt_Session_Storage_Abstract#_init()
   */
  protected function _init(array $options = array())
  {
    $options = array_merge(array(
      'session_use_cookies' => ini_get('session.use_cookies')
    ), $options);

    parent::_init($options);

    if (!is_null($this->getOption('session_name'))) {
      session_name($this->getOption('session_name'));
    }

    if (!is_null(self::$_sessionId)) {
      session_id(self::$_sessionId);
    }

    if (TRUE == $this->getOption('session_auto_start')) {
      $this->start();
    }
  }

  /**
   * @see CoreExt_Session_Storage_Interface#read($key)
   */
  public function read($key)
  {
    $returnValue = NULL;

    if (isset($_SESSION[$key])) {
      $returnValue = $_SESSION[$key];
    }

    return $returnValue;
  }

  /**
   * @see CoreExt_Session_Storage_Interface#write($key, $value)
   */
  public function write($key, $value)
  {
    $_SESSION[$key] = $value;
  }

  /**
   * @see CoreExt_Session_Storage_Interface#remove($key)
   */
  public function remove($key)
  {
    unset($_SESSION[$key]);
  }

  /**
   * @see CoreExt_Session_Storage_Interface#start()
   */
  public function start()
  {
    if (!$this->isStarted() && session_start()) {
      self::$_sessionStarted = TRUE;
      self::$_sessionId = session_id();
    }
  }

  /**
   * @see CoreExt_Session_Storage_Interface#destroy()
   */
  public function destroy()
  {
    if ($this->isStarted()) {
      return session_destroy();
    }
  }

  /**
   * @see CoreExt_Session_Storage_Interface#regenerate()
   */
  public function regenerate($destroy = FALSE)
  {
    if ($this->isStarted()) {
      session_regenerate_id($destroy);
      self::$_sessionId = session_id();
    }
  }

  /**
   * Persiste os dados da session no sistema de arquivos.
   * @see CoreExt_Session_Storage_Interface#shutdown()
   */
  public function shutdown()
  {
    if ($this->isStarted()) {
      session_write_close();
    }
  }

  /**
   * @link http://br.php.net/manual/en/countable.count.php
   */
  public function count()
  {
    return count($_SESSION);
  }

  /**
   * @see CoreExt_Session_Storage_Abstract#getSessionData()
   */
  public function getSessionData()
  {
    return $_SESSION;
  }
}