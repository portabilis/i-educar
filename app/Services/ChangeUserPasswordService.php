<?php

namespace App\Services;

use App\Models\LegacyEmployee;
use App\Rules\IsValidPassword;
use Illuminate\Support\Facades\Hash;


class ChangeUserPasswordService
{
    public function execute(LegacyEmployee $legacyEmployee)
    {
        $this->validate($legacyEmployee);
        $legacyEmployee->setPasswordAttribute(Hash::make($this->_senha));
        $legacyEmployee->force_reset_password = false;
        $legacyEmployee->data_troca_senha = now();
        $legacyEmployee->save();
    }

    public function validate(LegacyEmployee $legacyEmployee)
    {
        validator(
            ['password' => $legacyEmployee->getPasswordAttribute()],
            [
                'password' => [
                    new IsValidPassword()
                ]
            ]
        )->validate();
    }
}
