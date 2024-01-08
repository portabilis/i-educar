<?php

namespace App\Jobs;

use App\Models\FileExport;
use App\Services\StudentFileExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class StudentFileExporterJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 5;

    public $timeout = 3600;

    public $retryAfter = 5;

    private StudentFileExportService $studentFileExportService;

    public function __construct(private FileExport $studentFileExport, private array $args)
    {
        $this->studentFileExportService = new StudentFileExportService($this->studentFileExport, $this->args);
    }

    public function handle()
    {
        $this->studentFileExportService->execute();
    }

    public function failed(Throwable $exception)
    {
        $this->studentFileExportService->failed();
    }

    public function tags(): array
    {
        return [
            $this->studentFileExport->getConnectionName(),
            'student-file-export',
        ];
    }
}
