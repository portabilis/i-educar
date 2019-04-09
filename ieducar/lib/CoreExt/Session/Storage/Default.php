<?php

use Illuminate\Support\Facades\Session;

require_once 'CoreExt/Session/Storage/Abstract.php';

class CoreExt_Session_Storage_Default extends CoreExt_Session_Storage_Abstract
{
    /**
     * @see CoreExt_Session_Storage_Interface#read($key)
     */
    public function read($key)
    {
        $returnValue = null;

        if (Session::has($key)) {
            $returnValue = Session::get($key);
        }

        return $returnValue;
    }

    /**
     * @see CoreExt_Session_Storage_Interface#write($key, $value)
     */
    public function write($key, $value)
    {
        Session::put($key, $value);
    }

    /**
     * @see CoreExt_Session_Storage_Interface#remove($key)
     */
    public function remove($key)
    {
        Session::forget($key);
    }

    /**
     * @see CoreExt_Session_Storage_Interface#start()
     */
    public function start()
    {
        //
    }

    /**
     * @see CoreExt_Session_Storage_Interface#destroy()
     */
    public function destroy()
    {
        //
    }

    /**
     * @see CoreExt_Session_Storage_Interface#regenerate()
     */
    public function regenerate($destroy = false)
    {
        //
    }

    /**
     * Persiste os dados da session no sistema de arquivos.
     *
     * @see CoreExt_Session_Storage_Interface#shutdown()
     */
    public function shutdown()
    {
        //
    }

    /**
     * @link http://br.php.net/manual/en/countable.count.php
     */
    public function count()
    {
        return count(Session::all());
    }

    /**
     * @see CoreExt_Session_Storage_Abstract#getSessionData()
     */
    public function getSessionData()
    {
        return Session::all();
    }
}
