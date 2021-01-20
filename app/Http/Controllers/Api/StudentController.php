<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LegacyStudent;
use Illuminate\Http\Request;
use Throwable;

class StudentController extends Controller
{
    /**
     * Atualiza a inscrição estadual de um aluno.
     *
     * @param Request       $request
     * @param LegacyStudent $student
     *
     * @throws Throwable
     *
     * @return LegacyStudent
     */
    public function updateStateRegistration(Request $request, LegacyStudent $student)
    {
        $student->state_registration_id = $request->input('state_registration_id');
        $student->saveOrFail();

        return $student;
    }
}
