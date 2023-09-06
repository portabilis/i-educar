<?php

namespace App\Services\Exporter;

use App\Exports\ExporterSqlExport;
use App\Models\Exporter\Export;
use App\Models\NotificationType;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;

class ExportService
{
    private string $fileType = Excel::CSV;

    public function __construct(
        private Export $export,
        private ?string $disk = null
    ) {
    }

    /**
     * @param null $disk
     */
    public function execute(): void
    {
        DB::setDefaultConnection($this->export->getConnectionName());

        $exporter = new ExporterSqlExport($this->export->getExportQuery()->toSql());
        $success = $exporter->store($this->getFilename(), $this->disk, $this->fileType);
        if ($success) {
            $url = $this->getUrl();
            $this->export->update(['url' => $url]);
            (new NotificationService())->createByUser(
                $this->export->user_id,
                $this->getMessage(),
                $url,
                NotificationType::EXPORT_STUDENT
            );
        }
    }

    private function getFilename(): string
    {
        return sprintf(
            '%s/csv/%s/%s',
            $this->export->getConnectionName(),
            $this->export->hash,
            $this->export->filename
        );
    }

    private function getMessage(): string
    {
        $count = $this->export->getExportQuery()->count();

        return "Foram exportados {$count} registros. Clique aqui para fazer download do arquivo {$this->export->filename}.";
    }

    private function getUrl(): string
    {
        return Storage::disk($this->disk)->url($this->getFilename());
    }
}
