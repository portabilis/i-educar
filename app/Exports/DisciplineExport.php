<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DisciplineExport implements FromCollection, WithHeadingRow, WithHeadings, WithMapping
{
    use Exportable;

    private $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->institution_id,
            $row->knowledge_area_id,
            $row->name,
            $row->abbreviation,
            $row->foundation_type,
            $row->order,
            $row->educacenso_code,
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'institution_id',
            'knowledge_area_id',
            'name',
            'abbreviation',
            'foundation_type',
            'order',
            'educacenso_code',
        ];
    }
}
