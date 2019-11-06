<?php

namespace App\Services;

use Exception;

class BackupUrlPresigner
{
    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getPresignedUrl() : string
    {
        switch (config('filesystems.cloud')) {
            case 's3':
                return (new S3BackupUrlPresigner($this->url))->getPresignedUrl();
            default:
                throw new Exception('Method BackupUrlPresigner::getPresignedUrl() not implemented for cloud filesystem: '. config('filesystems.cloud'));
        }
    }
}
