<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ImplicitRule;

class StudentIdentificationRule implements DataAwareRule, ImplicitRule
{
    private const INEP_LENGTH = 12;

    private const CPF_LENGTH = 11;

    /** @var array<string, mixed> */
    private array $data = [];

    /**
     * @param string $cpfField    campo do CPF nos dados validados
     * @param string $inepField   campo do código INEP do aluno
     * @param string $hasCpfField campo que indica se o aluno possui CPF
     */
    public function __construct(
        private readonly string $cpfField = 'cpf',
        private readonly string $inepField = 'inep_id',
        private readonly string $hasCpfField = 'possui_cpf',
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $hasValidCpf = $this->isValidCpf($this->data[$this->cpfField] ?? null);
        $hasValidInep = $this->isValidInep($this->data[$this->inepField] ?? null);

        return match ($this->hasCpf()) {
            true => $hasValidCpf,
            false => $hasValidInep,
            null => $hasValidCpf || $hasValidInep,
        };
    }

    /**
     * @return string
     */
    public function message()
    {
        return match ($this->hasCpf()) {
            true => 'Informe um CPF válido para o aluno.',
            false => 'Informe um código INEP válido para o aluno, pois ele não possui CPF.',
            null => 'Informe um CPF ou um código INEP válido para o aluno.',
        };
    }


    private function hasCpf(): ?bool
    {
        $value = $this->data[$this->hasCpfField] ?? null;

        if ($value === null || $value === '') {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }


    private function isValidCpf(mixed $value): bool
    {
        if (!is_string($value) && !is_int($value)) {
            return false;
        }

        $cpf = preg_replace('/[.\-\s]/', '', (string) $value);

        if (!ctype_digit($cpf) || strlen($cpf) !== self::CPF_LENGTH || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        foreach ([9, 10] as $length) {
            $sum = 0;

            for ($position = 0; $position < $length; $position++) {
                $sum += (int) $cpf[$position] * ($length + 1 - $position);
            }

            if ((int) $cpf[$length] !== ($sum * 10 % 11) % 10) {
                return false;
            }
        }

        return true;
    }


    private function isValidInep(mixed $value): bool
    {
        if (!is_string($value) && !is_int($value)) {
            return false;
        }

        $inep = (string) $value;

        return ctype_digit($inep)
            && strlen($inep) === self::INEP_LENGTH
            && $inep[0] !== '0';
    }
}
