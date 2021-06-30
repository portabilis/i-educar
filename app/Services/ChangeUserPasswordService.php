<?php

namespace App\Services;

use App\Models\LegacyEmployee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangeUserPasswordService
{
    public function execute(LegacyEmployee $legacyEmployee, string $password)
    {
        $this->validate($password);
        $legacyEmployee->setPasswordAttribute(Hash::make($password));
        $legacyEmployee->force_reset_password = false;
        $legacyEmployee->data_troca_senha = now();
        $legacyEmployee->save();
    }

    public function validate(string $password)
    {
        validator(
            ['password' => $password],
            [
                'password' => [
                    Password::min(8)
                        ->mixedCase()
                        ->letters()
                        ->numbers()
                        ->symbols()
                ]
            ]
        )->validate();
    }
}
