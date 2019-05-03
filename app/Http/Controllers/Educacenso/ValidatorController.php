<?php

namespace App\Http\Controllers\Educacenso;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ValidatorController extends Controller
{
    const VALIDATORS = [
        'nome' => 'iEducar\Modules\Educacenso\Validator\NameValidator',
        'data-nascimento' => 'iEducar\Modules\Educacenso\Validator\BirthDateValidator',
    ];

    public function validation($validator, Request $request)
    {
        $validatorClass = self::VALIDATORS[$validator] ?? null;
        if (is_null($validatorClass)) {
            return response()->json(['error' => 'Wrong validator', 'success' => false], 422);
        }

        $validator = new $validatorClass($request->value ?: '');
        if ($validator->isValid()) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => $validator->getMessage(), 'success' => false], 422);
        }
    }
}
