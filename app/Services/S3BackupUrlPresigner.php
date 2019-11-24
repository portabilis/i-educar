<?php

namespace App\Services;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Exception;
use Storage;

class S3BackupUrlPresigner
{
    public function getPresignedUrl(string $url) : string
    {
        $s3Client = $this->getClient($url);

        $command = $s3Client->getCommand('GetObject', [
            'Bucket' => $this->getBucketFromUrl($url),
            'Key' => $this->getKeyFromUrl($url),
        ]);

        $request = $s3Client->createPresignedRequest($command, '+10 minutes');

        return (string) $request->getURI();
    }

    private function getClient(string $url) : S3Client
    {
        $clientParameters = [
            'version' => 'latest',
            'region'  => $this->getRegionFromUrl($url),
            'http'    => [
                'connect_timeout' => 5
            ],
        ];

        if (config('filesystems.disks.s3.key') && config('filesystems.disks.s3.secret')) {
            $clientParameters['credentials'] = new Credentials(config('filesystems.disks.s3.key'), config('filesystems.disks.s3.secret'));
        }

        return new S3Client($clientParameters);
    }

    private function getBucketFromUrl(string $url) : string
    {
        return explode('/', $url)[3];
    }

    private function getKeyFromUrl(string $url) : string
    {
        return implode('/', array_slice(explode('/', $url), 4));
    }

    private function getRegionFromUrl(string $url) : string
    {
        return substr(explode('.', explode('//', $url)[1])[0], 3);
    }
}
