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

        return $result instanceof LegacyPerson ? $result->idpes : false;
    }

    public function checkUnknowUserExists(): bool
    {
        return (bool)$this->getUnknowUserId();
    }
}
