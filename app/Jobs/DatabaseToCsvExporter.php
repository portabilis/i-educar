<?php

namespace App\Jobs;

use App\Exports\EloquentExporter;
use App\Models\Exporter\Export;
use App\Models\NotificationType;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class DatabaseToCsvExporter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    public $timeout = 900;

    /**
     * @var Export
     */
    private $export;

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
     * @return void
     */
    public function handle(NotificationService $notification, DatabaseManager $manager)
    {
        $manager->setDefaultConnection(
            $this->export->getConnectionName()
        );

        $exporter = new EloquentExporter($this->export);

        $filename = $this->transformTenantFilename($this->export);
        $url = $this->transformTenantUrl($filename);

        $exporter->store($filename, null, null, [
            'visibility' => 'public',
        ]);

        $notification->createByUser(
            $this->export->user_id,
            $this->getMessageToNotification($exporter),
            $url,
            NotificationType::EXPORT_STUDENT
        );

        $this->export->url = $url;
        $this->export->save();
    }
}
