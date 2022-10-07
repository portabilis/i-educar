<?php

namespace App\Services\Exporter;

use App\Exports\EloquentExporter;
use App\Exports\ExporterQueryExport;
use App\Jobs\NotifyUserExporter;
use App\Jobs\UpdateUrlExport;
use App\Models\Exporter\Export;
use App\Services\NotificationService;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;

class ExportService
{
    private string $disk;
    private string $connection;
    private string $filename;
    private EloquentExporter $exporter;
    private string $fileType = Excel::CSV;
    private int $querySize;

    /**
     * @param Export              $export
     * @param NotificationService $notification
     * @param DatabaseManager     $manager
     */
    public function __construct(
        private Export              $export,
        private NotificationService $notification,
        private DatabaseManager     $manager
    ) {
        $this->setExporter();
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
        $exporter = new ExporterQueryExport($this->manager, $this->connection, $this->export->model, $this->export->fields, $this->export->filters, $this->querySize);
        //guarda o arquivo no disco em jobs divididas e no final dispara outras jobs
        $exporter->store($this->filename, $this->disk, $this->fileType)
            ->chain([
                new UpdateUrlExport($this->export, $this->getUrl($this->filename)),
                new NotifyUserExporter($this->export->user_id, $this->getMessage(), $this->getUrl($this->filename))
            ]);
    }

    /**
     * @return void
     */
    private function setConnection(): void
    {
        $this->connection = $this->export->getConnectionName();
        $this->disk = $this->connection;
        $this->manager->setDefaultConnection(
            $this->connection
        );
    }

    /**
     * @return void
     */
    private function setFilename(): void
    {
        $this->filename = $this->transformTenantFilename($this->export);
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
     * @param Export $export
     *
     * @return string
     */
    private function transformTenantFilename(Export $export): string
    {
        return sprintf(
            '%s/csv/%s/%s',
            $export->getConnectionName(),
            $export->hash,
            $export->filename
        );
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    private function getUrl(string $filename): string
    {
        return Storage::url($filename);
    }

    private function tags(): array
    {
        return [
            $this->export->getConnectionName(),
            'csv-export'
        ];
    }

    /**
     * @return string
     */
    private function getMessage(): string
    {
        return "Foram exportados {$this->querySize} registros. Clique aqui para fazer download do arquivo {$this->export->filename}.";
    }
}
