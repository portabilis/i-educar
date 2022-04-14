<?php

namespace App\Services;

use App\Models\LegacyEmployee;
use Carbon\Carbon;
use Illuminate\Contracts\Hashing\Hasher;

class ChangeUserPasswordService
{
    private $validateUserPasswordService;
    private $hash;
    private $carbon;

    public function __construct(ValidateUserPasswordService $validateUserPasswordService, Hasher $hash, Carbon $carbon)
    {
        $this->validateUserPasswordService = $validateUserPasswordService;
        $this->hash = $hash;
        $this->carbon = $carbon;
    }

    public function execute(LegacyEmployee $legacyEmployee, string $password)
    {
        $this->validate($password, $legacyEmployee->getPasswordAttribute());
        $legacyEmployee->setPasswordAttribute($this->hash->make($password));
        $legacyEmployee->force_reset_password = false;
        $legacyEmployee->data_troca_senha = $this->carbon->nowWithSameTz();
        $legacyEmployee->save();
    }

    public function validate(string $newPassword, string $oldPassword)
    {
        $this->validateUserPasswordService->execute($newPassword, $oldPassword);
    }
}
