<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExporterSqlExport implements FromArray, WithHeadings
{
    use Exportable;

    private array $data;

    private array $headings = [];

    public function __construct(string $sql, bool $withHeader = true)
    {
        $this->data = DB::select($sql);
        if ($withHeader && isset($this->data[0])) {
            $this->headings = array_keys((array) $this->data[0]);
        }
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }
}
