<?php

interface CoreExt_Controller_Page_Interface extends CoreExt_Controller_Interface
{
    /**
     * Gera o código HTML para a requisição.
     *
     *
     * @return string
     */
    public function generate(CoreExt_Controller_Page_Interface $instance);
}
