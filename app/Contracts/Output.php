<?php

namespace App\Contracts;

interface Output
{
    /**
     * Avança a indicação de progresso
     */
    public function progressAdvance();

    /**
     * Inicia a indicação de progresso
     *
     * @param $count
     */
    public function progressStart($max);

    /**
     * Finaliza a indicação de progresso
     */
    public function progressFinish();

    /**
     * Envia uma mensagem de informação
     *
     * @param string $message
     */
    public function info($message);
}
