<?php

namespace App\Rules;

use App\Services\DiarioControllerService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class RegistrationExistsInSchoolClass implements ValidationRule
{
    public function __construct(
        private readonly DiarioControllerService $service,
        private readonly int $schoolClassId,
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $registration = $this->service->findMatricula(
            turmaId: $this->schoolClassId,
            alunoId: (int) $value,
        );

        if ($registration === null) {
            $fail('Matrícula não encontrada para o aluno e turma informados.');
        }
    }
}
