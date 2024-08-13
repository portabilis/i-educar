<?php

namespace App\Models\Enums;

use Illuminate\Support\Collection;

enum AbsenceDelayType: int
{
    case DELAY = 1;
    case ABSENCE = 2;

    public function name(): string
    {
        return match ($this) {
            self::DELAY => 'Atraso',
            self::ABSENCE => 'Falta'
        };
    }

    /**
     * @return Collection<int, string>
     */
    public static function getDescriptiveValues(): Collection
    {
        return collect(self::cases())->mapWithKeys(fn (AbsenceDelayType $type) => [$type->value => $type->name()]);
    }
}
