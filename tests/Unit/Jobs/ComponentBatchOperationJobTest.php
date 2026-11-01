<?php

use App\Jobs\ComponentBatchOperationJob;
use App\Models\ComponentBatchOperation;
use App\Services\ComponentBatchManagerService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->connection = DB::getDefaultConnection();
    $this->operation = new ComponentBatchOperation(['status_id' => 1]);
    $this->job = new ComponentBatchOperationJob($this->operation, 'escola-alfa');
});

afterEach(function () {
    DB::setDefaultConnection($this->connection);
});

test('switches the connection and executes the operation', function () {
    $this->mock(ComponentBatchManagerService::class)
        ->shouldReceive('execute')->once()->with($this->operation)->andReturn([]);

    $this->job->handle();

    $this->assertSame('escola-alfa', DB::getDefaultConnection());
});

test('marks the operation as failed with the error', function () {
    $this->mock(ComponentBatchManagerService::class)
        ->shouldReceive('failed')->once()->with($this->operation, 'boom');

    $this->job->failed(new RuntimeException('boom'));

    $this->assertSame('escola-alfa', DB::getDefaultConnection());
});

test('tags the job with the connection', function () {
    $this->assertSame(['escola-alfa', 'component-batch-operation'], $this->job->tags());
});
