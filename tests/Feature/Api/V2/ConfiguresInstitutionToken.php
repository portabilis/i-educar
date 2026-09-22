<?php

namespace Tests\Feature\Api\V2;

use App\Models\LegacyInstitution;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyInstitutionFactory;

trait ConfiguresInstitutionToken
{
    protected function configureInstitutionToken(LegacyInstitution $institution): void
    {
        $institution->update(['ativo' => 1]);

        LegacyInstitution::query()
            ->where('cod_instituicao', '!=', $institution->getKey())
            ->update(['ativo' => 0]);

        $institution->generalConfiguration()->delete();

        LegacyGeneralConfigurationFactory::new()->create([
            'ref_cod_instituicao' => $institution,
            'token_novo_educacao' => 'valid-token',
        ]);
    }

    protected function institutionWithToken(): void
    {
        $this->configureInstitutionToken(LegacyInstitutionFactory::new()->create(['ativo' => 1]));
    }
}
