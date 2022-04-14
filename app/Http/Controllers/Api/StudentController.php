<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateStateRegistrationRequest;
use App\Models\LegacyStudent;
use Throwable;

class StudentController extends Controller
{
    /**
     * Atualiza a inscrição estadual de um aluno.
     *
     * @param UpdateStateRegistrationRequest $request
     * @param LegacyStudent                  $student
     *
     * @throws Throwable
     *
     * @return array
     */
    public function updateStateRegistration(UpdateStateRegistrationRequest $request, LegacyStudent $student)
    {
        $student->state_registration_id = $request->getStateRegistration();
        $student->saveOrFail();

        return [
            'id' => $student->getKey(),
            'state_registration_id' => $student->state_registration_id,
        ];
    }
}
