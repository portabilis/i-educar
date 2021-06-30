<?php

namespace App\Services;

use Illuminate\Validation\Rules\Password;

class ValidateUserPasswordService
{
    public function execute(string $password)
    {
        $this->validate($password);
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
