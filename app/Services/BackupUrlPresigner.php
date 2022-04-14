<?php

namespace App\Services;

use Exception;

class BackupUrlPresigner
{
    public function getPresignedUrl(string $url): string
    {
        switch (config('filesystems.cloud')) {
            case 's3':
                return (new S3BackupUrlPresigner())->getPresignedUrl($url);
            default:
                throw new Exception('Method BackupUrlPresigner::getPresignedUrl() not implemented for cloud filesystem: ' . config('filesystems.cloud'));
        }
    }
}
