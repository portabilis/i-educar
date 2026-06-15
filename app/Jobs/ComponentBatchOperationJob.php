<?php

namespace App\Jobs;

use App\Models\ComponentBatchOperation;
use App\Services\ComponentBatchManagerService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ComponentBatchOperationJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public $timeout = 600;

    public function __construct(
        private ComponentBatchOperation $operation,
        private string $databaseConnection,
    ) {}

    public function handle(): void
    {
        DB::setDefaultConnection($this->databaseConnection);

        $service = app(ComponentBatchManagerService::class);
        $service->execute($this->operation);
    }

    public function failed(\Throwable $exception): void
    {
        DB::setDefaultConnection($this->databaseConnection);

        $service = app(ComponentBatchManagerService::class);
        $service->failed(operation: $this->operation, error: $exception->getMessage());
    }

    public function tags(): array
    {
        return [$this->databaseConnection, 'component-batch-operation'];
    }
}
