<?php

declare(strict_types=1);

namespace App\Support\Database;

use App\Models\LegacyPerson;
use Exception;

trait UnknowUser
{
    public function getUnknowUserId(): int
    {
        $result = $this->getUnknowUser();

        if (!$result instanceof LegacyPerson) {
            throw new Exception('Unknow user not found');
        }

        return $result->idpes;
    }

    public function checkUnknowUserExists(): bool
    {
        return $this->getUnknowUser() instanceof LegacyPerson;
    }

    private function getUnknowUser(): ?LegacyPerson
    {
        return LegacyPerson::query()
            ->where('nome', 'Desconhecido')
            ->first();
    }
}
