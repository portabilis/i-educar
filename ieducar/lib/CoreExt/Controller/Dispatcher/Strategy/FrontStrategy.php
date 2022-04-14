<?php

class CoreExt_Controller_Dispatcher_Strategy_FrontStrategy extends CoreExt_Controller_Dispatcher_Abstract implements CoreExt_Controller_Dispatcher_Strategy_Interface
{
    /**
     * Instância de CoreExt_Controller_Interface.
     *
     * @var CoreExt_Controller_Interface
     */
    protected $_controller = null;

    /**
     * Construtor.
     *
     * @see CoreExt_Controller_Strategy_Interface#__construct($controller)
     */
    public function __construct(CoreExt_Controller_Interface $controller)
    {
        $this->setController($controller);
    }

    /**
     * @see CoreExt_Controller_Strategy_Interface#setController($controller)
     */
    public function setController(CoreExt_Controller_Interface $controller)
    {
        $this->_controller = $controller;

        return $this;
    }

    /**
     * @see CoreExt_Controller_Strategy_Interface#getController()
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * Não implementado.
     *
     * @see CoreExt_Controller_Strategy_Interface#dispatch()
     */
    public function dispatch()
    {
        throw new CoreExt_Controller_Dispatcher_Exception('Método CoreExt_Controller_Strategy_FrontStrategy::dispatch() não implementado.');
    }
}
