<?php

namespace App\Services\Exporter;

use App\Exports\EloquentExporter;
use App\Exports\ExporterQueryExport;
use App\Jobs\NotifyUserExporter;
use App\Jobs\UpdateUrlExport;
use App\Models\Exporter\Export;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;

class ExportService
{
    private string $connection;
    private string $filename;
    private EloquentExporter $exporter;
    private string $fileType = Excel::CSV;
    private int $querySize;

    /**
     * @param Export $export
     */
    public function __construct(
        private Export $export,
    ) {
        $this->setConnection();
        $this->setFilename();
    }

    /**
     * @return void
     *
     */
    public function execute(): void
    {
        $this->querySize = $this->export->getExportQuery()->count();
        $exporter = new ExporterQueryExport($this->connection, $this->export, $this->querySize);
        $success = $exporter->store($this->filename, writerType: $this->fileType);
        if ($success) {
            UpdateUrlExport::dispatch($this->export, $this->getUrl());
            NotifyUserExporter::dispatch($this->export->user_id, $this->getMessage(), $this->getUrl());
        }
    }

    /**
     * @return void
     */
    private function setConnection(): void
    {
        $this->connection = $this->export->getConnectionName();
        DB::setDefaultConnection($this->connection);
    }

    /**
     * @return void
     */
    private function setFilename(): void
    {
        $this->filename = sprintf(
            '%s/csv/%s/%s',
            $this->connection,
            $this->export->hash,
            $this->export->filename
        );
    }

    /**
     * @return void
     */
    private function setExporter(): void
    {
        if (empty($this->exporter)) {
            $this->exporter = new EloquentExporter($this->export);
        }
    }

    /**
     * @return string
     */
    private function getUrl(): string
    {
        return Storage::url($this->filename);
    }

    /**
     * @return string
     */
    private function getMessage(): string
    {
        return "Foram exportados {$this->querySize} registros. Clique aqui para fazer download do arquivo {$this->export->filename}.";
    }
}
