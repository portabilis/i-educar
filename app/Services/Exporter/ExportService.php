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
        //obtem o total para ser usado na divisão das jobs de gerar o csv e para a mensagem de notificação
        $this->querySize = $this->export->getExportQuery()->count();
        //exporta a query
        $exporter = new ExporterQueryExport($this->connection, $this->export->model, $this->export->fields, $this->export->filters, $this->querySize);
        //guarda o arquivo no disco em jobs divididas e no final dispara outras jobs
        $exporter->store($this->filename, writerType: $this->fileType)
            ->chain([
                new UpdateUrlExport($this->export, $this->getUrl()),
                new NotifyUserExporter($this->export->user_id, $this->getMessage(), $this->getUrl())
            ]);
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
