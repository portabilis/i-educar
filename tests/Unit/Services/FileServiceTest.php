<?php

namespace Tests\Unit\Services;

use App\Models\FileRelation;
use App\Services\FileService;
use App\Services\UrlPresigner;
use Illuminate\Support\Collection;
use Tests\TestCase;

class FileServiceTest extends TestCase
{
    public function test_get_files_returns_empty_collection_when_relation_is_missing()
    {
        $files = (new FileService(new UrlPresigner))->getFiles(null);

        $this->assertInstanceOf(Collection::class, $files);
        $this->assertCount(0, $files);
    }

    public function test_get_files_by_type_returns_empty_collection_when_relation_is_missing()
    {
        $files = (new FileService(new UrlPresigner))->getFiles(null, FileRelation::TYPE_MEDICAL_REPORT);

        $this->assertInstanceOf(Collection::class, $files);
        $this->assertCount(0, $files);
    }
}
