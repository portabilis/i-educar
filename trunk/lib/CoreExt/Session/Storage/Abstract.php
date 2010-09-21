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

require_once 'CoreExt/Session/Storage/Interface.php';

/**
 * CoreExt_Session_Storage_Abstract abstract class.
 *
 * Implementa operações básicas para facilitar a implementação de
 * CoreExt_Session_Storage_Interface.
 *
 * Opções disponíveis:
 * - session_name: o nome da session, o padrão é o valor definido no php.ini
 * - session_auto_start: se a session deve ser iniciada na instanciação da
 *   classe. Padrão é TRUE
 * - session_auto_shutdown: se um método de shutdown deve ser chamado no
 *   encerramento da execução do script PHP. Padrão é TRUE.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Session
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
abstract class CoreExt_Session_Storage_Abstract
  implements CoreExt_Session_Storage_Interface, Countable
{
  /**
   * Flag para definir se a session foi iniciada ou não, útil para impedir que
   * funções que enviem headers sejam chamadas novamente (session_start, p.ex.)
   * @var bool
   */
  protected static $_sessionStarted = FALSE;

  /**
   * Id da session atual.
   * @var string
   */
  protected static $_sessionId = NULL;

  /**
   * Opções de configuração geral da classe.
   * @var array
   */
  protected $_options = array(
    'session_name'          => NULL,
    'session_auto_start'    => TRUE,
    'session_auto_shutdown' => TRUE
  );

  /**
   * Construtor.
   * @param array $options Array de opções de configuração.
   */
  public function __construct(array $options = array())
  {
    $this->_init($options);

    if (TRUE == $this->getOption('session_auto_shutdown')) {
      register_shutdown_function(array($this, 'shutdown'));
    }
  }

  /**
   * Método de inicialização do storage. As subclasses devem sobrescrever
   * este método para alterar o comportamento do mecanismo de session do PHP.
   *
   * @return CoreExt_Session_Storage_Abstract Provê interfae fluída
   */
  protected function _init(array $options = array())
  {
    $this->setOptions($options);
  }

  /**
   * @see CoreExt_Configurable#setOptions($options)
   */
  public function setOptions(array $options = array())
  {
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
   * Getter.
   * @return string
   */
  public static function getSessionId()
  {
    return self::$_sessionId;
  }

  /**
   * Getter.
   * @return bool
   */
  public static function isStarted()
  {
    return self::$_sessionStarted;
  }

  /**
   * Getter.
   *
   * Deve ser implementado pelas subclasses para retornar o array de dados
   * persistidos na session, permitindo que clientes iterem livremente pelos
   * dados.
   *
   * @return array
   */
  public abstract function getSessionData();
}