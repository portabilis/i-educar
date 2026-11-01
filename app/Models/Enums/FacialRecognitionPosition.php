<?php

namespace App\Models\Enums;

use Illuminate\Support\Collection;

enum FacialRecognitionPosition: string
{
    case FRONT = 'FR';
    case SLIGHT_LEFT = 'SL';
    case SLIGHT_RIGHT = 'SR';
    case SLIGHT_UP = 'SU';
    case SLIGHT_DOWN = 'SD';

    public function name(): string
    {
        return match ($this) {
            self::FRONT => 'Frente',
            self::SLIGHT_LEFT => 'Levemente à esquerda',
            self::SLIGHT_RIGHT => 'Levemente à direita',
            self::SLIGHT_UP => 'Levemente para cima',
            self::SLIGHT_DOWN => 'Levemente para baixo',
        };
    }

    /**
     * @return Collection<int, string>
     */
    public static function getDescriptiveValues(): Collection
    {
        return collect(self::cases())->mapWithKeys(fn (FacialRecognitionPosition $type) => [$type->value => $type->name()]);
    }
}
