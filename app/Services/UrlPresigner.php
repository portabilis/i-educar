<?php

namespace App\Services;

use Exception;

class UrlPresigner
{
    public function getPresignedUrl(string $url) : string
    {
        switch (config('filesystems.cloud')) {
            case 's3':
                return (new S3UrlPresigner())->getPresignedUrl($url);
            default:
                throw new Exception('Method UrlPresigner::getPresignedUrl() not implemented for cloud filesystem: '. config('filesystems.cloud'));
        }
    }
}
