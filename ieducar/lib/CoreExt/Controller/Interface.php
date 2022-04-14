<?php

interface CoreExt_Controller_Interface extends CoreExt_Configurable
{
    /**
     * Despacha o controle da execução para uma instância de
     * CoreExt_Controller_Interface.
     */
    public function dispatch();
}
