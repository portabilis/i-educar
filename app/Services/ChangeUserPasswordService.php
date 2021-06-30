<?php

namespace App\Services;

use App\Models\LegacyEmployee;
use Illuminate\Support\Facades\Hash;

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
        $validateUserPasswordService = app(ValidateUserPasswordService::class);
        $validateUserPasswordService->execute($password);
    }
}
