<?php

namespace App\Jobs;

use App\Exports\EloquentExporter;
use App\Models\Exporter\Export;
use App\Models\NotificationType;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class DatabaseToCsvExporter implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var int
     */
    public $timeout = 1800;

    /**
     * @var Export
     */
    private $export;

    /**
     * @var EloquentExporter
     */
    private $exporter;

    /**
     * Create a new job instance.
     *
     * @param Export $export
     */
    public function __construct(Export $export)
    {
        $this->export = $export;
    }

    /**
     * @return EloquentExporter
     */
    public function getExporter()
    {
        if (empty($this->exporter)) {
            $this->exporter = new EloquentExporter($this->export);
        }

        return $this->exporter;
    }

    /**
     * @param Export $export
     *
     * @return string
     */
    public function transformTenantFilename(Export $export)
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
    public function transformTenantUrl($filename)
    {
        return Storage::url($filename);
    }

    /**
     * @param EloquentExporter $exporter
     *
     * @return string
     */
    public function getMessageToNotification(EloquentExporter $exporter)
    {
        return "Foram exportados {$exporter->getExportCount()} registros. Clique aqui para fazer download do arquivo {$this->export->filename}.";
    }

    /**
     * Execute the job.
     *
     * @param NotificationService $notification
     * @param DatabaseManager     $manager
     *
     * @throws FileNotFoundException
     *
     * @return void
     */
    public function handle(NotificationService $notification, DatabaseManager $manager)
    {
        $manager->setDefaultConnection(
            $sftp = $this->export->getConnectionName()
        );

        $exporter = $this->getExporter();

        $file = $this->export->hash;

        $manager->unprepared(
            "COPY ({$exporter->query()}) TO '/tmp/{$file}' CSV HEADER;"
        );

        Storage::disk()->put(
            $filename = $this->transformTenantFilename($this->export),
            Storage::disk($sftp)->get("/tmp/{$file}")
        );

        Storage::disk($sftp)->delete("/tmp/{$file}");

        $url = $this->transformTenantUrl($filename);

        $notification->createByUser(
            $this->export->user_id,
            $this->getMessageToNotification($exporter),
            $url,
            NotificationType::EXPORT_STUDENT
        );

        $this->export->url = $url;
        $this->export->save();
    }

    public function tags()
    {
        return [
            $this->export->getConnectionName(),
            'csv-export'
        ];
    }
}
