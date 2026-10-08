<?php

namespace Tests\Unit\Eloquent;

use App\Models\Enums\FileExportStatus;
use App\Models\FileExport;
use App\Services\UrlPresigner;
use Carbon\Carbon;
use Tests\EloquentTestCase;

class FileExportTest extends EloquentTestCase
{
    public function test_presigned_url(): void
    {
        $this->assertEquals((new UrlPresigner)->getPresignedUrl($this->model->url), $this->model->presignedUrl);
    }

    public function test_filename(): void
    {
        $this->assertEquals('Alunos_' . Carbon::now()->format('Y-m-d_H:i'), $this->model->filename);
    }

    public function test_size_format(): void
    {
        $units = [
            'B',
            'KB',
            'MB',
            'GB',
            'TB',
        ];
        $bytes = max($this->model->size, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $expected = number_format($bytes / (1024 ** $pow), 2) . ' ' . $units[$pow];

        $this->assertEquals($expected, $this->model->sizeFormat);
    }

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return FileExport::class;
    }

    public function test_status(): void
    {
        $this->model->status_id = FileExportStatus::WAITING->value;
        $this->model->created_at = now();
        $this->assertTrue($this->model->statusIsWaiting());
        $this->assertFalse($this->model->statusIsError());
        $this->assertFalse($this->model->statusIsSuccess());

        // A exportação que espera há mais de cinco minutos é tratada como erro.
        $this->model->created_at = now()->subMinutes(6);
        $this->assertTrue($this->model->statusIsError());

        $this->model->status_id = FileExportStatus::SUCCESS->value;
        $this->assertTrue($this->model->statusIsSuccess());
        $this->assertFalse($this->model->statusIsError());
    }
}
