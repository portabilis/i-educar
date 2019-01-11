<?php

abstract class CoreExt_Singleton
{
    /**
     * A instância singleton de CoreExt_Singleton
     *
     * @var array
     */
    private static $_instance = [];

    /**
     * Construtor.
     */
    private function __construct()
    {
    }

    /**
     * Sobrescreva esse método para garantir que a subclasse possa criar um
     * singleton. Esta deve fazer uma chamada ao método _getInstance, passando
     * uma string que tenha como valor o nome da classe. Uma forma conveniente
     * de fazer isso é chamando _getInstance passando como parâmetro a constante
     * mágica __CLASS__.
     *
     * Exemplo:
     * <code>
     * <?php
     * ... // extends CoreExt_Singleton
     * public static function getInstance()
     * {
     *   return self::_getInstance(__CLASS__);
     * }
     * </code>
     *
     * @return void
     *
     * @throws CoreExt_Exception
     */
    public static function getInstance()
    {
        require_once 'CoreExt/Exception.php';
        throw new CoreExt_Exception('É necessário sobrescrever o método "getInstance()" de CoreExt_Singleton.');
    }

    /**
     * Retorna uma instância singleton, instanciando-a quando necessário.
     *
     * @param string $self Nome da subclasse de CoreExt_Singleton que será instanciada
     *
     * @return CoreExt_Singleton
     */
    protected static function _getInstance($self)
    {
        if (!isset(self::$_instance[$self])) {
            self::$_instance[$self] = new $self();
        }

        return self::$_instance[$self];
    }
}
