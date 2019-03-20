<?php

namespace App\Services;

use App\Models\LegacyRegistration;
use Illuminate\Database\Eloquent\Collection;

class RegistrationService
{
    /**
     * @param array $ids
     *
     * @return Collection
     */
    public function findAll(array $ids)
    {
        return LegacyRegistration::query()
            ->whereIn('cod_matricula', $ids)
            ->get();
    }
}
