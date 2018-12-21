<?php

require_once 'CoreExt/Configurable.php';

interface CoreExt_Session_Storage_Interface extends CoreExt_Configurable
{
    /**
     * Inicializa a session.
     */
    public function start();

    /**
     *
     * @param string $key
     *
     * @return mixed
     */
    public function read($key);

    /**
     * Persiste um dado valor na session.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function write($key, $value);

    /**
     * Remove/apaga um dado na session.
     *
     * @param string $key
     */
    public function remove($key);

    /**
     * Destrói os dados de uma session.
     */
    public function destroy();

    /**
     * Gera um novo id para a session.
     */
    public function regenerate($destroy = false);

    /**
     * Persiste os dados da session no storage definido ao final da execução
     * do script PHP.
     */
    public function shutdown();
}
