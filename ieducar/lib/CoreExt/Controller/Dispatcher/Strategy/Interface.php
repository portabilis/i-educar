<?php

interface CoreExt_Controller_Dispatcher_Strategy_Interface
{
    /**
     * Construtor.
     *
     * @param CoreExt_Controller_Interface $controller
     */
    public function __construct(CoreExt_Controller_Interface $controller);

    /**
     * Setter para a instância de CoreExt_Controller_Interface.
     *
     * @param CoreExt_Controller_Interface $controller
     *
     * @return CoreExt_Controller_Strategy_Interface Provê interface fluída
     */
    public function setController(CoreExt_Controller_Interface $controller);

    /**
     * Getter.
     *
     * @return CoreExt_Controller_Interface
     */
    public function getController();

    /**
     * Realiza o dispatch da requisição, encaminhando o controle da execução ao
     * controller adequado.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function dispatch();
}
