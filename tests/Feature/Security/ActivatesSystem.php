<?php

namespace Tests\Feature\Security;

use App\Models\LegacyGeneralConfiguration;
use Database\Factories\LegacyInstitutionFactory;

/**
 * Marca o sistema como ativo para a instituição atual. Sem isso, o middleware
 * `ieducar.suspended` redireciona usuários que não são administradores para a
 * tela de sistema suspenso antes de a rota ser avaliada.
 */
trait ActivatesSystem
{
    protected function activateSystem(): void
    {
        LegacyGeneralConfiguration::query()->update(['active_on_ieducar' => 1]);

        LegacyGeneralConfiguration::query()->firstOrCreate(
            ['ref_cod_instituicao' => LegacyInstitutionFactory::new()->current()->getKey()],
            ['active_on_ieducar' => 1],
        );
    }
}
