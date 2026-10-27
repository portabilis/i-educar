<?php

namespace App\Services;

use App\Models\Builders\LegacyTransferRequestBuilder;
use App\Models\LegacyInstitution;
use App\Models\LegacyTransferRequest;
use App\User;
use App_Model_MatriculaSituacao;

class PendingTransferService
{
    public function forUser(User $user): LegacyTransferRequestBuilder
    {
        $institutionId = $user->ref_cod_instituicao;

        if (!$institutionId && $user->isAdmin()) {
            $institutionId = app(LegacyInstitution::class)->getKey();
        }

        if (!$institutionId) {
            return LegacyTransferRequest::query()->whereRaw('false');
        }

        return LegacyTransferRequest::query()
            ->active()
            ->unattended()
            ->whereHas('oldRegistration', fn ($query) => $query
                ->where('aprovado', App_Model_MatriculaSituacao::TRANSFERIDO)
                ->whereInstitution($institutionId))
            ->whereHas('destinationSchool', function ($query) use ($user, $institutionId) {
                $query->where('ativo', 1)
                    ->where('ref_cod_instituicao', $institutionId);

                if (!$user->isAdmin() && !$user->isInstitutional()) {
                    $query->whereIn('cod_escola', $user->schools()->select('cod_escola'));
                }
            });
    }
}
