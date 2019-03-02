<?php

namespace App\Exports;

use App\Repositories\StudentRepositoryEloquent;
use App\Models\GuardianType;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StudentsExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize
{
    protected $repository;

    protected $params;

    public function __construct(array $params)
    {
        $this->repository = new StudentRepositoryEloquent;
        $this->params = $params;
    }

    public function collection()
    {
        return $this->repository->list($this->params);
    }

    public function map($student): array
    {
        $row = [];
        $fatherName = $student->individual->father->person->name ?? $student->father_name ?? null;
        $motherName = $student->individual->mother->person->name ?? $student->mother_name ?? null;
        $guardianName = $student->individual->guardian->person->name ?? null;
        $fatherAndMotherName = sprintf('%s, %s', $fatherName, $motherName);

        $fatherDocument = $student->individual->father->cpf ?? null;
        $motherDocument = $student->individual->mother->cpf ?? null;
        $guardianDocument = $student->individual->guardian->cpf ?? null;

        switch ($student->guardian_type) {
            case GuardianType::FATHER:
                $guardianName = $fatherName;
                $guardianDocument = $fatherDocument;
                break;

            case GuardianType::MOTHER:
                $guardianName = $motherName;
                $guardianDocument = $motherDocument;
                break;

            case GuardianType::BOTH:
                $guardianName = $fatherAndMotherName;
                $documents = [];

                if (!is_null($fatherDocument)) {
                    $documents[] = $fatherDocument;
                }

                if (!is_null($motherDocument)) {
                    $documents[] = $motherDocument;
                }

                $guardianDocument = join(', ', $documents);
                break;
        }

        $row[] = $student->id;
        $row[] = $student->census->inep_code ?? null;
        $row[] = $student->individual->social_name ?? $student->individual->person->name;
        $row[] = $motherName;
        $row[] = $guardianName;
        $row[] = $guardianDocument;

        return $row;
    }

    public function headings(): array
    {
        return [
            'Código do aluno',
            'Código INEP',
            'Nome do aluno',
            'Nome da mãe',
            'Nome do responsável',
            'CPF do responsável',
        ];
    }
}
