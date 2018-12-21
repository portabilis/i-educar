<?php

require_once 'CoreExt/Configurable.php';

abstract class CoreExt_Session_Abstract implements CoreExt_Configurable, ArrayAccess, Countable, Iterator
{
    /**
     * Opções de configuração geral da classe.
     *
     * @var array
     */
    protected $_options = [
        'sessionstorage' => null,
        'session_auto_start' => true
    ];

    /**
     * @var CoreExt_Session_Storage_Interface
     */
    protected $_sessionStorage = null;

    /**
     * @var array
     */
    protected $_sessionData = [];

    /**
     * Construtor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * @see CoreExt_Configurable#setOptions($options)
     */
    public function setOptions(array $options = [])
    {
        $options = array_change_key_case($options, CASE_LOWER);

        $defaultOptions = array_keys($this->getOptions());
        $passedOptions = array_keys($options);

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
     *
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
     *
     * @return mixed|NULL
     */
    public function getOption($key)
    {
        return $this->_hasOption($key) ? $this->_options[$key] : null;
    }

    /**
     * Setter.
     *
     * @param CoreExt_Session_Storage_Interface $storage
     */
    public function setSessionStorage(CoreExt_Session_Storage_Interface $storage)
    {
        $this->_sessionStorage = $storage;
    }

    /**
     * Getter.
     *
     * @return CoreExt_Session_Storage_Interface
     */
    public function getSessionStorage()
    {
        if (is_null($this->_sessionStorage)) {
            require_once 'CoreExt/Session/Storage/Default.php';
            $this->setSessionStorage(new CoreExt_Session_Storage_Default([
                'session_auto_start' => $this->getOption('session_auto_start')
            ]));
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
     *
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

        return null;
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
     *
     * @link  http://php.net/manual/en/language.oop5.overloading.php
     *
     * @param string|int $key
     * @param mixed      $val
     */
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Implementa o método mágico __get().
     *
     * @link  http://php.net/manual/en/language.oop5.overloading.php
     *
     * @param string|int $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * Implementa o método mágico __isset().
     *
     * @link  http://php.net/manual/en/language.oop5.overloading.php
     *
     * @param string|int $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Implementa o método mágico __unset().
     *
     * @link  http://php.net/manual/en/language.oop5.overloading.php
     *
     * @param string|int $key
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * @link http://br.php.net/manual/en/countable.count.php
     *
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

        return isset($key) ? true : false;
    }
}
