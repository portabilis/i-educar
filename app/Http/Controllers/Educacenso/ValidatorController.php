<?php

namespace App\Http\Controllers\Educacenso;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ValidatorController extends Controller
{
    const VALIDATORS = [
        'nome' => 'iEducar\Modules\Educacenso\Validator\NameValidator',
        'data-nascimento' => 'iEducar\Modules\Educacenso\Validator\BirthDateValidator',
        'certidao-nascimento' => 'iEducar\Modules\Educacenso\Validator\BirthCertificateValidator',
    ];

    public function validation($validator, Request $request)
    {
        $validatorClass = self::VALIDATORS[$validator] ?? null;
        if (is_null($validatorClass)) {
            return response()->json(['error' => 'Wrong validator', 'success' => false], 422);
        }
        $values = is_array($request->values) ? $request->values : [($request->value ?: '')];
        $validator = new $validatorClass(...$values);
        if ($validator->isValid()) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => $validator->getMessage(), 'success' => false], 422);
        }
    }
}
