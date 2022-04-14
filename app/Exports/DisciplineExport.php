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

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->collection;
    }

    /**
     * @param array $row
     *
     * @return array
     */
    public function map($row): array
    {
        return $row;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return array_keys($this->collection->first());
    }
}
