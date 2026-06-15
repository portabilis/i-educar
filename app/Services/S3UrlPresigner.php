<?php

namespace App\Services;

use Carbon\Carbon;
use Storage;

class S3UrlPresigner
{
    public function getPresignedUrl(string $url, ?string $filename = null): string
    {
        $key = $this->getKeyFromUrl($url);
        if (empty($key)) {
            return '';
        }

        $options = [];
        if ($filename) {
            $options['ResponseContentDisposition'] = 'inline; filename="' . rawurlencode($filename) . '"';
        }

        return (string) Storage::disk('s3')->temporaryUrl($key, Carbon::now()->addMinutes(5), $options);
    }

    private function getKeyFromUrl(string $url): string
    {
        $path = ltrim((string) parse_url($url, PHP_URL_PATH), '/');

        // path-style (bucket no caminho) -> remove o prefixo do bucket;
        // virtual-hosted (bucket no host) -> o path já é a key
        $bucket = config('filesystems.disks.s3.bucket');
        if ($bucket && str_starts_with($path, $bucket . '/')) {
            $path = substr($path, strlen($bucket) + 1);
        }

        return urldecode(urldecode($path));
    }
}
