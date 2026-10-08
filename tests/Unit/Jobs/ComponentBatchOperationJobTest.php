<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ComponentBatchOperationJob;
use App\Models\ComponentBatchOperation;
use App\Services\ComponentBatchManagerService;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class ComponentBatchOperationJobTest extends TestCase
{
    private ComponentBatchOperation $operation;

    private ComponentBatchOperationJob $job;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operation = new ComponentBatchOperation(['status_id' => 1]);
        $this->job = new ComponentBatchOperationJob($this->operation, 'escola-alfa');
    }

    public function test_switches_the_connection_and_executes_the_operation(): void
    {
        $this->mock(ComponentBatchManagerService::class)
            ->shouldReceive('execute')->once()->with($this->operation)->andReturn([]);

        $this->job->handle();

        $this->assertSame('escola-alfa', DB::getDefaultConnection());
    }

    public function test_marks_the_operation_as_failed_with_the_error(): void
    {
        $this->mock(ComponentBatchManagerService::class)
            ->shouldReceive('failed')->once()->with($this->operation, 'boom');

        $this->job->failed(new RuntimeException('boom'));

        $this->assertSame('escola-alfa', DB::getDefaultConnection());
    }

    public function test_tags_the_job_with_the_connection(): void
    {
        $this->assertSame(['escola-alfa', 'component-batch-operation'], $this->job->tags());
    }
}
