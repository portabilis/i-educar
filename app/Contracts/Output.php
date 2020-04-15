<?php

namespace App\Contracts;

interface Output
{
    /**
     * Avança a indicação de progresso
     */
    public function progressAdvance();

    /**
     * Envia uma mensagem de informação
     *
     * @param string $message
     */
    public function info($message);
}
