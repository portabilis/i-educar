<?php

namespace App\Exports;

use App\Models\GuardianType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class StudentsExport implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithMapping
{
    protected $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function map($student): array
    {
        $row = [];
        $fatherName = $student->individual->father_name;
        $motherName = $student->individual->mother_name;
        $guardianName = $student->individual->guardian_name;

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
                $names = [];
                $documents = [];

                if (!is_null($fatherName)) {
                    $names[] = $fatherName;
                }

                if (!is_null($motherName)) {
                    $names[] = $motherName;
                }

                if (!is_null($fatherDocument)) {
                    $documents[] = $fatherDocument;
                }

                if (!is_null($motherDocument)) {
                    $documents[] = $motherDocument;
                }

                $guardianName = join(', ', $names);
                $guardianDocument = join(', ', $documents);
                break;
        }

        $row[] = $student->id;
        $row[] = $student->census->inep_code ?? null;
        $row[] = $student->individual->real_name;
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

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
