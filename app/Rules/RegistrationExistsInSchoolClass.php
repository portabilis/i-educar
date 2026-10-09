<?php

namespace App\Rules;

use App\Services\DiarioControllerService;
use Closure;
use iEducar\Modules\Enrollments\Exceptions\StudentNotEnrolledInSchoolClass;
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

            return;
        }

        // findMatricula aceita enturmações que o IedFinder recusa, como a de turma desativada;
        // o lançamento lê a regra de avaliação pelo IedFinder, então a matrícula precisa passar nos dois.
        try {
            $this->service->getMatricula($registration->getKey());
        } catch (StudentNotEnrolledInSchoolClass $exception) {
            $fail($exception->getMessage());
        }
    }
}
