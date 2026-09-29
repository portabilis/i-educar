<?php

namespace Tests\Feature\Api\V2;

use App\Models\LegacyInstitution;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyInstitutionFactory;

trait ConfiguresInstitutionToken
{
    protected function configureInstitutionToken(LegacyInstitution $institution, ?string $token = 'valid-token'): void
    {
        $institution->update(['ativo' => 1]);

        LegacyInstitution::query()
            ->where('cod_instituicao', '!=', $institution->getKey())
            ->update(['ativo' => 0]);

        $institution->generalConfiguration()->delete();

        LegacyGeneralConfigurationFactory::new()->create([
            'ref_cod_instituicao' => $institution,
            'token_novo_educacao' => $token,
        ]);
    }

    protected function institutionWithToken(?string $token = 'valid-token'): LegacyInstitution
    {
        $institution = LegacyInstitutionFactory::new()->create(['ativo' => 1]);

        $this->configureInstitutionToken($institution, $token);

        return $institution;
    }
}
