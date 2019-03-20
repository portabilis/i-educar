<?php

namespace App\Services;

use App\Models\LegacyRegistration;

class RegistrationService
{
    public function findAll(array $ids)
    {
        return LegacyRegistration::query()->whereIn('cod_matricula', $ids)->get();
    }
}
