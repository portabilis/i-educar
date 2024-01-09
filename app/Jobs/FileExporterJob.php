<?php

namespace App\Jobs;

use App\Models\FileExport;
use App\Services\FileExportService;
use App\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Throwable;

class FileExporterJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 5;

    public $timeout = 3600;

    public $retryAfter = 5;

    public function __construct(private FileExport $fileExport, private array $args)
    {
        DB::setDefaultConnection($fileExport->getConnectionName());
    }

    public function handle()
    {
        (new FileExportService($this->fileExport, $this->args))->execute();
    }

    public function failed(Throwable $exception)
    {
        (new FileExportService($this->fileExport, $this->args))->failed();
    }

    public function tags(): array
    {
        return [
            $this->fileExport->getConnectionName(),
            'file-export',
        ];
    }
}
