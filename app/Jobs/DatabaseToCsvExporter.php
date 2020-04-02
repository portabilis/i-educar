<?php

namespace App\Jobs;

use App\Exports\EloquentExporter;
use App\Models\Exporter\Export;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class DatabaseToCsvExporter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Export
     */
    private $export;

    /**
     * @var NotificationService
     */
    private $notification;

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
            '%s/%s/%s',
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
     * @return string
     */
    public function getMessageToNotification()
    {
        return "Sua exportação de dados foi realizada com sucesso. Clique aqui para fazer download do arquivo {$this->export->filename}.";
    }

    /**
     * Execute the job.
     *
     * @param NotificationService $notification
     *
     * @return void
     */
    public function handle(NotificationService $notification)
    {
        $this->notification = $notification;

        $exporter = new EloquentExporter($this->export);

        $filename = $this->transformTenantFilename($this->export);
        $url = $this->transformTenantUrl($filename);

        $exporter->store($filename, null, null, [
            'visibility' => 'public',
        ]);

        $notification->createByUser(
            $this->export->user_id,
            $this->getMessageToNotification(),
            $url,
            1
        );

        $this->export->url = $url;
        $this->export->save();
    }
}
