<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class FunctionAbbreviationRule implements DataAwareRule, ValidationRule
{
    private array $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $nomeFuncao = $this->data['nm_funcao'] ?? '';
        if (mb_strlen((string) $value) > mb_strlen((string) $nomeFuncao)) {
            $fail('A abreviatura não pode ser maior que o nome da função.');
        }
    }
}