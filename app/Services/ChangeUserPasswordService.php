<?php

namespace App\Services;

use App\Events\UserUpdated;
use App\Models\LegacyEmployee;
use App\Models\LegacyUser;
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
        $this->validate($password, $legacyEmployee->password);
        $legacyEmployee->password = $this->hash->make($password);
        $legacyEmployee->force_reset_password = false;
        $legacyEmployee->data_troca_senha = $this->carbon->nowWithSameTz();
        $legacyEmployee->save();

        $user = LegacyUser::find($legacyEmployee->getKey());
        if ($user) {
            UserUpdated::dispatch($user);
        }
    }

    public function validate(string $newPassword, string $oldPassword)
    {
        $this->validateUserPasswordService->execute($newPassword, $oldPassword);
    }
}
