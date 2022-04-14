<?php

abstract class CoreExt_Session_Storage_Abstract implements CoreExt_Session_Storage_Interface, Countable
{
    /**
     * Flag para definir se a session foi iniciada ou não, útil para impedir que
     * funções que enviem headers sejam chamadas novamente (session_start, p.ex.)
     *
     * @var bool
     */
    protected static $_sessionStarted = false;

    /**
     * Id da session atual.
     *
     * @var string
     */
    protected static $_sessionId = null;

    /**
     * Opções de configuração geral da classe.
     *
     * @var array
     */
    protected $_options = [
        'session_name' => null,
        'session_auto_start' => true,
        'session_auto_shutdown' => true
    ];

    /**
     * Construtor.
     *
     * @param array $options Array de opções de configuração.
     */
    public function __construct(array $options = [])
    {
        $this->_init($options);

        if (true == $this->getOption('session_auto_shutdown')) {
            register_shutdown_function([$this, 'shutdown']);
        }
    }

    /**
     * Método de inicialização do storage. As subclasses devem sobrescrever
     * este método para alterar o comportamento do mecanismo de session do PHP.
     *
     * @param array $options
     *
     * @return void
     */
    protected function _init(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * @see CoreExt_Configurable#setOptions($options)
     */
    public function setOptions(array $options = [])
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
     * Getter.
     *
     * @return string
     */
    public static function getSessionId()
    {
        return self::$_sessionId;
    }

    /**
     * Getter.
     *
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
    abstract public function getSessionData();
}
