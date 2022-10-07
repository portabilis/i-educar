<?php

namespace App\Jobs;

use App\Models\Exporter\Export;
use App\Services\Exporter\ExportService;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DatabaseToCsvExporter implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
     * Execute the job.
     *
     * @param NotificationService $notification
     * @param DatabaseManager     $manager
     *
     * @return void
     */
    public function handle(NotificationService $notification, DatabaseManager $manager)
    {

        $export = new ExportService($this->export, $notification, $manager);

        $export->execute();
    }
}
