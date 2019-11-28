<?php

namespace App\Services;

use Carbon\Carbon;
use Storage;

class S3UrlPresigner
{
    public function getPresignedUrl(string $url) : string
    {
        return (string) Storage::disk('s3')->temporaryUrl($this->getKeyFromUrl($url), Carbon::now()->addMinutes(5));
    }

    private function getKeyFromUrl(string $url) : string
    {
        $url = preg_replace('/\?.*/', '', $url);
        return implode('/', array_slice(explode('/', $url), 3));
    }
}
