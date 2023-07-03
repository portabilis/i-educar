<?php

namespace App\Models\DataSearch;

class StudentFilter
{
    public function __construct(
        public readonly ?int $studentCode = null,
        public readonly ?string $inep = null,
        public readonly ?string $stateNetwork = null,
        public readonly ?string $studentName = null,
        public readonly ?string $birthdate = null,
        public readonly ?string $cpf = null,
        public readonly ?string $rg = null,
        public readonly ?string $fatherName = null,
        public readonly ?string $motherName = null,
        public readonly ?string $responsableName = null,
        public readonly ?int $year = null,
        public readonly ?int $institution = null,
        public readonly ?int $school = null,
        public readonly ?int $course = null,
        public readonly ?int $grade = null,
        public readonly ?int $perPage = null,
        public readonly ?string $pageName = null,
        public readonly ?bool $similarity = null,
    ) {
    }
}
