<?php

declare(strict_types = 1);

namespace App\Support\Database;

use App\Models\LegacyPerson;

trait UnknowUser
{

    public function getUnknowUserId(): int
    {
        $result = LegacyPerson::query()
            ->where('nome', 'Desconhecido')
            ->first();

        if (!$result instanceof LegacyPerson)
            throw new \Exception('Unknow user not found');

        return $result->idpes;
    }

    public function checkUnknowUserExists(): bool
    {
        return (bool) $this->getUnknowUserId();
    }
}
