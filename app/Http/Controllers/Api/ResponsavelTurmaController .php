<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateStateRegistrationRequest;
use App\Models\LegacyResponsavelTruma;
use Throwable;

class ResponsavelTrumaController extends Controller
{
    /**
     * Atualiza a inscrição estadual de um aluno.
     *
     * @param UpdateStateRegistrationRequest $request
     * @param LegacyResponsavelTruma                  $responsavel
     *
     * @throws Throwable
     *
     * @return array
     */
    public function updateStateRegistration(UpdateStateRegistrationRequest $request, LegacyResponsavelTruma $responsavel)
    {
        $responsavel->state_registration_id = $request->getStateRegistration();
        $responsavel->saveOrFail();

        return [
            'id' => $responsavel->getKey(),
            'state_registration_id' => $responsavel->state_registration_id,
        ];
    }
}
