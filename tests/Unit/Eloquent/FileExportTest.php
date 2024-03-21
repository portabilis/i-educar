<?php

namespace Tests\Unit\Eloquent;

use App\Models\FileExport;
use App\Services\UrlPresigner;
use Carbon\Carbon;
use Tests\EloquentTestCase;

class FileExportTest extends EloquentTestCase
{
    public function testPresignedUrl(): void
    {
        $this->assertEquals((new UrlPresigner)->getPresignedUrl($this->model->url), $this->model->presignedUrl);
    }

    public function testFilename(): void
    {
        $this->assertEquals('Alunos_' . Carbon::now()->format('Y-m-d_H:i'), $this->model->filename);
    }

    public function testSizeFormat(): void
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
}
