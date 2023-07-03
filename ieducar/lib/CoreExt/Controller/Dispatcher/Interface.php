<?php

interface CoreExt_Controller_Dispatcher_Interface
{
    /**
     * Setter.
     *
     *
     * @return CoreExt_Controller_Dispatcher_Interface Provê interface fluída
     */
    public function setRequest(CoreExt_Controller_Request_Interface $request);

    /**
     * Getter.
     *
     * @return CoreExt_Controller_Request_Interface
     */
    public function getRequest();

    /**
     * Retorna uma string correspondendo a parte de controller de uma URL.
     *
     * @return string|null
     */
    public function getControllerName();

    /**
     * Retorna uma string correspondendo a parte de action de uma URL.
     *
     * @return string|null
     */
    public function getActionName();
}
