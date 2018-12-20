<?php

require_once 'CoreExt/Session/Storage/Abstract.php';

class CoreExt_Session_Storage_Default extends CoreExt_Session_Storage_Abstract
{
    /**
     * @see CoreExt_Session_Storage_Abstract#_init()
     */
    protected function _init(array $options = [])
    {
        $options = array_merge([
            'session_use_cookies' => ini_get('session.use_cookies')
        ], $options);

        parent::_init($options);

        if (!is_null($this->getOption('session_name'))) {
            session_name($this->getOption('session_name'));
        }

        if (!is_null(self::$_sessionId)) {
            @session_id(self::$_sessionId);
        }

        if (true == $this->getOption('session_auto_start')) {
            $this->start();
        }
    }

    /**
     * @see CoreExt_Session_Storage_Interface#read($key)
     */
    public function read($key)
    {
        $returnValue = null;

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
        if (!$this->isStarted() && @session_start()) {
            self::$_sessionStarted = true;
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
    public function regenerate($destroy = false)
    {
        if ($this->isStarted()) {
            session_regenerate_id($destroy);
            self::$_sessionId = session_id();
        }
    }

    /**
     * Persiste os dados da session no sistema de arquivos.
     *
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
