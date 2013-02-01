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

require_once 'CoreExt/Configurable.php';

/**
 * CoreExt_Session_Abstract abstract class.
 *
 * Componente de gerenciamento de session PHP. Implementa as interfaces
 * ArrayAccess, Countable e Iterator do Standard PHP Library (SPL), tornando
 * possível o acesso simples aos dados da sessão através da interface array
 * ou orientada a objeto.
 *
 * A persistência da session é implementada por uma classe adapter, uma
 * subclasse de CoreExt_Session_Storage_Interface. Isso torna simples a
 * reposição do storage de session: basta criar um novo adapter e passar como
 * argumento ao construtor dessa classe.
 *
 * As opções de configuração da classe são:
 * - sessionStorage: instância de CoreExt_Session_Storage_Interface
 * - session_auto_start: bool, se é para iniciar automaticamente a session
 *
 * Como mencionado, esta classe possui diversas formas de acesso aos dados
 * persistidos na session:
 *
 * <code>
 * <?php
 * $session = new CoreExt_Session();
 *
 * // Acesso OO dos dados da session
 * $session->foo = 'bar';
 * $session->bar = 'foo';
 *
 * // Acesso array dos dados da session
 * $session->foo2 = 'bar2';
 * $session->bar2 = 'foo2';
 *
 * // É possível iterar o objeto CoreExt_Session
 * foreach ($session as $key => $value) {
 *   print $key . ': ' . $value . PHP_EOL;
 * }
 *
 * // Imprime:
 * // foo: bar
 * // bar: foo
 * // foo2: bar2
 * // bar2: foo2
 * </code>
 *
 * A classe se encarrega de fechar a sessão no final da execução do PHP.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Session
 * @since     Classe disponível desde a versão 1.1.0
 * @todo      Implementar chamada a regenerateId de CoreExt_Session_Storage_Interface
 * @todo      Implementar funcionalidade "remeber me"
 * @version   @@package_version@@
 */
abstract class CoreExt_Session_Abstract
  implements CoreExt_Configurable, ArrayAccess, Countable, Iterator
{
  /**
   * Opções de configuração geral da classe.
   * @var array
   */
  protected $_options = array(
    'sessionstorage'     => NULL,
    'session_auto_start' => TRUE
  );

  /**
   * @var CoreExt_Session_Storage_Interface
   */
  protected $_sessionStorage = NULL;

  /**
   * @var array
   */
  protected $_sessionData = array();

  /**
   * Construtor.
   * @param array $options
   */
  public function __construct(array $options = array())
  {
    $this->setOptions($options);
  }

  /**
   * @see CoreExt_Configurable#setOptions($options)
   */
  public function setOptions(array $options = array())
  {
    $options = array_change_key_case($options, CASE_LOWER);

    $defaultOptions = array_keys($this->getOptions());
    $passedOptions  = array_keys($options);

    if (0 < count(array_diff($passedOptions, $defaultOptions))) {
      require_once 'CoreExt/Exception/InvalidArgumentException.php';
      throw new CoreExt_Exception_InvalidArgumentException(
        sprintf('A classe %s não suporta as opções: %s.', get_class($this), implode(', ', $passedOptions))
      );
    }

    if (isset($options['sessionstorage'])) {
      $this->setSessionStorage($options['sessionstorage']);
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
   * @param CoreExt_Session_Storage_Interface $storage
   */
  public function setSessionStorage(CoreExt_Session_Storage_Interface $storage)
  {
    $this->_sessionStorage = $storage;
  }

  /**
   * Getter.
   * @return CoreExt_Session_Storage_Interface
   */
  public function getSessionStorage()
  {
    if (is_null($this->_sessionStorage)) {
      require_once 'CoreExt/Session/Storage/Default.php';
      $this->setSessionStorage(new CoreExt_Session_Storage_Default(array(
        'session_auto_start' => $this->getOption('session_auto_start')
      )));
    }
    return $this->_sessionStorage;
  }

  /**
   * Getter.
   *
   * Retorna o array de dados gerenciados por CoreExt_Session_Storage_Interface,
   * atualizando o atributo $_sessionData quando este diferir do valor retornado.
   *
   * @return array
   * @see current()
   */
  public function getSessionData()
  {
    if ($this->_sessionData != $this->getSessionStorage()->getSessionData()) {
      $this->_sessionData = $this->getSessionStorage()->getSessionData();
    }
    return $this->_sessionData;
  }

  /**
   * @link http://br.php.net/manual/en/arrayaccess.offsetexists.php
   */
  public function offsetExists($offset)
  {
    $value = $this->getSessionStorage()->read($offset);
    return isset($value);
  }

  /**
   * @link http://br.php.net/manual/en/arrayaccess.offsetget.php
   */
  public function offsetGet($offset)
  {
    if ($this->offsetExists($offset)) {
      return $this->getSessionStorage()->read($offset);
    }
    return NULL;
  }

  /**
   * @link http://br.php.net/manual/en/arrayaccess.offsetset.php
   */
  public function offsetSet($offset, $value)
  {
    $this->getSessionStorage()->write((string) $offset, $value);
  }

  /**
   * @link http://br.php.net/manual/en/arrayaccess.offsetunset.php
   */
  public function offsetUnset($offset)
  {
    $this->getSessionStorage()->remove($offset);
  }

  /**
   * Implementa o método mágico __set().
   * @link  http://php.net/manual/en/language.oop5.overloading.php
   * @param string|int $key
   * @param mixed $val
   */
  public function __set($key, $value)
  {
    $this->offsetSet($key, $value);
  }

  /**
   * Implementa o método mágico __get().
   * @link  http://php.net/manual/en/language.oop5.overloading.php
   * @param string|int $key
   * @return mixed
   */
  public function __get($key)
  {
    return $this->offsetGet($key);
  }

  /**
   * Implementa o método mágico __isset().
   * @link  http://php.net/manual/en/language.oop5.overloading.php
   * @param string|int $key
   * @return bool
   */
  public function __isset($key)
  {
    return $this->offsetExists($key);
  }

  /**
   * Implementa o método mágico __unset().
   * @link  http://php.net/manual/en/language.oop5.overloading.php
   * @param string|int $key
   */
  public function __unset($key)
  {
    $this->offsetUnset($key);
  }

  /**
   * @link http://br.php.net/manual/en/countable.count.php
   * @return int
   */
  public function count()
  {
    return $this->getSessionStorage()->count();
  }

  /**
   * Implementa o método Iterator::current(). Chama método getSessionData()
   * para atualizar o atributo $_sessionData, permitindo a ação da função
   * {@link http://br.php.net/current current()}.
   *
   * @link http://br.php.net/manual/en/iterator.current.php
   */
  public function current()
  {
    $this->getSessionData();
    return current($this->_sessionData);
  }

  /**
   * @link http://br.php.net/manual/en/iterator.key.php
   */
  public function key()
  {
    $data = $this->getSessionData();
    return key($this->_sessionData);
  }

  /**
   * @link http://br.php.net/manual/en/iterator.next.php
   */
  public function next()
  {
    $data = $this->getSessionData();
    next($this->_sessionData);
  }

  /**
   * @link http://br.php.net/manual/en/iterator.rewind.php
   */
  public function rewind()
  {
    $data = $this->getSessionData();
    reset($this->_sessionData);
  }

  /**
   * @link http://br.php.net/manual/en/iterator.valid.php
   */
  public function valid()
  {
    $key = key($this->_sessionData);
    return isset($key) ? TRUE : FALSE;
  }
}