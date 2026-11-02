<?php

namespace App\Services;

use Illuminate\Support\Str;

class NameCapitalizationService
{
    public function execute(string $name): string
    {
        $titleCase = Str::title($name);

        return preg_replace_callback(
            '/(?<![\p{L}\p{M}])(de|da|do|dos|das|e)(?![\p{L}\p{M}])/iu',
            fn (array $matches) => Str::lower($matches[0]),
            $titleCase
        ) ?? throw new \InvalidArgumentException('O nome deve conter texto UTF-8 válido.');
    }
}
