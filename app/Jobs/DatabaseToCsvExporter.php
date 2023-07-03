<?php

namespace App\Jobs;

use App\Models\Exporter\Export;
use App\Services\Exporter\ExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DatabaseToCsvExporter implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 5;

    public $timeout = 1800;

    public $retryAfter = 5;

    /**
     * @var Export
     */
    private $export;

    /**
     * Create a new job instance.
     */
    public function __construct(Export $export)
    {
        $this->export = $export;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $export = new ExportService($this->export);

        $export->execute();
    }

    public function tags(): array
    {
        return [
            $this->export->getConnectionName(),
            'csv-export',
        ];
    }
}
