<?php

namespace App\Exports;

use App\Models\Exporter\Export;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EloquentExporter implements FromQuery, WithChunkReading, WithHeadings
{
    use Exportable;

    /**
     * @var Export
     */
    private $export;

    /**
     * EloquentExporter constructor.
     *
     * @param Export $export
     */
    public function __construct(Export $export)
    {
        $this->export = $export;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return $this->export->getExportHeading();
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 500;
    }

    /**
     * @return Builder
     */
    public function query()
    {
        return $this->export->getExportQuery();
    }

    /**
     * @return int
     */
    public function getExportCount()
    {
        return $this->export->getExportQuery()->count();
    }
}
