<?php

namespace App\Support\ExternalServices;

use App\Services\iDiarioService;

trait iDiario
{
    /**
     * Retorna instância do iDiarioService
     *
     * @return iDiarioService|null
     */
    public function getIdiarioService()
    {
        if (iDiarioService::hasIdiarioConfigurations()) {
            return app(iDiarioService::class);
        }

        return null;
    }
}
