<?php

namespace App\Exports;

use App\Models\Exporter\Export;

class EloquentExporter
{
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
     * @return string
     */
    public function query()
    {
        return $this->export->getExportQuery()->toSql();
    }

    /**
     * @return int
     */
    public function getExportCount()
    {
        return $this->export->getExportQuery()->count();
    }
}
